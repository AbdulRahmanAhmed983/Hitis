<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;
use App\Http\Traits\DataTrait;
use App\Http\Traits\FinanceTrait;
use App\Http\Traits\StudentTrait;
use App\Http\Traits\UserTrait;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentAffairsController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Exports\ReportsExport;
use Excel;
class AdminController extends Controller
{
    use DataTrait, UserTrait, StudentTrait,FinanceTrait;

    public function addNewUserIndex()
    {
        return view('admin.add_user');
    }

    public function addNewUser(Request $request)
    {
        $rules = [
            'name' => 'required|not_regex:/[#;<>]/u'
                . ($request->role == 'academic_advising' ? '|unique:academic_advisors,name' : ''),
            'username' => 'required|string|max:20|not_regex:/^[RT][0-9]{6}$/u|regex:/^[a-zA-Z0-9_\/@+-]+$/u|
            unique:users,username|unique:deleted_users,username',
            'password' => 'required|string|min:8|max:8|regex:/^[A-Z0-9]{8}$/u',
            'mobile' => 'required|digits:11|regex:/^(01)[0125]/|unique:users,mobile|unique:deleted_users,mobile',
            'email' => 'required|email:rfc,dns|regex:/^[^\x{0621}-\x{064A}٠-٩ ]+$/u|unique:users,email|
            unique:deleted_users,email',
            'role' => 'required|in:admin,student_affairs,finance,academic_advising,control,chairman',
        ];
        $data = $d = $request->validate($rules);
        $data['password'] = Hash::make($data['password']);
        $data['password_status'] = 0;
        $data['created_by'] = auth()->id();
        try {
            DB::transaction(function () use ($data) {
                User::create($data);
                if ($data['role'] == 'academic_advising') {
                    DB::table('academic_advisors')
                        ->insert([
                            'username' => $data['username'],
                            'name' => $data['name'],
                        ]);
                }
            });
            return redirect()->back()->with(['success' => 'تمت إضافة المستخدم بنجاح', 'data' => $d]);
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->withInput()->with('error', 'خطأ في الإتصال');
        }
    }

    public function usersListIndex(Request $request)
    {
        $items_per_pages = 20;
        $users = User::where('role', '!=', 'owner')->where('role', '!=', 'student')
            ->paginate($items_per_pages);
        if (isset($request->search)) {
            $users = User::where('role', '!=', 'owner')->where('role', '!=', 'student')
                ->whereRaw('CONCAT(`name`,"\0",`username`,"\0",`mobile`,"\0",`email`,"\0",`role`) LIKE ?',
                    ['%' . $request->search . '%'])->paginate($items_per_pages);
            $users->appends(['search' => $request->search]);
        }
        $request->validate([
            'page' => 'nullable|integer|between:1,' . $users->lastPage(),
            'search' => 'nullable|string|not_regex:/[#;<>]/u',
        ]);
        $hidden_keys = [6, 7, 8, 9, 10];
        $removed_keys = ['password', 'last_session'];
        $keys = ['الاسم', 'username', 'role', 'رقم الهاتف', 'البريد الالكترونى', 'active', 'last signup', 'created at',
            'created by', 'updated at', 'updated by'];
        return view('admin.users_list')->with([
            'users' => $users,
            'keys' => $keys,
            'removed_keys' => $removed_keys,
            'hidden_keys' => $hidden_keys,
            'search' => $request->search
        ]);
    }

    public function changeUserDataIndex($username)
    {
        $rule = [
            'username' => ['required', 'not_regex:/^[RT][0-9]{6}$/u', 'exists:users,username',
                function ($attribute, $value, $fail) {
                    if (User::where('username', $value)->whereIn('role', ['owner', 'admin'])->exists()
                        and Auth::user()->role != 'owner')
                        $fail('');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return view('admin.change_data')
                ->withErrors("اسم المستخدم $username غير موجود او انه ليس من صلاحياتك");
        }
        $data = User::find($username);
        [$actions, $arr] = $this->getUserPermissions($username);
        foreach ($actions as $action) {
            if (DB::table('has_permissions')->where('username', $data->username)
                ->where('action', $action)->exists()) {
                $arr[$action] = true;
            } else {
                $arr[$action] = false;
            }
        }
        session()->put(['username_change_data' => $username]);
        return view('admin.change_data')->with(['data' => $data->getOriginal(), 'actions' => $actions,
            'have' => $arr]);
    }

    public function changeUserData(Request $request)
    {
        $username = session()->get('username_change_data');
        [$actions, $arr] = $this->getUserPermissions($username);
        session()->forget('username_change_data');
        $rules = [
            'name' => 'required|string|not_regex:/[#;<>]/u',
            'mobile' => ['required', 'digits:11', 'regex:/^(01)[0125]/',
                function ($attribute, $value, $fail) use ($username) {
                    if ($this->checkUniqueUser($username, $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },],
            'email' => ['nullable', 'email:rfc,dns', 'regex:/^[^\x{0621}-\x{064A}٠-٩ ]+$/u',
                function ($attribute, $value, $fail) use ($username) {
                    if ($this->checkUniqueUser($username, $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },],
            'password_status' => 'required|in:0,1,2',
            'password' => 'nullable|string|min:8',

        ];
        $data = $request->validate($rules);
        $data2 = null;
        if (Auth::user()->role == 'owner') {
            $data2 = $request->validate([
                'action' => 'nullable|array|distinct|between:0,' . count($actions),
                'action.*' => 'required|in:' . implode(',', $actions)
            ]);
        }
        $data['updated_by'] = Auth::id();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        try {
            DB::transaction(function () use ($actions, $arr, $data, $data2, $username) {
                User::find($username)->update($data);
                if (User::find($username)->role == 'academic_advising') {
                    DB::table('academic_advisors')->where('username', $username)->update([
                        'name' => $data['name']
                    ]);
                }
                if (is_array($data2) and empty($data2)) {
                    DB::table('has_permissions')->where('username', $username)->delete();
                } else if (isset($data2['action']) and Auth::user()->role == 'owner') {
                    $remove = array_filter($arr, function ($key, $value) use ($data2) {
                        return $key and !in_array($value, $data2['action']);
                    }, ARRAY_FILTER_USE_BOTH);
                    $have = array_filter($arr, function ($key, $value) use ($data2) {
                        return $key;
                    }, ARRAY_FILTER_USE_BOTH);
                    $add = array_filter($data2['action'], function ($value) use ($have) {
                        return !in_array($value, array_keys($have));
                    });
                    DB::table('has_permissions')->where('username', $username)
                        ->whereIn('action', array_keys($remove))->delete();
                    foreach ($add as $value) {
                        DB::table('has_permissions')->insert([
                            'username' => $username,
                            'action' => $value,
                            'added_by' => auth()->id()
                        ]);
                    }
                }
            });
            return redirect()->back()->with('success', 'تم تغيير البيانات بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'خطأ في الإتصال');
        }
    }

    public function deleteUser($username)
    {
        $rule = [
            'username' => ['required', 'not_regex:/^[RT][0-9]{6}$/u', 'exists:users,username',
                function ($attribute, $value, $fail) {
                    if (User::where('username', $value)->whereIn('role', ['owner', 'admin'])
                            ->exists() and Auth::user()->role != 'owner')
                        $fail('');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "اسم المستخدم $username غير موجود او انه ليس من صلاحياتك");
        }
        $user = User::find($username);
        if ($user->role == 'academic_advising') {
            if (Student::where('academic_advisor', $username)->exists() or DB::table('academic_advisors')
                    ->where('username', $username)->where('current_students', '!=', 0)
                    ->exists()) {
                return redirect()->back()->with('error', "لايمكن حذف $user->name لان لديه طلاب");
            }
        }
        try {
            DB::transaction(function () use ($username, $user) {
                $user->updated_by = Auth::id();
                DB::table('deleted_users')->insert($user->getAttributes());
                if ($user->role == 'academic_advising') {
                    DB::table('academic_advisors')->where('username', $username)->delete();
                }
                $user->delete();
            });
            return redirect()->back()->with('success', 'تم حذف المستخدم بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function configIndex()
    {
        $semesters = DB::table('semester')->where('id', 1)->first();
        $first_semester = $semesters->first_semester;
        $second_semester = $semesters->second_semester;
        $summer_semester = $semesters->summer_semester;
        $academic_registration = $semesters->academic_registration;
        $moodle_registration = $this->getData(['moodle_registration'])['moodle_registration'][0];
        $moodle_login = $this->getData(['moodle_login'])['moodle_login'][0];
        $maintenance_mood = $this->getData(['maintenance_mood'])['maintenance_mood'][0];
        $hour_payment['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 1)->first();
        $hour_payment['english'] = (array)DB::table('hour_payment_english')->where('id', 1)->first();
        $hour_payment_remaining['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 3)
            ->first();
        $hour_payment_remaining['english'] = (array)DB::table('hour_payment_english')
            ->where('id', 3)->first();
        $ministerial_payment['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 2)
            ->first();
        $ministerial_payment['english'] = (array)DB::table('hour_payment_english')->where('id', 2)
            ->first();
        $ministerial_payment_remaining['arabic'] = (array)DB::table('hour_payment_arabic')
            ->where('id', 4)->first();
        $ministerial_payment_remaining['english'] = (array)DB::table('hour_payment_english')
            ->where('id', 4)->first();
        $total_payment['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 6)->first();
        $total_payment['english'] = (array)DB::table('hour_payment_english')->where('id', 6)->first();
        $section_numbers['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 5)
            ->first();
        $section_numbers['english'] = (array)DB::table('hour_payment_english')->where('id', 5)
            ->first();
        $registration_hour['arabic'] = (array)DB::table('students_registration_hour')
            ->where('id', 1)->first();
        $registration_hour['english'] = (array)DB::table('students_registration_hour')
            ->where('id', 2)->first();
        $exception_students = DB::table('students_payments_exception')->pluck('student_code')->toArray();
        $military_education[] = $this->getData(['military_education_number'])['military_education_number'][0];
        $military_education[] = $this->getData(['military_education_payment'])['military_education_payment'][0];
        $warning_threshold = $this->getData(['warning_threshold'])['warning_threshold'][0];
        $english_degree = $this->getData(['english_degree'])['english_degree'][0];
        $ministerial_receipt = $this->getData(['ministerial_receipt_start', 'ministerial_receipt_end']);
          //administrative_expenses //
         $administrative_expenses_insurance = (array)DB::table('administrative_expenses')->where('id', 1)->first();
        $administrative_expenses_profile = (array)DB::table('administrative_expenses')->where('id', 2)->first();
        $administrative_expenses_registration_fees = (array)DB::table('administrative_expenses')->where('id', 3)->first();
        $administrative_expenses_card_email = (array)DB::table('administrative_expenses')->where('id', 4)->first();
        $administrative_expenses_renew_card_email = (array)DB::table('administrative_expenses')->where('id', 5)->first();
        $administrative_expenses_military = (array)DB::table('administrative_expenses')->where('id', 6)->first();

        $administrative_expenses_total_first = $administrative_expenses_insurance['first'] + $administrative_expenses_profile['first'] +
        $administrative_expenses_registration_fees['first'] + $administrative_expenses_card_email['first'] +  $administrative_expenses_renew_card_email['first'] +
        $administrative_expenses_military['first'];

        $administrative_expenses_total_second = $administrative_expenses_insurance['second'] + $administrative_expenses_profile['second'] +
        $administrative_expenses_registration_fees['second'] + $administrative_expenses_card_email['second'] +  $administrative_expenses_renew_card_email['second'] +
        $administrative_expenses_military['second'];

        $administrative_expenses_total_third = $administrative_expenses_insurance['third'] + $administrative_expenses_profile['third'] +
        $administrative_expenses_registration_fees['third'] + $administrative_expenses_card_email['third'] +  $administrative_expenses_renew_card_email['third'] +
        $administrative_expenses_military['third'];

        $administrative_expenses_total_fourth = $administrative_expenses_insurance['fourth'] + $administrative_expenses_profile['fourth'] +
        $administrative_expenses_registration_fees['fourth'] + $administrative_expenses_card_email['fourth'] +  $administrative_expenses_renew_card_email['fourth'] +
        $administrative_expenses_military['fourth'];
        $departments = DB::table('departments')->select('id','name')->orderBy('id')->get();
        $extra_fees = DB::table('extra_fees')->get()->toArray();
        $data_key = DB::table('data')->select('data_key')->distinct()->get()->toArray();
       return view('admin.configuration', compact('hour_payment', 'registration_hour',
            'data_key','departments', 'first_semester', 'second_semester', 'summer_semester', 'warning_threshold','maintenance_mood',
            'ministerial_payment', 'total_payment', 'moodle_registration', 'moodle_login', 'hour_payment_remaining',
            'academic_registration', 'ministerial_payment_remaining', 'section_numbers', 'english_degree',
            'ministerial_receipt', 'exception_students', 'military_education','extra_fees'
            ,'administrative_expenses_insurance','administrative_expenses_profile','administrative_expenses_registration_fees'
            ,'administrative_expenses_card_email','administrative_expenses_renew_card_email','administrative_expenses_military',
            'administrative_expenses_total_first','administrative_expenses_total_second','administrative_expenses_total_third','administrative_expenses_total_fourth'));
    }

    public function addData(Request $request)
    {
        $data = $request->validate([
            'data_key' => 'required|regex:/^[a-zA-Z0-9\/-_()\+ ]+$/u',
            'value' => ['required', 'regex:/^[\x{0621}-\x{064A}a-zA-Z0-9٠-٩()\-\+\|. ]+$/u'],
        ]);
        try {
            DB::table('data')->insert($data);
            return redirect()->back()->with('success', 'تم إدخال البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function getDataKeyValues(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'data_key' => 'required|exists:data,data_key',
            ]);
            if ($validator->fails()) {
                return Response()->json(['error' => 'بيانات غير صالحة'], 400);
            }

            return Response(DB::table('data')->select()->orderBy('sorting_index')
                ->where('data_key', $request->data_key)->get()->toArray(), 200);
        }
        abort(404);
    }

    public function updateData(Request $request)
    {
        $data_key = $request->validate([
            'data_key2' => 'required|exists:data,data_key',
        ])['data_key2'];
        $data_table = DB::table('data')->where('data_key', $data_key);
        $rules = [
            'values' => 'required|array|size:' . $data_table->count(),
            'values.*' => ['required', 'distinct', 'regex:/^[\x{0621}-\x{064A}a-zA-Z0-9٠-٩()\-\+\|. ]+$/u'],
            'index' => 'required|array|size:' . $data_table->count(),
            'index.*' => 'required|integer',
        ];
        $data = $request->validate($rules);
        $data['value'] = $data['values'];
        unset($data['values']);
        $data['sorting_index'] = $data['index'];
        unset($data['index']);
        DB::beginTransaction();
        try {
            $data_table->delete();
            for ($i = 0; $i < count($data['value']); $i++) {
                DB::table('data')->insert([
                    'data_key' => $data_key,
                    'value' => $data['value'][$i],
                    'sorting_index' => $data['sorting_index'][$i],
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateSemester(Request $request)
    {
        $rules = [
            'first_semester' => 'required|integer|between:0,2',
            'second_semester' => 'required|integer|between:0,2',
            'summer_semester' => 'required|integer|between:0,2',
            'academic_registration' => 'required|integer|between:0,1',
        ];
        $data = $request->validate($rules);
        $old_data = DB::table('semester')->where('id', 1)->first();
        if (($data['first_semester'] > 0 and $data['second_semester'] == 0 and $data['summer_semester'] == 0) or
            ($data['second_semester'] > 0 and $data['first_semester'] == 0 and $data['summer_semester'] == 0) or
            ($data['summer_semester'] > 0 and $data['second_semester'] == 0 and $data['first_semester'] == 0) or
            ($data['summer_semester'] == 0 and $data['second_semester'] == 0 and $data['first_semester'] == 0)) {
            try {
                DB::transaction(function () use ($old_data, $data) {
                    DB::table('semester')->where('id', 1)->update($data);
                    if (in_array(2, $data) and array_search(2, (array)$old_data) != array_search(2, $data)) {
                        $year = $this->getCurrentYear();
                        $semester = $this->getCurrentSemester();
                        $students = DB::table('registration_semester')->where('payment', 0)
                            ->where('year', $year)->where('semester', $semester)->get()->toArray();
                        foreach ($students as $student) {
                            $con = [
                                'student_code' => $student->student_code,
                                'year' => $student->year,
                                'semester' => $student->semester,
                            ];
                            // DB::table('registration')->where($con)->delete();
                            // DB::table('registration_semester')->where($con)->delete();
                            $payment = DB::table('students_payments')->where($con);
                            if (!empty($payment->first()) and $payment->first()->paid_payments > 0) {
                                DB::table('students_wallet_transaction')->insert([
                                    'student_code' => $student->student_code,
                                    'year' => $student->year,
                                    'semester' => $student->semester,
                                    'amount' => $payment->first()->paid_payments,
                                    'date' => Carbon::now(),
                                    'type' => 'ايداع',
                                    'reason' => 'استرجاع مصاريف دراسية من حذف التسجيل',
                                ]);
                                DB::table('students_wallet')->where('student_code', $student->student_code)
                                    ->increment('amount', $payment->first()->paid_payments);
                            }
                            $payment->delete();
                            DB::table('payment_tickets')->where($con)->where('type', 'دراسية')
                                ->delete();
                            DB::table('students_discounts')->where($con)->where('type', 'دراسية')
                                ->delete();
                        }
                    }
                });
                return redirect()->back()->with('success', 'تم تغير البيانات');
            } catch (Exception $ex) {
                return redirect()->back()->with('error', 'خطأ في الإتصال');
            }
        } else {
            return redirect()->back()->with('error', 'لا يمكن تفعيل أكثر من فصل دراسي في نفس الوقت');
        }
    }

    public function moodleSetting(Request $request)
    {
        $rules = [
            'moodle_registration' => 'required|integer|between:0,1',
            'moodle_login' => 'required|integer|between:0,1',
        ];
        $data = $request->validate($rules);
        try {
            DB::transaction(function () use ($data) {
                DB::table('data')->where('data_key', 'moodle_registration')->update([
                    'value' => $data['moodle_registration']
                ]);
                DB::table('data')->where('data_key', 'moodle_login')->update([
                    'value' => $data['moodle_login']
                ]);
            });
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function getCourses(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'data' => 'required|array|size:3',
                'data.0' => 'required|in:E,R',
                'data.1' => 'required|integer|between:1,8',
                'data.2' => 'required|integer|between:0,1',
                'data.3' => 'required|integer',
            ]);
            if (!$validator->fails()) {
                return Response()->json(['error' => 'بيانات غير صالحة'], 400);
            }
            try {
                $courses = $this->getAllCourses()[$request->data[0]][$request->data[1]][$request->data[2]][$request->data[3]];
                return Response($courses, 200);
            } catch (Exception $e) {
                return Response()->json(['error' => 'خطأ في الإتصال'], 400);
            }
        }
        abort(404);
    }

    public function updateCourses(Request $request)
    {
        $rules = [
            'type' => 'required|in:E,R',
            'semester' => 'required|integer|between:1,8',
            'elective' => 'required|integer|between:0,1',
            'departments_id' => 'required|integer',
        ];
        $data = $request->validate($rules);
        $courses = $this->getAllCourses()[$request->type][$request->semester][$request->elective][$request->departments_id];
        $count = count($courses);
        $rules = [
            'code' => 'required|array|size:' . $count,
            'code.*' => 'required',
            'is_selected' => 'nullable|array|between:0,' . $count,
            'is_selected.*' => 'required|in:1',
            'hours' => 'required|array|size:' . $count,
            'hours.*' => 'required|integer|between:1,5',
            'name' => 'required|array|size:' . $count,
            'name.*' => 'required|regex:/^[\x{0621}-\x{064A}a-zA-Z0-9٠-٩()\- ]+$/u',
        ];
        $data = array_merge($data, $request->validate($rules));
        DB::beginTransaction();
        try {
            for ($i = 0; $i < $count; $i++) {
                $update_data = [
                    'type' => $data['type'],
                    'departments_id' => $data['departments_id'],
                    'semester' => $data['semester'],
                    'elective' => $data['elective'],
                    'code' => $data['code'][$i],
                    'is_selected' => (isset($data['is_selected'][$i]) ? 1 : 0),
                     'hours' => $data['hours'][$i],
                    'name' => $data['name'][$i],
                ];
                DB::table('courses')->where('code', $courses[$i]['code'])
                    ->where('type', $courses[$i]['type'])->where('departments_id', $courses[$i]['departments_id'])
                    ->update($update_data);
            }
            DB::commit();
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function addCourses(Request $request)
    {
        $count = count(max($request->all()));
        $rules = [
            'course_code' => 'required|array|size:' . $count,
            'course_code.*' => 'required|string|max:7|not_regex:/[#;<>]/u',
            'course_type' => 'required|array|size:' . $count,
            'course_type.*' => 'required|in:R,E',
            'course_name' => 'required|array|size:' . $count,
            'course_name.*' => 'required|string|max:100|not_regex:/[#;<>]/u',
            'course_semester' => 'required|array|size:' . $count,
            'course_semester.*' => 'required|integer|between:1,8',
            'course_hours' => 'required|array|size:' . $count,
            'course_hours.*' => 'required|integer|between:1,6',
            'course_elective' => 'required|array|size:' . $count,
            'course_elective.*' => 'required|integer|between:0,1',
            'departments_id' => ['required', 'array',
            function ($attribute, $value, $fail) use ($request) {
                $departments_Ar = ['ترميم الأثار والمقتنيات الفنية غيرالعضوية',' الأثار والمقتنيات الفنية العضوية'];
                $departments_En = ['Marketing and E-Commerce','Accounting and Review','Business information systems'];
                if ($request->course_type[0] == 'T' && in_array($value, $departments_Ar)){
                    $fail('يوجد خطأ في اختيار التخصص او الشعبة ');
                }
                elseif ($request->course_type[0] == 'R' && in_array($value, $departments_En)){
                    $fail('يوجد خطأ في اختيار التخصص او الشعبة ');
                }

            }],
        ];
        $data = $request->validate($rules);
        try {
            $not_added = [];
            DB::transaction(function () use (&$not_added, $data, $count) {
                for ($i = 0; $i < $count; $i++) {
                    $full_code = $data['course_type'][$i] . $data['course_code'][$i];
                    // if (DB::table('courses')->where(compact('full_code'))->exists()) {
                    //     $not_added[] = "كود المادة $full_code مكرر";
                    // } else {
                        $insert_data = [
                            'type' => $data['course_type'][$i],
                            'code' => $data['course_code'][$i],
                            'name' => $data['course_name'][$i],
                            'semester' => $data['course_semester'][$i],
                            'hours' => $data['course_hours'][$i],
                            'elective' => $data['course_elective'][$i],
                            'departments_id' => $data['departments_id'][$i],
                        ];
                        DB::table('courses')->insert($insert_data);
                    //}
                }
            });
            return redirect()->back()->with('success', 'تم تغير البيانات')->withErrors($not_added);
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updatePayment($type, Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            'summer_payment' => 'required|numeric|min:0',
            'type' => 'required|string|in:arabic,english'
        ];
        $request->merge(['type' => $type]);
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            if ($type == 'arabic') {
                DB::table('hour_payment_arabic')->where('id', 1)->update($data);
            } else {
                DB::table('hour_payment_english')->where('id', 1)->update($data);
            }
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updatePaymentRemaining($type, Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            'type' => 'required|string|in:arabic,english'
        ];
        $request->merge(['type' => $type]);
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            if ($type == 'arabic') {
                DB::table('hour_payment_arabic')->where('id', 3)->update($data);
            } else {
                DB::table('hour_payment_english')->where('id', 3)->update($data);
            }
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateMinisterialPayment($type, Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            'summer_payment' => 'required|numeric|min:0',
            'type' => 'required|string|in:arabic,english'
        ];
        $request->merge(['type' => $type]);
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            if ($type == 'arabic') {
                DB::table('hour_payment_arabic')->where('id', 2)->update($data);
            } else {
                DB::table('hour_payment_english')->where('id', 2)->update($data);
            }
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateMinisterialPaymentRemaining($type, Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            'summer_payment' => 'required|numeric|min:0',
            'type' => 'required|string|in:arabic,english'
        ];
        $request->merge(['type' => $type]);
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            if ($type == 'arabic') {
                DB::table('hour_payment_arabic')->where('id', 4)->update($data);
            } else {
                DB::table('hour_payment_english')->where('id', 4)->update($data);
            }
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateTotalPayment($type, Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            'summer_payment' => 'required|numeric|min:0',
            'type' => 'required|string|in:arabic,english'
        ];
        $request->merge(['type' => $type]);
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            if ($type == 'arabic') {
                DB::table('hour_payment_arabic')->where('id', 6)->update($data);
            } else {
                DB::table('hour_payment_english')->where('id', 6)->update($data);
            }
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateTotalPaymentException(Request $request)
    {
        $data = $request->validate([
            'usernames' => 'nullable|array',
            'usernames.*' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
        ]);
        try {
            DB::transaction(function () use ($data) {
                DB::table('students_payments_exception')->delete();
                if (!empty($data['usernames'])) {
                    foreach ($data['usernames'] as $datum) {
                        DB::table('students_payments_exception')->insert(['student_code' => $datum]);
                    }
                }
            });
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateMilitaryEducation(Request $request)
    {
        $data = $request->validate([
            'military_education_number' => 'nullable|string|max:50',
            'military_education_payment' => 'required|numeric|between:0,5000',
        ]);
        $data['military_education_number'] = ($data['military_education_number'] ?: '');
        try {
            DB::table('data')->where('data_key', 'military_education_number')
                ->update(['value' => $data['military_education_number']]);
            DB::table('data')->where('data_key', 'military_education_payment')
                ->update(['value' => $data['military_education_payment']]);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateRegistrationHour($type, Request $request)
    {
        $rule = [
            'study_group_1' => 'required|integer|min:1',
            'study_group_2' => 'required|integer|min:1',
            'study_group_3' => 'required|integer|min:1',
            'study_group_4' => 'required|integer|min:1',
            'summer' => 'required|integer|min:1',
            'type' => 'required|string|in:arabic,english'
        ];
        $request->merge(['type' => $type]);
        $data = $request->validate($rule);
        unset($data['type']);
        try {
            if ($type == 'arabic') {
                DB::table('students_registration_hour')->where('id', 1)->update($data);
            } else {
                DB::table('students_registration_hour')->where('id', 2)->update($data);
            }
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateSectionNumber($type, Request $request)
    {
        $rule = [
            'first' => 'required|integer|min:1',
            'second' => 'required|integer|min:1',
            'third' => 'required|integer|min:1',
            'fourth' => 'required|integer|min:1',
            'summer' => 'nullable|integer|size:0',
            'action' => 'required|in:save,reset',
            'type' => 'required|string|in:arabic,english'
        ];
        $request->merge(['type' => $type]);
        $data = $request->validate($rule);
        if ($data['action'] == 'save') {
            unset($data['action'], $data['type']);
            try {
                if ($type == 'arabic') {
                    DB::table('hour_payment_arabic')->where('id', 5)->update($data);
                } else {
                    DB::table('hour_payment_english')->where('id', 5)->update($data);
                }
                return redirect()->back()->with('success', 'تم تغير البيانات');
            } catch (Exception $ex) {
                return redirect()->back()->with('error', 'خطأ في الإتصال');
            }
        } else {
            set_time_limit(0);
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            unset($data['action'], $data['type']);
            $section_size = array_combine(['الاولي', 'الثانية', 'الثالثة', 'الرابعة'], array_values($data));
            $students = Student::join('registration_semester', 'students.username', '=',
                'registration_semester.student_code')
                ->where(['payment' => 1, 'registration_semester.year' => $year,
                    'registration_semester.semester' => $semester,
                    'specialization' => ($type == 'arabic') ? 'ترميم الاثار و المقتنيات الفنية' : 'سياجة'])
                ->join('payment_tickets', function ($join) {
                    $join->on('payment_tickets.student_code', '=', 'students.username')
                        ->on('payment_tickets.year', 'registration_semester.year')
                        ->on('payment_tickets.semester', 'registration_semester.semester')
                        ->where('type', 'دراسية');
                })->orderBy('confirmed_at')->get()->groupBy(['specialization', 'study_group'])->toArray();
            try {
                DB::transaction(function () use ($type, $data, $section_size, $semester, $year, $students) {
                    if ($type == 'arabic') {
                        DB::table('hour_payment_arabic')->where('id', 5)->update($data);
                    } else {
                        DB::table('hour_payment_english')->where('id', 5)->update($data);
                    }
                    foreach ($students as $specialization => $study_groups) {
                        $sections[$specialization] = [];
                        foreach ($study_groups as $study_group => $values) {
                            $sections[$specialization][$study_group]['section'] = 1;
                            $sections[$specialization][$study_group]['count'] = 0;
                            foreach ($values as $student) {
                                $data1 = [
                                    'student_code' => $student['username'],
                                    'year' => $year,
                                    'semester' => $semester,
                                ];
                                $data2 = [
                                    'section_number' => ($student['studying_status'] == 'مستجد') ?
                                        $sections[$specialization][$study_group]['section'] : 'سكشن الباقون',
                                    'specialization' => $student['specialization'],
                                    'study_group' => $student['study_group'],
                                ];
                                DB::table('section_number')->updateOrInsert($data1, $data2);
                                if ($student['studying_status'] == 'مستجد') {
                                    $sections[$specialization][$study_group]['count']++;
                                }
                                if ($sections[$specialization][$study_group]['count'] >= $section_size[$study_group]) {
                                    $sections[$specialization][$study_group]['count'] = 0;
                                    $sections[$specialization][$study_group]['section']++;
                                }
                            }
                        }
                    }
                });
                return redirect()->back()->with('success', 'تم تغير البيانات');
            } catch (Exception $ex) {
                return redirect()->back()->with('error', 'خطأ في الإتصال');
            }
        }
    }

    public function updateEnglishDegree(Request $request)
    {
        $rule = [
            'english_degree' => 'required|integer|between:25,50',
        ];
        $data = $request->validate($rule);
        try {
            DB::table('data')->where('data_key', 'english_degree')->update([
                'value' => $data['english_degree']
            ]);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateMinisterialReceiptNumber(Request $request)
    {
        $rule = [
            'ministerial_receipt_start' => 'required|integer|lt:ministerial_receipt_end',
            'ministerial_receipt_end' => 'required|integer|gt:ministerial_receipt_start',
        ];
        $data = $request->validate($rule);
        try {
            DB::transaction(function () use ($data) {
                DB::table('data')->where('data_key', 'ministerial_receipt_start')->update([
                    'value' => $data['ministerial_receipt_start']
                ]);
                DB::table('data')->where('data_key', 'ministerial_receipt_end')->update([
                    'value' => $data['ministerial_receipt_end']
                ]);
            });
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function academicIndex(Request $request)
    {
        $items_per_pages = 50;
        $advisors = DB::table('academic_advisors')->paginate($items_per_pages);
        if (isset($request->search)) {
            $advisors = DB::table('academic_advisors')
                ->whereRaw('CONCAT(`name`,"\0",`username`) LIKE ?', ['%' . $request->search . '%'])
                ->paginate($items_per_pages);
            $advisors->appends(['search' => $request->search]);
        }
        $advisors->getCollection()->transform(function ($value) {
            $value->current_students = DB::table('students')->where('academic_advisor', $value->username)
                ->count();
            return $value;
        });
        $request->validate([
            'page' => 'nullable|integer|between:1,' . $advisors->lastPage(),
            'search' => 'nullable|string|not_regex:/[#;<>]/u',
        ]);
        $hidden_keys = [];
        $removed_keys = [];
        $keys = ['الاسم', 'username', 'الفرقة', 'التخصص', 'الشعبة','حالة الطلاب', 'الحد الأقصى لعدد الطلاب', 'عدد الطلاب الحالي'];
        $students = DB::table('students')->select(['name', 'departments_id','specialization', 'study_group', 'studying_status',
            'academic_advisor'])->orderBy('departments_id')->orderBy('specialization')->orderBy('study_group')
            ->orderBy('studying_status')->orderBy('name')->get()->transform(function ($value) {
                $value->studying_status = ($value->studying_status == 'من الخارج') ? 'باقي' : $value->studying_status;
                $value->has_advisor = (!is_null($value->academic_advisor) ? 'لديه مرشد أكاديمي' :
                    'ليس لديه مرشد أكاديمي');
                    $value->departments_id = DB::table('departments')->select('id', 'name')->where('id', '=', $value->departments_id)->pluck('name')[0];
                return $value;
            })->groupBy(['specialization', 'departments_id','study_group', 'studying_status', 'has_advisor'])->toArray();

        $counter = [];
        $col_counter = [];
        foreach ($students as $specialization => $array) {
            uksort($array, function ($a, $b) {
                $sort = DB::table('departments')->pluck('id', 'name')->toArray();
                return $sort[$a] - $sort[$b];
            });
            $col_counter[$specialization]['col'] = 0;
            foreach ($array as $department => $groups) {
                uksort($groups, function ($a, $b) {
                    $sort = DB::table('data')->where('data_key', 'study_group')->pluck('sorting_index', 'value')->toArray();
                    return $sort[$a] - $sort[$b];
                });
                $col_counter[$specialization][$department]['col'] = 0;
                foreach ($groups as $study_group => $group) {
                    $col_counter[$specialization][$department][$study_group]['col'] = 0;

                    foreach ($group as $studying_status => $has_advisors) {
                        $col_counter[$specialization][$department][$study_group]['col'] += count($has_advisors);

                        foreach ($has_advisors as $has_advisor => $student) {
                            $counter[$specialization][$department][$study_group][$studying_status][$has_advisor] = count($student);
                        }
                    }
                    $col_counter[$specialization][$department]['col'] += $col_counter[$specialization][$department][$study_group]['col'];
                }
                $col_counter[$specialization]['col'] += $col_counter[$specialization][$department][$study_group]['col'];
            }
        }
        return view('admin.academic_list')->with([
            'advisors' => $advisors,
            'keys' => $keys,
            'removed_keys' => $removed_keys,
            'hidden_keys' => $hidden_keys,
            'search' => $request->search,
            'counter' => $counter,
            'col_counter' => $col_counter,
        ]);
    }

    public function editAcademicIndex($username)
    {
        $rule = [
            'username' => 'required|exists:users,username|exists:academic_advisors,username',
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "اسم المستخدم $username غير موجود");
        }
        $advisor = (array)DB::table('academic_advisors')->where('username', $username)->first();
        $students = DB::table('students')->select(['specialization','departments_id', 'study_group', 'studying_status', 'name'])
            ->where('academic_advisor', $advisor['username']);
        $advisor['current_students'] = $students->count();
        $students = $students->orderBy('specialization')->orderBy('departments_id')->orderBy('study_group')
            ->orderBy('studying_status')->orderBy('name')->get()->transform(function ($value) {
                $value->studying_status = ($value->studying_status == 'من الخارج') ? 'باقي' : $value->studying_status;
                $value->departments_id = DB::table('departments')->select('id', 'name')->where('id', '=', $value->departments_id)->pluck('name')[0];
                return $value;
            })->groupBy(['specialization', 'departments_id','study_group', 'studying_status'])->toArray();
        $counter = [];
        $col_counter = [];

        foreach ($students as $specialization => $array) {
            uksort($array, function ($a, $b) {
               $sort = DB::table('departments')->pluck('id', 'name')->toArray();
               return $sort[$a] - $sort[$b];
            });
            $col_counter[$specialization]['col'] = 0;
             foreach ($array as $department => $groups) {
                uksort($groups, function ($a, $b) {
                    $sort = DB::table('data')->where('data_key', 'study_group')->pluck('sorting_index', 'value')->toArray();
                    return $sort[$a] - $sort[$b];
                });
                $col_counter[$specialization][$department]['col'] = 0;
           foreach ($groups as $study_group => $group) {
                $col_counter[$specialization][$department]['col'] += $col_counter[$specialization][$department][$study_group]['col'] =
                    count($group);
                foreach ($group as $studying_status => $student) {
                    $counter[$specialization][$department][$study_group][$studying_status] = count($student);
                }
            }
        }
    }

        $data = [];
        if ($advisor['study_group'] != 'all') {
            $data[] = ['study_group', $advisor['study_group']];
        }
        if ($advisor['specialization'] != 'all') {
            $data[] = ['specialization', $advisor['specialization']];
        }
        // if ($advisor['departments_id'] != 'all') {
        //     $data[] = ['departments_id', $advisor['departments_id']];
        // }
        if ($advisor['studying_status'] != 'all') {
            if ($advisor['studying_status'] == 'مستجد') {
                $data[] = ['studying_status', $advisor['studying_status']];
            } else {
                $data[] = ['studying_status', '!=', 'مستجد'];
            }
        }
        $students = DB::table('students')->select(['name', 'specialization','departments_id', 'study_group', 'studying_status',
            'academic_advisor'])->whereNull('academic_advisor')->where($data)->orderBy('specialization')->orderBy('departments_id')
            ->orderBy('study_group')->orderBy('studying_status')->orderBy('name')->get()
            ->transform(function ($value) {
                $value->studying_status = ($value->studying_status == 'من الخارج') ? 'باقي' : $value->studying_status;
                $value->departments_id = DB::table('departments')->select('id', 'name')->where('id', '=', $value->departments_id)->pluck('name')[0];
                return $value;
            })->groupBy(['specialization', 'departments_id','study_group', 'studying_status'])->toArray();
        $counter2 = [];
        $col_counter2 = [];

        foreach ($students as $specialization => $array) {
            $col_counter2[$specialization]['col'] = 0;
        foreach ($array as $departments => $department) {

                foreach ($department as $study_group => $group) {
                    $col_counter2[$specialization]['col'] += $col_counter2[$specialization][$study_group]['col'] =
                        count($group);
                        foreach ($group as $studying_status => $student) {
                            $counter2[$specialization][$study_group][$studying_status] = count($student);
                        }
                    }
            }
        }

        $data_arr = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id'], false);
        $departments_all = DB::table('departments')->pluck( 'name');
        return view('admin.academic_numbers', compact('advisor', 'data_arr', 'counter','departments_all',
            'col_counter', 'counter2', 'col_counter2'));
    }

    public function removeStudentsAcademic($advisor, Request $request)
    {
        $validator = Validator::make(['advisor' => $advisor],
            ['advisor' => 'required|exists:users,username|exists:academic_advisors,username']);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "اسم المستخدم $advisor غير موجود");
        }
        $advisor = (array)DB::table('academic_advisors')->where('username', $advisor)->first();
        $students = DB::table('students')->distinct()->where('academic_advisor', $advisor['username'])
            ->select(['specialization', 'study_group', 'studying_status'])->get()->transform(function ($value) {
                $value->studying_status = ($value->studying_status == 'من الخارج') ? 'باقي' : $value->studying_status;
                return $value;
            })->toArray();
        $data = $request->validate([
            'specialization' => 'required|in:' . implode(',', array_unique(array_column($students,
                    'specialization'))),
            // 'study_group' => 'required|in:' . implode(',', array_unique(array_column($students,
            //         'study_group'))),
            // 'studying_status' => 'required|in:' . implode(',', array_unique(array_column($students,
            //         'studying_status'))),
            'number' => 'required|numeric|between:1,4000'
        ]);
        $num = $data['number'];
        unset($data['number']);
        try {
            DB::table('students')->where('academic_advisor', $advisor['username'])->where($data)
                ->limit($num)->orderBy('name')->update(['academic_advisor' => null]);
            return redirect()->back()->with('success', 'تم حذف ' . $num . ' طلاب');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function addStudentsAcademic($advisor, Request $request)
    {
        $validator = Validator::make(['advisor' => $advisor],
            ['advisor' => 'required|exists:users,username|exists:academic_advisors,username']);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "اسم المستخدم $advisor غير موجود");
        }
        $advisor = (array)DB::table('academic_advisors')->where('username', $advisor)->first();
        $advisor['current_students'] = DB::table('students')->select(['specialization', 'study_group', 'studying_status', 'name'])
            ->where('academic_advisor', $advisor['username'])->count();
        $data_arr = $this->getDistinctValues('students', ['study_group', 'studying_status', 'specialization'],
            false);
        $data_filter['specialization'] = ($advisor['specialization'] == 'all') ?
            implode(',', $data_arr['specialization']) : $advisor['specialization'];
        $data_filter['study_group'] = ($advisor['study_group'] == 'all') ?
            implode(',', $data_arr['study_group']) : $advisor['study_group'];
        $data_filter['studying_status'] = ($advisor['studying_status'] == 'all') ?
            implode(',', $data_arr['studying_status']) : $advisor['studying_status'];
        $data = $request->validate([
            'specialization' => 'required|in:' . $data_filter['specialization'],
            'study_group' => 'required|in:' . $data_filter['study_group'],
            'studying_status' => 'required|in:' . $data_filter['studying_status'],
            'number' => 'required|numeric|between:1,' . ($advisor['max_students'] - $advisor['current_students'])
        ]);
        $num = $data['number'];
        unset($data['number']);
        try {
            DB::table('students')->whereNull('academic_advisor')->where($data)
                ->limit($num)->orderBy('name')->update(['academic_advisor' => $advisor['username']]);
            return redirect()->back()->with('success', 'تم أضافة ' . $num . ' طلاب');
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateAcademicNumbers($username, Request $request)
    {

        $rule = [
            'username' => 'required|exists:users,username|exists:academic_advisors,username',
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "اسم المستخدم $username غير موجود");
        }
        $current_students = DB::table('students')->where('academic_advisor', $username)
            ->count();
        $data_arr = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $rule = [
            'max_students' => 'required|integer|min:' . $current_students,
            'study_group' => 'required|string|in:all,' . implode(',', $data_arr['study_group']),
            'specialization' => 'required|string|in:all,' . implode(',', $data_arr['specialization']),
            'departments_id' => 'required|string',
            'studying_status' => 'required|string|in:all,مستجد,باقي',
        ];
        $data = $request->validate($rule);
        if ($data['study_group'] != 'all') {
            $flag = DB::table('students')->where('academic_advisor', $username)
                ->where('study_group', '!=', $data['study_group'])->exists();
            if ($flag) {
                return redirect()->back()->with('error', 'يجب ان يكون كل الطلبة فى الفرقة ' . $data['study_group']);
            }
        }
        if ($data['specialization'] != 'all') {
            $flag = DB::table('students')->where('academic_advisor', $username)
                ->where('specialization', '!=', $data['specialization'])->exists();

            if ($flag) {

                return redirect()->back()->with('error', 'يجب ان يكون كل الطلبة فى التخصص ' . $data['specialization']);
            }
        }
        if ($data['departments_id'] != 'all') {
            $flag = DB::table('students')->where('academic_advisor', $username)
                ->where('departments_id', '!=', $data['departments_id'])->exists();

            if ($flag) {

                return redirect()->back()->with('error', 'يجب ان يكون كل الطلبة فى الشعبة ' . $data['departments_id']);
            }
        }
        if ($data['studying_status'] != 'all') {
            if ($data['studying_status'] == 'مستجد') {
                $flag = DB::table('students')->where('academic_advisor', $username)
                    ->where('studying_status', '!=', $data['study_group'])->exists();
                if ($flag) {
                    return redirect()->back()->with('error', 'يجب حذف الطلبة الباقيين اولاً');
                }
            } else {
                $flag = DB::table('students')->where('academic_advisor', $username)
                    ->where('studying_status', 'مستجد')->exists();
                if ($flag) {
                    return redirect()->back()->with('error', 'يجب حذف الطلبة المستجدين اولاً');
                }
            }
        }
        try {
            DB::table('academic_advisors')->where('username', $username)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function showAcademicStudent($username, Request $request)
    {
        $rule = [
            'username' => 'required|exists:users,username|exists:academic_advisors,username',
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->route('academic.list')->with('error', "اسم المستخدم $username غير موجود");
        }
        $items_per_pages = 50;
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $sort = DB::table('registration_semester')->where('year', $year)
            ->where('semester', $semester)
            ->join('students', 'students.username', '=', 'registration_semester.student_code')
            ->where('academic_advisor', $username)
            ->select(['student_code', 'guidance'])
            ->orderBy('guidance')->orderBy('student_code')->get()->pluck('student_code')->toArray();
        $orderedIds = implode('\',\'', $sort);
        $students = Student::select('students.name', 'students.username', 'cgpa', 'earned_hours', 'total_hours'
            , 'studying_status', 'student_classification', 'specialization', 'study_group')
            ->where('academic_advisor', $username)
            ->orderByRaw(DB::raw('FIELD(username,\'' . $orderedIds . '\') DESC'))
            ->orderByRaw('SUBSTRING(username, 2, 6)')->paginate($items_per_pages);
        if (isset($request->search)) {
            $students = Student::select('students.name', 'students.username', 'cgpa', 'earned_hours', 'total_hours'
                , 'studying_status', 'student_classification', 'specialization', 'study_group')
                ->where('academic_advisor', $username)
                ->whereRaw('CONCAT(`name`,"\0",`username`,"\0",`specialization`,"\0",`study_group`,"\0"
                ,`studying_status`) LIKE ?', ['%' . $request->search . '%'])
                ->orderByRaw(DB::raw('FIELD(username,\'' . $orderedIds . '\') DESC'))
                ->orderByRaw('SUBSTRING(username, 2, 6)')->paginate($items_per_pages);
            $students->appends(['search' => $request->search]);
        }
        $request->validate([
            'page' => 'nullable|integer|between:1,' . $students->lastPage(),
            'search' => 'nullable|string|not_regex:/[#;<>]/u',
        ]);
        $hidden_keys = [];
        $removed_keys = [];
        $keys = ['الاسم', 'Code', 'المعدل التراكمي للدرجات', 'الساعات المكتسبة', 'إجمالي الساعات المسجلة',
            'الحالة الدراسية', 'تصنيف الطلاب', 'التخصص', 'الفرقة الدراسية'];
        $status = [];
        foreach ($students->pluck('username')->toArray() as $student) {
            $status[$student] = $this->checkGuidance($student);
        }
        return view('admin.students_list')->with([
            'students' => $students,
            'keys' => $keys,
            'primaryKey' => 'username',
            'removed_keys' => $removed_keys,
            'hidden_keys' => $hidden_keys,
            'search' => $request->search,
            'status' => $status,
            'academic_name' => User::find($username)->name,
        ]);
    }

    public function studentStatus(Request $request)
    {
        $rules = [
            'username' => 'nullable|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:users,username|
            exists:students,username',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('student.status.admin')->withErrors($validator)->withInput();
        } else {
            if (!is_null($request->username)) {
                [$registrations, $grades, $trans_courses] = $this->getStudentRegistrationStatus($request->username);
                [$payment, $semester_payment, $other_payment, $total_other_payment, $discount, $total_discount,
                    $year_semester] = $this->getStudentFinanceStatus($request->username);
                $student[] = $this->getStudentInfo($request->username)['name'];
                $student[] = $request->username;
                $seating_numbers = DB::table('seating_numbers')->where('student_code', $request->username)
                    ->get()->groupBy(['year'])->toArray();
                return view('admin.student_status',
                    compact('payment', 'semester_payment', 'other_payment', 'total_other_payment',
                        'discount', 'total_discount', 'year_semester', 'grades', 'registrations', 'student',
                        'trans_courses', 'seating_numbers'));
            }
            return view('admin.student_status');
        }
    }

    public function changeWarningThreshold(Request $request)
    {
        $data = $request->validate([
            'warning_threshold' => 'required|numeric|between:2,4'
        ]);
        $warning_threshold = round($data['warning_threshold'], 2);
        try {
            DB::table('data')->where('data_key', 'warning_threshold')
                ->update(['value' => $warning_threshold]);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function changeStudentLevel()
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $checks = $this->checkStatusAction($year, $semester);
        if ($checks['action_exist']) {
            return redirect()->back()->with('error', 'تم تفعيل هذا الترم من قبل');
        }
        if ($checks['grade_finished']) {
            return redirect()->back()->with('error', 'مازال هناك نتائج مواد غير مكتملة');
        }
        try {
            set_time_limit(0);
            echo "<script>alert('برجاء الانتظار حتى تنتهى العملية');</script>";
            $filename = "backup-level-update-" . Carbon::now()->format('H:i:s_d-m-Y') . ".sql";
            $command = "mysqldump --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') .
                " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . " > " . storage_path() .
                "/app/backup/" . $filename;
            exec($command);
            $next_year = $this->getNextYear();
            $current_students = Student::whereNotIn('student_classification', ['سحب ملف', 'خريجين', 'مفصولين'])
                ->join('registration_years', function ($join) use ($year) {
                    $join->on('registration_years.student_code', '=', 'students.username')
                        ->where('registration_years.year', $year);
                })->select('students.*')->get()->toArray();
            $warning_threshold = (float)$this->getData(['warning_threshold'])['warning_threshold'][0];
            if (in_array($semester, ['ترم ثاني', 'ترم أول'])) {
                DB::transaction(function () use ($semester, $year, $current_students, $warning_threshold) {
                    foreach ($current_students as $student) {
                        $restricted = !DB::table('students_excuses')
                            ->where('student_code', $student['username'])->where('year', $year)
                            ->where(function ($query) use ($semester) {
                                $query->orWhere(function ($query) {
                                    $query->where('type', 'وقف قيد')
                                        ->where('semester', 'year');
                                })->orWhere(function ($query) use ($semester) {
                                    $query->where('type', 'عذر عن ترم')
                                        ->where('semester', $semester);
                                });
                            })->exists();
                        $previous_semesters = DB::table('students_semesters')
                            ->where('student_code', $student['username'])
                            ->whereRaw('CONCAT(year,\'-\',semester) != ?', [$year . '-' . $semester])->count();
                        $warning = 0;
                        if ($restricted and $previous_semesters >= 1) {
                            if ($student['cgpa'] < $warning_threshold and $student['earned_hours'] < 105) {
                                $warning = 1;
                                DB::table('students_current_warning')
                                    ->where('student_code', $student['username'])
                                    ->increment('warning');
                            } else {
                                DB::table('students_current_warning')
                                    ->where('student_code', $student['username'])->update(['warning' => 0]);
                            }
                        }
                        DB::table('students_semesters')->insert([
                            'student_code' => $student['username'],
                            'year' => $year,
                            'semester' => $semester,
                            'warning' => $warning,
                        ]);
                        $current_warning = DB::table('students_current_warning')
                            ->where('student_code', $student['username'])->first();
                        if ($current_warning->warning >= 3) {
                            DB::table('students')->where('username', $student['username'])
                                ->update(['student_classification' => 'مفصولين']);
                        }
                    }
                    DB::table('change_status_details')->insert([
                        'year' => $year,
                        'semester' => $semester,
                        'action' => 'warning',
                        'created_at' => Carbon::now(),
                    ]);
                });
                return redirect()->back()->with('success', 'تم تعديل انذارات الطلاب');
            } elseif ($semester == 'ترم صيفي') {
                DB::transaction(function () use ($semester, $next_year, $year, $current_students, $warning_threshold) {
                    $level_hours = [
                        'الاولي' => [27, 'الثانية'],
                        'الثانية' => [60, 'الثالثة'],
                        'الثالثة' => [93, 'الرابعة'],
                        'الرابعة' => [132, 'خريج']
                    ];
                    foreach ($current_students as $student) {
                        $student_excuse_stop = DB::table('students_excuses')
                            ->where('student_code', $student['username'])->where('year', $year)
                            ->where('type', 'وقف قيد')->exists();
                        $student_excuses_semester = DB::table('students_excuses')
                            ->where('student_code', $student['username'])->where('year', $year)
                            ->where('type', 'عذر عن ترم')->groupBy('student_code')->count();
                        $restricted = !($student_excuse_stop or $student_excuses_semester == 2);
                        $new_status = $student['studying_status'];
                        $new_classification = $student['student_classification'];
                        $new_group = $student['study_group'];
                        if ($restricted) { // law == 2
                            if ($student['cgpa'] >= $warning_threshold or $student['earned_hours'] >= 105) {
                                DB::table('students_current_warning')
                                    ->where('student_code', $student['username'])->update(['warning' => 0]);
                            }
                            if ($student['earned_hours'] < $level_hours[$student['study_group']][0]) {
                                if ($student['studying_status'] == 'مستجد') {
                                    /** hit7awel le باقي **/
                                    $new_classification = 'مقيد';
                                    $new_status = 'باقي';
                                } elseif ($student['studying_status'] == 'باقي') {
                                    if ($student['study_group'] == 'الاولي') {
                                        /** hit7awel le فصل الطالب **/
                                        $new_classification = 'مفصولين';
                                    } else {
                                        /** hit7awel le من الخارج **/
                                        $new_classification = 'مقيد';
                                        $new_status = 'من الخارج';
                                    }
                                } elseif ($student['studying_status'] == 'من الخارج') {
                                    if ($student['study_group'] == 'الثانية') {
                                        /** hit7awel le فصل الطالب **/
                                        $new_classification = 'مفصولين';
                                    } else {
                                        /** count number of من الخارج **/
                                        $registration_years = DB::table('registration_years')
                                            ->where('student_code', $student['username'])
                                            ->where('study_group', $student['study_group'])->count();
                                        if ($registration_years < 5) {
                                            /** hit7awel le من الخارج **/
                                            $new_classification = 'مقيد';
                                            $new_status = 'من الخارج';
                                        } else {
                                            /** hit7awel le فصل الطالب **/
                                            $new_classification = 'مفصولين';
                                        }
                                    }
                                }
                            } else {
                                /** hit7awel le  مستجد فى المستوى التالى **/
                                $new_status = 'مستجد';
                                $new_group = $level_hours[$student['study_group']][1];
                                if ($new_group == 'خريج') {
                                    if ($student['military_education'] != 'غير مجتاز') {
                                        $new_classification = 'خريجين';
                                    } else {
                                        $new_group = 'الرابعة';
                                        $new_classification = 'مقيد';
                                        if ($student['studying_status'] == 'مستجد') {
                                            /** hit7awel le باقي **/
                                            $new_status = 'باقي';
                                        } elseif ($student['studying_status'] == 'باقي') {
                                            /** hit7awel le من الخارج **/
                                            $new_status = 'من الخارج';
                                        } elseif ($student['studying_status'] == 'من الخارج') {
                                            /** count number of من الخارج **/
                                            $registration_years = DB::table('registration_years')
                                                ->where('student_code', $student['username'])
                                                ->where('study_group', $student['study_group'])->count();
                                            if ($registration_years < 5) {
                                                /** hit7awel le من الخارج **/
                                                $new_status = 'من الخارج';
                                            } else {
                                                /** hit7awel le فصل الطالب **/
                                                $new_classification = 'مفصولين';
                                            }
                                        }
                                    }
                                } else {
                                    $new_classification = 'مقيد';
                                }
                            }
                            DB::table('students')->where('username', $student['username'])->update([
                                'study_group' => $new_group,
                                'studying_status' => $new_status,
                                'student_classification' => $new_classification,
                            ]);
                        }
                        if ($new_group != 'خريج') {
                            $data = [
                                'student_code' => $student['username'],
                                'year' => $next_year,
                                'study_group' => $new_group,
                                'studying_status' => $new_status,
                            ];
                            DB::table('registration_years')->insert($data);
                            if (!$restricted) {
                                DB::table('students')->where('username', $student['username'])->update([
                                    'student_classification' => 'مقيد',
                                ]);
                            }
                        }
                    }
                    DB::table('change_status_details')->insert([
                        'year' => $year,
                        'semester' => $semester,
                        'action' => 'level up',
                        'created_at' => Carbon::now(),
                    ]);
                });
            }
            return redirect()->back()->with('success', 'تم تعديل مستويات الطلاب');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال لم يتم تعديل البيانات');
        }
    }

    public function changeStudentLevelSimulation()
    {
        set_time_limit(0);
        $year = $this->getCurrentYear();
        $current_students = DB::table('registration_years')
            ->where('registration_years.year', $year)->select('students.*')
            ->join('students', 'registration_years.student_code', '=', 'students.username')
            ->get()->toArray();
        $level_hours = [
            'الاولي' => [27, 'الثانية'],
            'الثانية' => [60, 'الثالثة'],
            'الثالثة' => [93, 'الرابعة'],
            'الرابعة' => [132, 'خريج']
        ];
        $counters = [
            'الاولي' => ['total' => 0, 'restricted' => 0, 'stop restriction' => 0, 'next level' => 0, 'remaining' => 0,
                'separate' => 0, 'warning' => 0],
            'الثانية' => ['total' => 0, 'restricted' => 0, 'stop restriction' => 0, 'next level' => 0, 'remaining' => 0,
                'remaining outside' => 0, 'separate' => 0, 'warning' => 0],
            'الثالثة' => ['total' => 0, 'restricted' => 0, 'stop restriction' => 0, 'next level' => 0, 'remaining' => 0,
                'remaining outside 1' => 0, 'remaining outside 2' => 0, 'remaining outside 3' => 0, 'separate' => 0,
                'warning' => 0],
            'الرابعة' => ['total' => 0, 'restricted' => 0, 'stop restriction' => 0, 'next level' => 0, 'remaining' => 0,
                'remaining outside 1' => 0, 'remaining outside 2' => 0, 'remaining outside 3' => 0, 'separate' => 0,
                'warning' => 0],
        ];
        $warning_threshold = (float)$this->getData(['warning_threshold'])['warning_threshold'][0];
        foreach ($current_students as $student) {
            $counters[$student->study_group]['total']++;
            if (!in_array($student->student_classification, ['سحب ملف', 'خريجين'])) {
                $student_excuse_stop = DB::table('students_excuses')
                    ->where('student_code', $student->username)->where('year', $year)
                    ->where('type', 'وقف قيد')->exists();
                $student_excuses_semester = DB::table('students_excuses')
                    ->where('student_code', $student->username)->where('year', $year)
                    ->where('type', 'عذر عن ترم')->groupBy('student_code')->count();
                $restricted = !($student_excuse_stop or $student_excuses_semester == 2);
                if ($restricted) {
                    $counters[$student->study_group]['restricted']++;
                } else {
                    $counters[$student->study_group]['stop restriction']++;
                    continue;
                }
                if ($student->cgpa < $warning_threshold) {
                    $counters[$student->study_group]['warning']++;
                }
                if ($student->earned_hours < $level_hours[$student->study_group][0]) {
                    if ($student->studying_status == 'مستجد') {
                        /** hit7awel le باقي **/
                        $counters[$student->study_group]['remaining']++;
                    } elseif ($student->studying_status == 'باقي') {
                        if ($student->study_group == 'الاولي') {
                            /** hit7awel le فصل الطالب **/
                            $counters[$student->study_group]['separate']++;
                        } else {
                            /** hit7awel le من الخارج **/
                            if ($student->study_group == 'الثانية') {
                                $counters[$student->study_group]['remaining outside']++;
                            } else {
                                $counters[$student->study_group]['remaining outside 1']++;
                            }
                        }
                    } elseif ($student->studying_status == 'من الخارج') {
                        if ($student->study_group == 'الثانية') {
                            /** hit7awel le فصل الطالب **/
                            $counters[$student->study_group]['separate']++;
                        } else {
                            /** count number of من الخارج **/
                            $registration_years = DB::table('registration_years')
                                ->where('student_code', $student->username)
                                ->where('study_group', $student->study_group)->count();
                            if ($registration_years < 5) {
                                /** hit7awel le من الخارج **/
                                $counters[$student->study_group]['remaining outside ' . ($registration_years - 1)]++;
                            } else {
                                /** hit7awel le فصل الطالب **/
                                $counters[$student->study_group]['separate']++;
                            }
                        }
                    }
                } else {
                    $counters[$student->study_group]['next level']++;
                }
            }
        }
        return redirect()->route('configuration')->with(compact('counters'));
    }

    public function showSemesterRegistration(Request $request)
    {
        $login = session()->get('login') ?: false;
        if ($request->getMethod() == 'POST') {
            $request->validate([
                'username' => 'required|in:chairman',
                'password' => 'required|in:chair-man55'
            ]);
            $login = true;
        }
        if ($login) {
            session()->flash('login', true);
            $data_filter = $this->getDistinctValues('registration_semester', ['year', 'semester']);
            $data = $request->validate([
                'year' => 'nullable|in:' . implode(',', $data_filter['year']),
                'semester' => 'nullable|in:' . implode(',', $data_filter['semester']),
            ]);
            $year = $data['year'] ?? $this->getCurrentYear();
            $semester = $data['semester'] ?? $this->getCurrentSemester();
            $col_counter = [];
            $data = $this->getData(['study_group', 'specialization', 'studying_status']);
            $study_groups = $data['study_group'];
            $specializations = $data['specialization'];
            $studying_status = $data['studying_status'];
            $info = [];
            foreach ($study_groups as $study_group) {
                $col_counter[$study_group]['col'] = 0;
                foreach ($specializations as $specialization) {
                    $col_counter[$study_group][$specialization]['col'] = 0;
                    foreach ($studying_status as $status) {
                        $col_counter[$study_group][$specialization]['col']++;
                        $col_counter[$study_group]['col']++;
                        $where_data = ['registration_years.study_group' => $study_group,
                            'registration_years.year' => $year, 'registration_years.studying_status' => $status];

                        $info['اجمالى عدد الطلاب'][$study_group][$specialization][$status] =
                            DB::table('registration_years')->where($where_data)
                                ->join('students', function ($join) use ($specialization) {
                                    $join->on('registration_years.student_code', '=', 'students.username')
                                        ->where('students.specialization', $specialization);
                                })->count();
                            //   dd($info['اجمالى عدد الطلاب'][$study_group][$specialization][$status]);
                        $info['الطلاب المقيدين'][$study_group][$specialization][$status] =
                            DB::table('registration_years')->where($where_data)
                                ->join('students', function ($join) use ($specialization) {
                                    $join->on('registration_years.student_code', '=', 'students.username')
                                    ->where('students.student_classification', '=', 'مقيد')
                                        ->where('students.specialization', $specialization);
                                })->count();

                               // dd($info['الطلاب المقيدين'][$study_group][$specialization][$status]);
                        // $info['الطلاب المقيدين'][$study_group][$specialization][$status] =
                        //     $info['اجمالى عدد الطلاب'][$study_group][$specialization][$status] -
                        //     $info['الطلاب المقيدين'][$study_group][$specialization][$status];


                        $registered = DB::table('registration_years')->where($where_data)
                            ->join('registration_semester', function ($join) use ($semester, $year) {
                                $join->on('registration_semester.student_code', '=', 'registration_years.student_code')
                                    ->where('registration_semester.year', $year)
                                    ->where('registration_semester.semester', $semester);
                            })->join('students', function ($join) use ($specialization) {
                                $join->on('registration_years.student_code', '=', 'students.username')
                                    ->where('students.specialization', $specialization);
                            });
                        $info['الطلاب المسجلين'][$study_group][$specialization][$status] = $registered->count();
                        $info['الطلاب المسددين'][$study_group][$specialization][$status] = $registered
                            ->where('payment', 1)->count();
                        $reg_courses = DB::table('registration_years')
                            ->where($where_data)
                            ->join('registration_semester', function ($join) use ($semester, $year) {
                                $join->on('registration_semester.student_code', '=', 'registration_years.student_code')
                                    ->where('registration_semester.year', $year)
                                    ->where('registration_semester.semester', $semester);
                            })->join('registration', function ($join) use ($semester, $year) {
                                $join->on('registration.student_code', '=', 'registration_semester.student_code')
                                    ->where('registration.year', $year)
                                    ->where('registration.semester', $semester);
                            })->join('courses', 'courses.full_code', '=',
                                'registration.course_code')
                            ->join('students', function ($join) use ($specialization) {
                                $join->on('registration_years.student_code', '=', 'students.username')
                                    ->where('students.specialization', $specialization);
                            });
                        $info['الساعات المسجله'][$study_group][$specialization][$status] = $reg_courses
                            ->sum('hours');
                        $reg_courses->where('payment', 1);
                        $info['الساعات المسدده'][$study_group][$specialization][$status] = $reg_courses
                            ->sum('hours');
                        $payment = DB::table('registration_years')->where($where_data)
                            ->join('students_payments', 'students_payments.student_code', '=',
                                'registration_years.student_code')->where('students_payments.year', $year)
                            ->where('students_payments.semester', $semester)
                            ->join('students', function ($join) use ($specialization) {
                                $join->on('registration_years.student_code', '=', 'students.username')
                                    ->where('students.specialization', $specialization);
                            });
                        $info['اجمالى المبلغ المستحق'][$study_group][$specialization][$status] = $payment
                            ->sum('payment');
                        $info['اجمالى المبلغ المسدد'][$study_group][$specialization][$status] = $payment
                            ->sum('paid_payments');
                        $info['اجمالى مبلغ المحفظة'][$study_group][$specialization][$status] =
                            DB::table('registration_years')->where($where_data)
                                ->join('students_wallet', 'students_wallet.student_code', '=',
                                    'registration_years.student_code')
                                ->join('students', function ($join) use ($specialization) {
                                    $join->on('registration_years.student_code', '=', 'students.username')
                                        ->where('students.specialization', $specialization);
                                })->sum('amount');
                        $info['اجمالى مبلغ الخصومات المحفظة'][$study_group][$specialization][$status] =
                            DB::table('registration_years')->where($where_data)
                                ->join('students_discounts', 'students_discounts.student_code', '=',
                                    'registration_years.student_code')
                                ->where('students_discounts.year', $year)
                                ->where('students_discounts.semester', $semester)
                                ->where('type', 'محفظة')
                                ->join('students', function ($join) use ($specialization) {
                                    $join->on('registration_years.student_code', '=', 'students.username')
                                        ->where('students.specialization', $specialization);
                                })->sum('amount');
                        $info['اجمالى مبلغ الخصومات'][$study_group][$specialization][$status] =
                            DB::table('registration_years')->where($where_data)
                                ->join('students_discounts', 'students_discounts.student_code', '=',
                                    'registration_years.student_code')
                                ->where('students_discounts.year', $year)
                                ->where('students_discounts.semester', $semester)
                                ->where('type', 'دراسية')
                                ->join('students', function ($join) use ($specialization) {
                                    $join->on('registration_years.student_code', '=', 'students.username')
                                        ->where('students.specialization', $specialization);
                                })->sum('amount');
                        $info['اجمالى المبلغ المتبقى'][$study_group][$specialization][$status] =
                            $info['اجمالى المبلغ المستحق'][$study_group][$specialization][$status] -
                            $info['اجمالى المبلغ المسدد'][$study_group][$specialization][$status] -
                            $info['اجمالى مبلغ الخصومات'][$study_group][$specialization][$status];
                    }
                }
            }
            return view('admin.show_semester_registrations', compact('login', 'info',
                'year', 'semester', 'data_filter', 'study_groups', 'specializations', 'studying_status', 'col_counter'));
        }
        return view('admin.show_semester_registrations', compact('login'));
    }

    public function confirmationIndex(Request $request)
    {
        $items_per_pages = 50;
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $sort = DB::table('registration_semester')->where('year', $year)
            ->where('semester', $semester)
            ->join('students', 'students.username', '=', 'registration_semester.student_code')
            ->orderBy('guidance', 'DESC')->orderBy('student_code')->get()
            ->pluck('student_code')->toArray();
        $orderedIds = implode('\',\'', $sort);
        $students = Student::select('students.name', 'students.username', 'cgpa', 'earned_hours', 'total_hours'
            , 'studying_status', 'student_classification', 'specialization', 'study_group')
            ->orderByRaw(DB::raw('FIELD(username,\'' . $orderedIds . '\') DESC'))
            ->orderByRaw('SUBSTRING(username, 2, 6)')->paginate($items_per_pages);
        if (isset($request->search)) {
            $students = Student::select('students.name', 'students.username', 'cgpa', 'earned_hours', 'total_hours'
                , 'studying_status', 'student_classification', 'specialization', 'study_group')
                ->whereRaw('CONCAT(`name`,"\0",`username`,"\0",`specialization`,"\0",`study_group`,"\0"
                ,`studying_status`) LIKE ?', ['%' . $request->search . '%'])
                ->orderByRaw(DB::raw('FIELD(username,\'' . $orderedIds . '\') DESC'))
                ->orderByRaw('SUBSTRING(username, 2, 6)')->paginate($items_per_pages);
            $students->appends(['search' => $request->search]);
        }
        $request->validate([
            'page' => 'nullable|integer|between:1,' . $students->lastPage(),
            'search' => 'nullable|string|not_regex:/[#;<>]/u',
        ]);
        $hidden_keys = [];
        $removed_keys = [];
        $keys = ['الاسم', 'Code', 'المعدل التراكمي للدرجات', 'الساعات المكتسبة', 'إجمالي الساعات المسجلة',
            'الحالة الدراسية', 'تصنيف الطلاب', 'التخصص', 'الفرقة الدراسية'];
        $status = [];
        foreach ($students->pluck('username')->toArray() as $username) {
            $status[$username] = $this->checkGuidance($username);
        }
        $edit = 'admin.show.registration';
        $delete = 'admin.registrations.delete';
        return view('academic_advising.confirmation')->with([
            'students' => $students,
            'keys' => $keys,
            'primaryKey' => 'username',
            'removed_keys' => $removed_keys,
            'hidden_keys' => $hidden_keys,
            'search' => $request->search,
            'status' => $status,
            'edit' => $edit,
            'delete' => $delete,
        ]);
    }

 public function deleteRegistration($username)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $rule = [
            'username' => ['required', 'exists:students,username',
                function ($attribute, $value, $fail) use ($year, $semester) {
                    if (!$this->registrationExists($value, $semester, $year))
                        $fail('');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "تسجيل الطالب  $username غير موجود او ان البيانات غير صحيحة");
        }
      $amount_withdrawn  = DB::table('students_wallet_transaction')->where('student_code',$username)
        ->where('year',$year)->where('semester',$semester)->where('type','سحب')->pluck('amount')[0];
        $amount_wallet = DB::table('students_wallet')->where('student_code',$username)->pluck('amount')[0];
        $total_amount = $amount_wallet + $amount_withdrawn;

        try {
            DB::transaction(function () use ($semester, $year, $username ,$total_amount) {
                DB::table('students_payments')->where([
                    ['student_code', '=', $username],
                    ['year', '=', $year],
                    ['semester', '=', $semester]
                ])->delete();
                DB::table('section_number')->where([
                    ['student_code', '=', $username],
                    ['year', '=', $year],
                    ['semester', '=', $semester],
                ])->delete();
                DB::table('registration')->where([
                    ['student_code', '=', $username],
                    ['year', '=', $year],
                    ['semester', '=', $semester],
                ])->delete();
                DB::table('registration_semester')->where([
                    ['student_code', '=', $username],
                    ['year', '=', $year],
                    ['semester', '=', $semester],
                ])->delete();
                DB::table('students_wallet')->where('student_code', $username)
                ->update(['amount'=>$total_amount]);
                DB::table('students_wallet_transaction')->where([
                    ['student_code', '=', $username],
                    ['year', '=', $year],
                    ['semester', '=', $semester],
                    ['type' , '=', 'سحب'],
                ])->delete();

                $type = 'success';
                $title = 'تم حذف تسجيلك';
                $message = 'تم حذف تسجيلك فى هذا الترم برجاء مراجعة المرشد الأكاديمي';
                DB::table('notifications')->insert([
                    'username' => $username,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                ]);
            });
            return redirect()->back()->with('success', "تم حذف التسجيل الطالب $username بنجاح");
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }


    public function showRegistration($username)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();

        $rule = [
            'username' => ['required', 'exists:students,username',
                function ($attribute, $value, $fail) use ($year, $semester) {
                    if (!$this->registrationExists($value, $semester, $year))
                        $fail('');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->route('admin.registrations')
                ->with('error', "تسجيل الطالب  $username غير موجود او ان البيانات غير صحيحة");
        }
        $student = $this->getStudentInfo($username);
        $courses = $this->getStudentCourses($student);
        $previous_courses = collect($courses[2])->sortByDesc(['registration_year', 'registration_semester',
            'elective'])->groupBy(['registration_year', 'registration_semester'])->toArray();
        $registered_courses = $this->getRegisteredCourses($username, $year, $semester);
        return view('admin.student_subjects', compact('courses', 'semester',
            'registered_courses', 'previous_courses', 'year', 'student'));
    }

    public function confirmRegistration($username, Request $request)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $studentWallet = $this->getStudentWallet($username);
        if ($studentWallet) {
            $wallet = $studentWallet->amount;
         }else{
              return redirect()->back()->with('error', ' هذا الطالب ليس له رصيد في المحفظة')->withInput();
         }
         $student = $this->getStudentInfo($username);
        $registered_courses = $this->getRegisteredCourses($username, $year, $semester);
        $rule = [
            'username' => ['required', 'exists:students,username',
                function ($attribute, $value, $fail) use ($year, $semester) {
                    if (!$this->registrationExists($value, $semester, $year))
                        $fail('الطالب غير مسجل');
                    if ($this->checkGuidance($value))
                        $fail('تم تأكيد التسجيل من قبل');
                },
            ],
        ];
         $StudentLevel="";
        if ($student['study_group']=="الاولي")
        {
            $StudentLevel="first";
        }
        elseif ($student['study_group']=="الثانية")
        {
            $StudentLevel="second";
        }
        elseif ($student['study_group']=="الثالثة")
        {
            $StudentLevel="third";
        }
        elseif ($student['study_group']=="الرابعة")
        {
            $StudentLevel="fourth";
        }
        else
        {
            return redirect()->back()->with('error', 'حدث خطأ في التسجيل')->withInput();
        }
        $checkAdministrative = DB::table('payments_administrative_expenses')->where('student_code',$username)
        ->where('year',$year)->pluck('used');
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->route('admin.show.registration', ['username' => $username])->with('error', $validator->errors()
                ->toArray()['username'][0]);
        }
        $rules = [
            'guidance' => 'required|array|distinct|between:1,' . count($registered_courses),
            'guidance.*' => ['required', 'in:1',
                function ($attribute, $value, $fail) use ($registered_courses) {
                    if (!in_array(explode('.', $attribute)[1],
                        array_column($registered_courses, 'full_code')))
                        $fail('هذه القيمة ' . explode('.', $attribute)[1] . ' غير موجودة');
                },
            ],
        ];
        $data = $request->validate($rules);
        $selected_courses = $this->getAllCourses(array_keys($data['guidance']));
        DB::beginTransaction();
        try {
            $student = $this->getStudentInfo($username);
            $payment_data = [];
            $payment_data['student_code'] = $username;
            $payment_data['year'] = $year;
            $payment_data['semester'] = $semester;
            $payment_data['hours'] = 0;
            $payment_data['hour_payment'] = $this->getHourPayment($student['specialization'], $student['study_group'],
                $student['studying_status']);
            $payment_data['ministerial_payment'] = $this->getMinisterialPayment($student['specialization'],
                $student['study_group'], $student['studying_status']);
            $all_courses = $this->getAllCourses(array_keys($data['guidance']));
            $elective_counter = $this->getElectiveCourseCount();
            $merged_array = [];
            foreach (array_chunk($elective_counter, 2, true) as $chunk) {
                $keys = array_keys($chunk);
                $values = array_values($chunk);
                $merged_array[$keys[0] . '-' . $keys[1]] = $values[0] + $values[1];
            }
            $previous_elective = $this->getElectiveCourses($username);
            $total_elective_bygroup = null;
            $total_elective_bygroup=$this->getElectiveCourseCountByGroup()[$student['study_group']];

            foreach ($previous_elective as $key => $value) {
               $search_key = strval($key);
                foreach ($merged_array as $merged_key => $merged_value) {
                    $merged_keys = explode('-', $merged_key);
                }
           }
            $selected_courses_elective = array_filter($selected_courses, function ($item) {
                    return $item['elective'] == 1 && $item['departments_id'] == $student['departments_id'];
                });
                $total_elective_bygroup = $this->getElectiveCourseCountByGroup()[$student['study_group']];
                if ($total_elective_bygroup === 'الاولي') {
                    $total_elective_bygroup = 2;
                } elseif ($total_elective_bygroup === 'الثانية') {
                    $total_elective_bygroup = 0;
                } elseif ($total_elective_bygroup === 'الثالثة') {
                    $total_elective_bygroup = 3;
                } elseif ($total_elective_bygroup === 'الرابعة') {
                    $total_elective_bygroup = 3;
                }

                foreach ($selected_courses_elective as $selected_course_elective) {
                            if ($selected_course_elective['semester'] == 1 || $selected_course_elective['semester'] == 2) {
                            $total_elective_bygroup = 2;
                        } elseif ($selected_course_elective['semester'] == 3 || $selected_course_elective['semester'] == 4) {
                            $total_elective_bygroup = 0;
                        } elseif ($selected_course_elective['semester'] == 5 || $selected_course_elective['semester'] == 6) {
                            $total_elective_bygroup = 3;
                        } elseif ($selected_course_elective['semester'] == 7 || $selected_course_elective['semester'] == 8) {
                            $total_elective_bygroup = 3;
                        }
                }
                if ($previous_elective == null) {
                    $total_elective_count = count($selected_courses_elective);
                    if (isset($value)) {
                        $total_elective_count += count($value);
                    }
                } else {
                    $total_elective_count = count($selected_courses_elective);
                }
                if($selected_courses_elective){
                    if($total_elective_count > $total_elective_bygroup) {
                        return redirect()->back()->with('error', 'خطأ لقد اجتازت المقرارات الاختيارية في هذا المستوي ')->withInput();
                        }
                }

            foreach ($all_courses as $course) {
                $payment_data['hours'] += $course['hours'];
            }
            $payment_data['payment'] = ($payment_data['hours'] * $payment_data['hour_payment']) +
                $payment_data['ministerial_payment'];
                 $payment_data['paid_payments'] = $payment_data['payment'];
                if ( empty($checkAdministrative) || !isset($checkAdministrative[0]) || $checkAdministrative[0] == 0)
               {
                       return redirect()->back()->with('error', ' استكمال المصاريف الادارية لاتمام التسجيل')->withInput();
                }
              if ($this->CheckNewStudentPayment($student['specialization'],$student['studying_status'],$wallet, $StudentLevel,$payment_data['payment'],$semester))
                     {
                          DB::table('students_wallet')->where('student_code', $username)->update([
                                    'amount' => $wallet - $payment_data['payment'],
                                ]);
                                DB::table('students_wallet_transaction')->insert([
                                    'student_code' => $username,
                                    'year' => $year,
                                    'semester' => $semester,
                                    'amount' => $payment_data['payment'],
                                    'date' => Carbon::now(),
                                    'type' => 'سحب',
                                    'reason' => 'سحب مصاريف دراسية',
                                ]);
                }else{
                    return redirect()->back()->with('error', ' الطالب ليس لديه رصيد كافي لاتمام التسجيل')->withInput();

                }
            DB::table('registration_semester')->where([
                ['student_code', '=', $username],
                ['year', '=', $year],
                ['semester', '=', $semester],
            ])->update(['guidance' => 1]);
            DB::table('students_payments')->insert($payment_data);
            DB::table('registration_semester')->where([
                ['student_code', '=', $username],
                ['year', '=', $year],
                ['semester', '=', $semester],
            ])->update(['payment' => 1]);
            $this->confirmStudentSemester($username, $year, $semester);
            $delete_code = DB::table('registration')->where([
                ['student_code', '=', $username],
                ['year', '=', $year],
                ['semester', '=', $semester],
            ])->whereNotIn('course_code', array_keys($data['guidance']))->get()
                ->pluck('course_code')->toArray();
            $del = DB::table('registration')->where([
                ['student_code', '=', $username],
                ['year', '=', $year],
                ['semester', '=', $semester],
            ])->whereNotIn('course_code', array_keys($data['guidance']))->delete();
            if ($del > 0) {
                $delete_courses = $this->getAllCourses($delete_code);
                $type = 'warning';
                $title = 'تم حذف المقررات التاليه من تسجيلك';
                $message = '<ul class="d-inline-block">';
                foreach ($delete_courses as $delete_course) {
                    $message .= '<li>' . $delete_course['full_code'] . ' ' . $delete_course['name'] . '</li>';
                }
                $message .= '</ul>';
                DB::table('notifications')->insert([
                    'username' => $username,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                ]);
            }
            DB::commit();
            if ($del > 0) {
                return redirect()->route('admin.registrations')->with([
                    'success' => "تم تأكيد المواد المختارة للطالب $username بنجاح",
                    'error' => "تم حذف المواد الغير مختاره ايضاً"
                ]);
            } else {
                return redirect()->route('admin.registrations')
                    ->with('success', "تم تأكيد المواد المختارة للطالب $username بنجاح");
            }
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function studentRegisterIndex(Request $request)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $validator = Validator::make($request->all(), [
            'username' => ['nullable', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) use ($year, $semester) {
                    if ($this->registrationExists($value, $semester, $year))
                        $fail('هذا الطالب مسجل في الترم');
                }
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.student.register')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if (!empty($data)) {
            $student = $this->getStudentInfo($data['username']);
            // dd($student);
            $courses = $this->getStudentCourses($student);
            $previous_courses = collect($courses[2])->sortByDesc(['registration_year', 'registration_semester',
                'elective'])->groupBy(['registration_year', 'registration_semester'])->toArray();
            return view('admin.student_register', compact('courses', 'semester', 'year',
                'student', 'previous_courses'));
        }
        return view('admin.student_register');
    }

    public function storeStudentRegister($student_code, Request $request)
    {
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $studentWallet = $this->getStudentWallet($student_code);
            if ($studentWallet) {
                $wallet = $studentWallet->amount;
            }else{
                return redirect()->back()->with('error', ' هذا الطالب ليس له رصيد في المحفظة')->withInput();
            }

            $rule = [
                'username' => ['required', 'exists:students,username',
                    function ($attribute, $value, $fail) use ($year, $semester) {
                        if ($this->registrationExists($value, $semester, $year))
                            $fail('هذا الطالب مسجل في الترم');
                    }
                ],
            ];
            $validator = Validator::make(['username' => $student_code], $rule);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }
            $student = $this->getStudentInfo($student_code);
            $courses = $this->getStudentCourses($student);

            $StudentLevel="";
            if ($student['study_group']=="الاولي")
            {
                $StudentLevel="first";
            }
            elseif ($student['study_group']=="الثانية")
            {
                $StudentLevel="second";
            }
            elseif ($student['study_group']=="الثالثة")
            {
                $StudentLevel="third";
            }
            elseif ($student['study_group']=="الرابعة")
            {
                $StudentLevel="fourth";
            }
            else
            {
                return redirect()->back()->with('error', 'حدث خطأ في التسجيل')->withInput();
            }
        $checkAdministrative = DB::table('payments_administrative_expenses')->where('student_code',$student_code)
        ->where('year',$year)->pluck('used');
            $courses_code = [];


                foreach ($courses[0] as $course) {
                if ($course->elective) {
                    if ($elective_counter[$course->semester] > 0) {
                        $courses_code[$course->full_code] = $course->hours;
                        $elective_counter[$course->semester]--;
                    }
                } else {
                    $courses_code[$course->full_code] = $course->hours;
                }
            }
            foreach ($courses[1] as $course) {
                $courses_code[$course->full_code] = $course->hours;
            }
            $registration_hour = $this->getStudentsRegistrationHour($student['specialization'], $student['study_group']);
            if (array_sum($courses_code) < $registration_hour) {
                foreach ($courses[4] as $course) {
                    $courses_code[$course->full_code] = $course->hours;
                }
            }
            $rules = [
                'guidance' => 'required|array|distinct|between:1,' . count($courses_code),
                'guidance.*' => ['required', 'in:1',
                    function ($attribute, $value, $fail) use ($courses_code) {
                        if (!in_array(explode('.', $attribute)[1], array_keys($courses_code)))
                            $fail('هذه القيمة ' . explode('.', $attribute)[1] . ' غير موجودة');
                    },
                ],
            ];
            $data = $request->validate($rules);
            $prerequisites = $this->getCoursePrerequisite(array_keys($data['guidance']));
            $selected_courses = $this->getAllCourses(array_keys($data['guidance']));
            $elective_counter = $this->getElectiveCourseCount();
            $merged_array = [];
            foreach (array_chunk($elective_counter, 2, true) as $chunk) {
            $keys = array_keys($chunk);
            $values = array_values($chunk);
            $merged_array[$keys[0] . '-' . $keys[1]] = $values[0] + $values[1];
        }
        $previous_elective = $this->getElectiveCourses($student_code);
        $total_elective_bygroup = null;
        foreach ($previous_elective as $key => $value) {
            $search_key = strval($key);
            foreach ($merged_array as $merged_key => $merged_value) {
                $merged_keys = explode('-', $merged_key);
            }
        }
        $selected_courses_elective = array_filter($selected_courses, function ($item) use($student) {
            return $item['elective'] == 1 && $item['departments_id'] == $student['departments_id'];
        });
        $total_elective_bygroup = $this->getElectiveCourseCountByGroup()[$student['study_group']];
        if ($total_elective_bygroup === 'الاولي') {
            $total_elective_bygroup = 2;
        } elseif ($total_elective_bygroup === 'الثانية') {
            $total_elective_bygroup = 0;
        } elseif ($total_elective_bygroup === 'الثالثة') {
            $total_elective_bygroup = 3;
        } elseif ($total_elective_bygroup === 'الرابعة') {
            $total_elective_bygroup = 3;
        }
        foreach ($selected_courses_elective as $selected_course_elective) {
            if ($selected_course_elective['semester'] == 1 || $selected_course_elective['semester'] == 2) {
                $total_elective_bygroup = 2;
            } elseif ($selected_course_elective['semester'] == 3 || $selected_course_elective['semester'] == 4) {
                $total_elective_bygroup = 0;
            } elseif ($selected_course_elective['semester'] == 5 || $selected_course_elective['semester'] == 6) {
                $total_elective_bygroup = 3;
            } elseif ($selected_course_elective['semester'] == 7 || $selected_course_elective['semester'] == 8) {
                $total_elective_bygroup = 3;
            }
        }
        if ($previous_elective == null) {
            $total_elective_count = count($selected_courses_elective);
            if (isset($value)) {
                $total_elective_count += count($value);
            }
        } else {
            $total_elective_count = count($selected_courses_elective);
        }
            if ($selected_courses_elective) {
                if ($total_elective_count > $total_elective_bygroup) {
                    return redirect()->back()->with('error', 'خطأ لقد اجتازت المقرارات الاختيارية في هذا المستوي ') ->withInput();
                }
            }
            $hour = 0;
            $insert_courses = [];
            foreach ($selected_courses as $course) {
                if (isset($prerequisites[$course['full_code']])) {
                    foreach ($prerequisites[$course['full_code']] as $prerequisite) {
                        if (!(in_array($prerequisite->required_cousrse_code, array_keys($data['guidance'])) or
                            in_array($prerequisite->required_cousrse_code,
                                array_column($courses[2], 'full_code')))) {
                            return redirect()->back()->with('error', 'المتطلبات السابقة للمقرر ' . $course['full_code']
                                . ' غير مسجل او مجتاز');
                        }
                    }
                }
                $hour += $course['hours'];
                if ($hour > $registration_hour) {
                    return redirect()->back()->with('error', 'لا يمكن التسجيل لقد تم تجاوز ' . $registration_hour . ' الساعة');
                }
                $insert_courses[] = [
                    'student_code' => $student_code,
                    'course_code' => $course['full_code'],
                    'year' => $year,
                    'semester' => $semester,
                ];
            }
            $reg_sem = [
                'student_code' => $student_code,
                'year' => $year,
                'semester' => $semester,
                'guidance' => 1,
                'created_at' => now(),
                'created_by' => auth()->id()
            ];
            DB::beginTransaction();
            try {
                $payment_data = [];
                $payment_data['student_code'] = $student_code;
                $payment_data['year'] = $year;
                $payment_data['semester'] = $semester;
                $payment_data['hours'] = $hour;
                $payment_data['hour_payment'] = $this->getHourPayment($student['specialization'], $student['study_group'],
                $student['studying_status']);
                $payment_data['ministerial_payment'] = $this->getMinisterialPayment($student['specialization'],
                $student['study_group'], $student['studying_status']);
                $payment_data['payment'] = ($payment_data['hours'] * $payment_data['hour_payment']) +
                    $payment_data['ministerial_payment'];
                    $payment_data['paid_payments'] = $payment_data['payment'];
                if ( empty($checkAdministrative) || !isset($checkAdministrative[0]) || $checkAdministrative[0] == 0)
                {
                        return redirect()->back()->with('error', ' استكمال المصاريف الادارية لاتمام التسجيل')->withInput();
                    }
                    if(!DB::table('students_payments_exception')->where(compact('student_code'))->exists()){

                if ($this->CheckNewStudentPayment($student['specialization'],$student['studying_status'],$wallet, $StudentLevel,$payment_data['payment'],$semester))
                        {
                            DB::table('students_wallet')->where('student_code', $student_code)->update([
                                        'amount' => $wallet - $payment_data['payment'],
                                    ]);
                                    DB::table('students_wallet_transaction')->insert([
                                        'student_code' => $student_code,
                                        'year' => $year,
                                        'semester' => $semester,
                                        'amount' => $payment_data['payment'],
                                        'date' => Carbon::now(),
                                        'type' => 'سحب',
                                        'reason' => 'سحب مصاريف دراسية',
                                    ]);
                    }else{

                        return redirect()->back()->with('error', ' الطالب ليس لديه رصيد كافي لاتمام التسجيل')->withInput();

                    }
                    }
                DB::table('registration')->insert($insert_courses);
                DB::table('registration_semester')->insert($reg_sem);
                DB::table('students_payments')->insert($payment_data);

                DB::table('registration_semester')->where([
                    ['student_code', '=', $student_code],
                    ['year', '=', $year],
                    ['semester', '=', $semester],
                ])->update(['payment' => 1]);
                $this->confirmStudentSemester($student_code, $year, $semester);

                DB::commit();
                return redirect()->route('admin.student.register')
                    ->with('success', "تم تسجيل و تأكيد المواد المختارة للطالب $student_code بنجاح");
            } catch (Exception $ex) {
                dd($ex);
                DB::rollback();
                return redirect()->back()->with('error', 'خطأ في الإتصال');
            }
    }
    public function deleteAllRegistration(){
            try{
                $year = $this->getCurrentYear();
                    $semester = $this->getCurrentSemester();
                    $students = DB::table('registration_semester')->where('payment', 0)
                        ->where('year', $year)->where('semester', $semester)->get()->toArray();
                    foreach ($students as $student) {
                        $con = [
                            'student_code' => $student->student_code,
                            'year' => $student->year,
                            'semester' => $student->semester,
                        ];
                        DB::table('registration')->where($con)->delete();
                        DB::table('registration_semester')->where($con)->delete();
                    }
                    return redirect()->back()->with('success', 'تم تغير البيانات');
                } catch (Exception $ex) {
                    dd($ex);
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }

    }
    public function updateExtraFees(Request $request) {
        $rules = [
            'name_fees.*' => 'required|string',
            'amount.*' => 'required|numeric|min:0',
            'active.*' => 'required|numeric|in:0,1',
        ];
        $validatedData = $request->validate($rules);
        $extraFeesData = [];
        foreach ($validatedData['name_fees'] as $index => $nameFee) {
            $extraFeesData[] = [
                'name_fees' => $nameFee,
                'amount' => $validatedData['amount'][$index],
                'active' => $validatedData['active'][$index],
            ];
        }
        try {
            foreach ($extraFeesData as $index => $extraFeeData) {
                DB::table('extra_fees')->where('id', $index + 1)->update($extraFeeData);
            }
            return redirect()->back()->with('success', 'تم تغيير البيانات');
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->with('error', 'خطأ في الاتصال');
        }
    }

       public function updateInsurancePayment(Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            //'summer_payment' => 'required|numeric|min:0',
        ];
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            DB::table('administrative_expenses')->where('id', 1)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }
    public function updateProfileExpenses(Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            //'summer_payment' => 'required|numeric|min:0',
        ];
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            DB::table('administrative_expenses')->where('id', 2)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateRegistrationFees(Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            //'summer_payment' => 'required|numeric|min:0',
        ];
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            DB::table('administrative_expenses')->where('id', 3)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }
    public function updateCardEmail(Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            //'summer_payment' => 'required|numeric|min:0',
        ];
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            DB::table('administrative_expenses')->where('id', 4)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }
    public function updateRenewCardEmail(Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            //'summer_payment' => 'required|numeric|min:0',
        ];
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            DB::table('administrative_expenses')->where('id', 5)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }
    public function updateMilitaryPayment(Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            //'summer_payment' => 'required|numeric|min:0',
        ];
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            DB::table('administrative_expenses')->where('id', 6)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }
    public function updateTotalExpenses(Request $request)
    {
        $rule = [
            'first_payment' => 'required|numeric|min:0',
            'second_payment' => 'required|numeric|min:0',
            'third_payment' => 'required|numeric|min:0',
            'fourth_payment' => 'required|numeric|min:0',
            //'summer_payment' => 'required|numeric|min:0',
        ];
        $data = $request->validate($rule);
        foreach ($data as $key => $value) {
            $data[str_replace("_payment", "", $key)] = $value;
            unset($data[$key]);
        }
        try {
            DB::table('administrative_expenses')->where('id', 7)->update($data);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }
     public function deleteExceptionStudents(Request $request){

        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();

        $wallet = $this->getStudentWallet($request->student_code)->amount;
        $subjects_registered = DB::table('registration')->where('student_code',$request->student_code)->where('year',$year)->where('semester',$semester)
        ->pluck('course_code')->toArray();
        $subject_hours = DB::table('courses')->whereIn('full_code',$subjects_registered)->pluck('hours')->toArray();
        $total_hours = array_sum($subject_hours);
        $student_info = $this->getStudentInfo($request->student_code);
         $checkAdministrative = DB::table('payments_administrative_expenses')->where('student_code',$request->student_code)->where('year',$year)->pluck('used');
            if ( empty($checkAdministrative) || !isset($checkAdministrative[0]) || $checkAdministrative[0] == 0)
               {
                       return redirect()->back()->with('error', ' استكمال المصاريف الادارية لاتمام التسجيل')->withInput();
                }
             $ministerial_payment['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 2)->first();
             $ministerial_payment['english'] = (array)DB::table('hour_payment_english')->where('id', 2)->first();
             $ministerial_payment_remaining['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 4)->first();
             $ministerial_payment_remaining['english'] = (array)DB::table('hour_payment_english')->where('id', 4)->first();

             if ($student_info['studying_status'] == 'مستجد' && $semester != 'ترم صيفي') {
                $payments = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? (array)DB::table('hour_payment_arabic')->where('id', 1)->first()
                    : (array)DB::table('hour_payment_english')->where('id', 1)->first();

                $payment = 0;

                switch ($student_info['study_group']) {
                    case 'الاولي':
                        $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                        $payment_total = ($payments['first'] * $total_hours) + $ministerial_payment[$ministerial_payment_key]['first'];
                        break;
                    case 'الثانية':
                        $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                        $payment_total = ($payments['second'] * $total_hours) + $ministerial_payment[$ministerial_payment_key]['second'];
                        break;
                    case 'الثالثة':
                        $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                        $payment_total = ($payments['third'] * $total_hours) + $ministerial_payment[$ministerial_payment_key]['third'];
                        break;
                    case 'الرابعة':
                        $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                        $payment_total = ($payments['fourth'] * $total_hours) + $ministerial_payment[$ministerial_payment_key]['fourth'];
                        break;
                }


            if($payment_total <= $wallet){

               DB::table('students_wallet')->where('student_code',$request->student_code)->update([
                    'amount' => $wallet - $payment_total
               ]);
               DB::table('students_wallet_transaction')->insert([
                                    'student_code' => $request->student_code,
                                    'year' => $year,
                                    'semester' => $semester,
                                    'amount' =>  $payment_total,
                                    'date' => Carbon::now(),
                                    'type' => 'سحب',
                                    'reason' => 'سحب مصاريف دراسية',
                                ]);
                DB::table('registration_semester') ->where('student_code', $request->student_code)->where('year', $year)->update([
                    'payment' => 1
            ]);
                 DB::table('students_payments_exception')->where('student_code',$request->student_code)->delete();
               return redirect()->back()->with('success', 'تم تغير البيانات');

            }else{
                return redirect()->back()->withInput()->with('error', 'الطالب ليس لديه رصيد كافي');
            }
        }elseif($student_info['studying_status'] == 'باقي' and $semester != 'ترم صيفي'){
            $payments = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? (array)DB::table('hour_payment_arabic')->where('id', 3)->first()
            : (array)DB::table('hour_payment_english')->where('id', 3)->first();
            $payment = 0;
            switch ($student_info['study_group']) {
                case 'الاولي':
                    $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                    $payment_total = ($payments['first'] * $total_hours) + $ministerial_payment_remaining[$ministerial_payment_key]['first'];
                    break;
                case 'الثانية':
                    $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                    $payment_total = ($payments['second'] * $total_hours) + $ministerial_payment_remaining[$ministerial_payment_key]['second'];
                    break;
                case 'الثالثة':
                    $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                    $payment_total = ($payments['third'] * $total_hours) + $ministerial_payment_remaining[$ministerial_payment_key]['third'];
                    break;
                case 'الرابعة':
                    $ministerial_payment_key = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
                    $payment_total = ($payments['fourth'] * $total_hours) + $ministerial_payment_remaining[$ministerial_payment_key]['fourth'];
                    break;
            }

            if($payment_total <= $wallet){
              DB::table('students_wallet')->where('student_code',$request->student_code)->update([
                    'amount' => $wallet - $payment_total
              ]);
                DB::table('students_wallet_transaction')->insert([
                                    'student_code' => $request->student_code,
                                    'year' => $year,
                                    'semester' => $semester,
                                    'amount' => $wallet - $payment_total,
                                    'date' => Carbon::now(),
                                    'type' => 'سحب',
                                    'reason' => 'سحب مصاريف دراسية',
                                ]);

          DB::table('registration_semester') ->where('student_code', $request->student_code)->where('year', $year)->update([
                'payment' => 1
            ]);

               DB::table('students_payments_exception')->where('student_code',$request->student_code)->delete();

               return redirect()->back()->with('success', 'تم تغير البيانات');

            }else{
                return redirect()->back()->withInput()->with('error', 'الطالب ليس لديه رصيد كافي');

            }

        }else{
                      return redirect()->back()->withInput()->with('error', 'هذا التيرم غير مناسب لحذف الطالب');
        }
    }

     public function updateMaintenanceMode(Request $request){
            $rules = [
                    'maintenance_mood' => 'required|integer|between:0,1',
            ];
            $data = $request->validate($rules);
            try{
            DB::table('data')->where('data_key','maintenance_mood')->update(['value' => $request->maintenance_mood]);
            return redirect()->back()->with('success', 'تم تغير البيانات');
        }catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
        }

        //     public function getRegistration()
        // {
        //     $semester = $this->getCurrentSemester();
        //     $year = $this->getCurrentYear();
        //     if ($semester == '') {
        //         return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
        //     }
        //     $filter_data = $this->getDistinctValues('registration', ['course_code', 'year']);


        //     return view('control.registerations' , compact('filter_data'));
        // }


        // public function exportRegistration(Request $request){
        //     set_time_limit(0);
        //     $semester = $this->getCurrentSemester();
        //     $year = $this->getCurrentYear();
        //     if ($semester == '') {
        //         return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
        //     }
        //     $filter_data = $this->getDistinctValues('registration', ['course_code', 'year'],false);
        //     $data = $request->validate([
        //         'course_code' => 'required|in:' . implode(',', $filter_data['course_code']),
        //         'year' => 'required|in:' . implode(',', $filter_data['year']),
        //     ]);
        //   $regists =   DB::table('registration')->select(['registration.student_code','registration.course_code','registration.year',
        //   'registration.semester','registration.yearly_performance_score','registration.written','registration.grade', 'students.name'])
        //   ->where('registration.course_code', $data['course_code'])->where('year', $data['year'])
        //   ->join('students', function ($join) use ($data) {
        //     $join->on('students.username', '=', 'registration.student_code');
        //   })->get();
        //     $headers = [
        //         [

        //             [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => 'كود الطالب'
        //             ],
        //              [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => 'اسم الطالب'
        //             ],
        //             [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => 'كود المقرر الدراسي'
        //             ],
        //             [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => 'السنة الدراسية'
        //             ],
        //             [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => 'الفصل الدراسي'
        //             ],
        //             [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => 'اعمال السنه'
        //             ],
        //             [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => 'التحريري'
        //             ],
        //             [
        //                 'col' => 1,
        //                 'row' => 1,
        //                 'text' => ' التقدير'
        //             ],
        //         ],
        //     ];

        //     $export_data = [];
        //     $i = 0;
        //     foreach ($regists as $regist) {
        //         $export_data[$i][] = $regist->student_code;
        //         $export_data[$i][] = $regist->name;
        //         $export_data[$i][] = $regist->course_code;
        //         $export_data[$i][] = $regist->year;
        //         $export_data[$i][] = $regist->semester;
        //         $export_data[$i][] = $regist->yearly_performance_score;
        //         $export_data[$i][] = $regist->written;
        //         $export_data[$i][] = $regist->grade;
        //         $i++;
        //     }
        //     try {
        //         return Excel::download(new ReportsExport([], $headers, $export_data),
        //             'كشف الطلاب المسجلين في مقرر '
        //             . ' عام ' . str_replace('/', '-', $data['year']) . ' ' . $data['course_code'] . '.xlsx');
        //     } catch (Exception $e) {
        //         return redirect()->back()->withErrors('خطأ في الإتصال');
        //     }
        //   }




}







