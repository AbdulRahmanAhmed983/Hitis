<?php

namespace App\Http\Controllers;

use App\Http\Traits\DataTrait;
use App\Http\Traits\MoodleTrait;
use App\Http\Traits\StudentTrait;
use App\Http\Traits\UserTrait;
use App\Models\Registeration_semester;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SmartIDImport;
use App\Models\SmartID;
use App\Imports\PaymentsAdministrativeExpensesImport;

class StudentAffairsController extends Controller
{
    use DataTrait, StudentTrait, UserTrait, MoodleTrait;

    public function index()
    {

        $data = $this->getStudentData();
        return view('student_affairs.student_registration')->with('data', $data);
    }

    public function getLastUsername(Request $request)
    {
        if ($request->ajax()) {
            if ($request->specialization === 'سياحة') {
                $output = $this->getNewUsername('T');
            } else if ($request->specialization === 'ترميم الاثار و المقتنيات الفنية') {
                $output = $this->getNewUsername('R');
            } else {
                return Response('خطاء فى البيانات', 400);
            }
            return Response($output, 200);
        }
        abort(404);
    }

    public function addStudent(Request $request)
    {
      //  dd($request->all());

        $validation_data = $this->getStudentData();
        $rules = [
            'name' => ['required', 'string', 'min:8', 'max:70', 'regex:/^[\x{0621}-\x{064A} ]+$/u',
                function ($attribute, $value, $fail) {
                    if (substr_count($value, ' ') < 2) {
                        $fail("يجب أن يحتوي الحقل $attribute على ثلاثة أسماء أو أكثر");
                    }
                }],
            //   'national_id' =>
            //     'required|digits:14|unique:students,national_id|unique:deleted_students,national_id',

            'passport_id' => (($request->nationality != "مصري") ?
                'required|string|regex:/^[a-zA-Z]{1}[0-9]{8,10}$/u|unique:students,national_id|unique:deleted_students,national_id' :
                'nullable|regex:/^$/i'),
            // 'certificate_obtained' => ['nullable', Rule::in($validation_data['certificate_obtained'])],
            // 'other_certificate_obtained' => (($request->certificate_obtained === "شهاده معادله") ? ['nullable',
            //     'string', 'max:70', 'regex:/^[\x{0621}-\x{064A}a-zA-Z0-9٠-٩ \(\)\-]+$/u',
            //     function ($attribute, $value, $fail) use ($validation_data) {
            //         if (in_array($this->removeArabicChar($value), $validation_data['certificate_obtained'])) {
            //             $fail('برجاء إدخال اسم الشهاده بشكل صحيح');
            //         }
            //     }
            // ] : 'nullable|regex:/^$/i'),
            //'nationality' => ['nullable', Rule::in($validation_data['nationality'])],
            // 'mobile' => 'required|digits:11|regex:/^(01)[0125]/|unique:students,mobile|unique:users,mobile|
            // unique:deleted_students,mobile|unique:deleted_users,mobile',
            'student_classification' => ['required', Rule::in($validation_data['student_classification'])],
            'classification_notes' => ((in_array($request->student_classification, ['عذر', 'وقف قيد'])) ?
                'nullable|string|max:255|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \/\-+*.\(\)\&,]+$/u' : 'nullable|regex:/^$/i'),
            'apply_classification' => ['required', Rule::in($validation_data['apply_classification'])],
            'apply_classification_notes' => ($request->apply_classification !== 'مرشح') ?
                'nullable|string|max:255|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \/\-+*.\(\)\&,]+$/u' : 'nullable|regex:/^$/i',
            'study_group' => ['required', Rule::in($validation_data['study_group'])],
            'departments_id' => ['required','integer',
            function ($attribute, $value, $fail) use ($request) {
                $departments_Ar = ['ترميم الأثار والمقتنيات الفنية غيرالعضوية',' الأثار والمقتنيات الفنية العضوية'];
                $departments_En = ['Marketing and E-Commerce','Accounting and Review','Business information systems'];
                if ($request->specialization == 'سياحة' && in_array($value, $departments_Ar)){
                    $fail('يوجد خطأ في اختيار التخصص او الشعبة ');
                }
                elseif ($request->specialization == 'ترميم الاثار و المقتنيات الفنية' && in_array($value, $departments_En)){
                    $fail('يوجد خطأ في اختيار التخصص او الشعبة ');
                }

            }],
            'specialization' => ['required', Rule::in($validation_data['specialization']),],
            'studying_status' => ['required', Rule::in($validation_data['studying_status'])],
             'notes' => 'nullable|string|max:255|regex:/^[^<>#;*]+$/u',
            'username' => 'required|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|unique:students,username|
            unique:users,username|unique:deleted_students,username|in:' .
                ($request->specialization === 'ترميم الاثار و المقتنيات الفنية' ? $this->getNewUsername('R') :
                    ($request->specialization === 'سياحة' ? $this->getNewUsername('T') : 'no')),
           'password' => 'required|string|min:8|max:8|regex:/^[A-Z0-9]{8}$/u',
        ];
        $data = $request->validate($rules);
        // if ($data['certificate_obtained'] == 'شهاده معادله') {
        //     $data['certificate_obtained'] = $this->removeArabicChar($data['other_certificate_obtained']);
        // }
       // unset($data['other_certificate_obtained']);
        // if ($data['nationality'] == 'أخرى') {
        //     $data['nationality'] = $this->removeArabicChar($data['other_nationality']);
        // }
        // unset($data['passport_id']);
        // unset($data['other_birth_country']);
        // if (isset($data['passport_id'])) {
        //     $data['national_id'] = $data['passport_id'];
        // }
        $data['name'] = $this->removeArabicChar($data['name']);
        if ($this->getCurrentSemester() == 'ترم صيفي') {
            $data['registration_date'] = explode('/', $this->getNextYear())[1];
        } else {
            $data['registration_date'] = explode('/', $this->getCurrentYear())[1];
        }
        $user_data['name'] = $data['name'];
        //$user_data['mobile'] = $data['mobile'];
        $user_data['username'] = $registration_data['student_code'] = $data['username'];
        $user_data['created_by'] = $data['created_by'] = auth()->id();
        $user_data['password'] = Hash::make($data['password']);
        $user_data['password_status'] = 1;
        $user_data['role'] = 'student';
        if ($this->getCurrentSemester() == 'ترم صيفي') {
            $registration_data['year'] = $this->getNextYear();
        } else {
            $registration_data['year'] = $this->getCurrentYear();
        }
        $registration_data['study_group'] = $data['study_group'];
        $registration_data['studying_status'] = $data['studying_status'];
        $data['academic_advisor'] = 'doctor';

        try {
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $fileName = $request->username . '-' . $data['registration_date'] . '.' . $photo->extension();
                $photo->move(storage_path('app/public/uploads/photos/students/'), $fileName);
                $data['photo'] = 'uploads/photos/students/' . $fileName;
            } elseif (isset($data['photo']) and is_string($data['photo'])) {
                $fileName = $request->username . '-' . $data['registration_date'] . '.jpg';
                file_put_contents(storage_path('app/public/uploads/photos/students/' . $fileName),
                    file_get_contents($data['photo']));
                $data['photo'] = 'uploads/photos/students/' . $fileName;
            }
            DB::transaction(function () use ($registration_data, $user_data, $data) {
                Student::create($data);
                User::create($user_data);
                DB::table('registration_years')->insert($registration_data);
                DB::table('students_current_warning')->insert(['student_code' => $data['username']]);
            });
            return redirect()->back()->with(['success' => 'تم اضافة الطالب بنجاح', 'data' => $data]);
        } catch (Exception $ex) {
            dd($ex);
            if (glob(storage_path('app/public/uploads/photos/students/' . $request->username . '-' .
                    $data['registration_date'] . '.*')) != []) {
                File::delete(glob(storage_path('app/public/uploads/photos/students/' . $request->username . '-' .
                    $data['registration_date'] . '.*'))[0]);
            }
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function searchStudent(Request $request)
    {
        $rules = [
            'username' => 'nullable|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:users,username|exists:students,username',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('student.search')->withErrors($validator)->withInput();
        } else {
            if (!is_null($request->username)) {
                $student = Student::find($request->username)->getOriginal();
                $department = DB::table('departments')->select('name')->where('id', '=', $student['departments_id'])->pluck('name')[0];
                $exam_place = DB::table('exam_place')->where(['student_code' => $request->username,
                    'year' => $this->getCurrentYear(), 'semester' => $this->getCurrentSemester()])->first();
                return view('student_affairs.student_search', compact('student','department', 'exam_place'));
            }
            return view('student_affairs.student_search');
        }

    }
     public function searchAdministrative(Request $request)
    {
        $rules = [
            'student_code' => 'nullable|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:payments_administrative_expenses,student_code',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('search.administrative')->withErrors($validator)->withInput();
        }
        else {
            if (!is_null($request->username)) {

                $student = Student::find($request->username)->getOriginal();
                $payments_administrative = DB::table('payments_administrative_expenses')->where(['student_code' => $request->username,
                'year' => $this->getCurrentYear()])->first();
                return view('student_affairs.print_administrative', compact('student', 'payments_administrative'));
            }
            return view('student_affairs.administrative_search');
        }
    }

    public function addSession(Request $request)
    {
        if ($request->ajax()) {
            session()->flash($request->key, $request->value);
            return Response('success', 200);
        }
        abort(404);
    }

    public function printStudent()
    {
        if (session()->exists('student')) {
            $student = session('student');
            $department = DB::table('departments')->select('name')->where('id', '=', $student['departments_id'])->pluck('name')[0];

            return view('student_affairs.print_form')->with([
                'student' => $student,
                'department' => $department,
                'date' => date('d/m/Y')
            ]);
        } else {
            return redirect('studentSearch');
        }
    }

    public function studentList(Request $request)
    {
        $filter_data = $this->getDistinctValues('students', ['immigrant_student', 'studying_status',
            'specialization', 'study_group', 'student_classification', 'father_profession', 'religion', 'gender',
            'certificate_obtained', 'birth_province', 'nationality', 'birth_country', 'certificate_obtained_date',
            'issuer_national_number', 'recruitment_area', 'military_education', 'registration_date',
            'apply_classification', 'enlistment_status','departments_id']);
        $filter_data['per_page'] = [25, 50, 100, 500, 1000, 'all'];
        $departments = DB::table('departments')->select('id','name')->get();
        $filter_data['grade'] = ['ممتاز', 'جيد جدا', 'جيد', 'مقبول', 'ضعيف'];
        $filter_data['academic_advisor'] = DB::table('academic_advisors')->select('username', 'name')
            ->get()->pluck('name', 'username')->toArray();
        $items_per_pages = empty($request->validate([
            'per_page' => 'nullable|in:' . implode(',', $filter_data['per_page']),
        ])) ? 50 : ($request->per_page == 'all' ? 99999999999 : $request->per_page);
        $data = array_filter($request->validate([
            'immigrant_student' => 'nullable|in:' . implode(',', $filter_data['immigrant_student']),
            'studying_status' => 'nullable|in:' . implode(',', $filter_data['studying_status']),
            'specialization' => 'nullable|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'nullable|in:' . implode(',', $filter_data['departments_id']),
            'study_group' => 'nullable|in:' . implode(',', $filter_data['study_group']),
            'student_classification' => 'nullable|in:' . implode(',', $filter_data['student_classification']),
            'father_profession' => 'nullable|in:' . implode(',', $filter_data['father_profession']),
            'religion' => 'nullable|in:' . implode(',', $filter_data['religion']),
            'gender' => 'nullable|in:' . implode(',', $filter_data['gender']),
            'certificate_obtained' => 'nullable|in:' . implode(',', $filter_data['certificate_obtained']),
            'birth_province' => 'nullable|in:' . implode(',', $filter_data['birth_province']),
            'nationality' => 'nullable|in:' . implode(',', $filter_data['nationality']),
            'birth_country' => 'nullable|in:' . implode(',', $filter_data['birth_country']),
            'certificate_obtained_date' => 'nullable|in:' . implode(',', $filter_data['certificate_obtained_date']),
            'issuer_national_number' => 'nullable|in:' . implode(',', $filter_data['issuer_national_number']),
            'academic_advisor' => 'nullable|in:' . implode(',', $filter_data['academic_advisor']),
            'recruitment_area' => 'nullable|in:' . implode(',', $filter_data['recruitment_area']),
            'military_education' => 'nullable|in:' . implode(',', $filter_data['military_education']),
            'grade' => 'nullable|in:' . implode(',', $filter_data['grade']),
            'registration_date' => 'nullable|in:' . implode(',', $filter_data['registration_date']),
            'apply_classification' => 'nullable|in:' . implode(',', $filter_data['apply_classification']),
            'enlistment_status' => 'nullable|in:' . implode(',', $filter_data['enlistment_status']),
        ]), function ($value) {
            return ($value !== null && $value !== '');
        });
        if (!empty($data) or !is_null($request->search)) {
            $request->validate([
                'search' => 'nullable|string|not_regex:/[#;<>]/u',
            ]);
            if (isset($data['academic_advisor'])) {
                $data['academic_advisor'] = array_search($data['academic_advisor'], $filter_data['academic_advisor']);
            }
            $g_flag = false;
            if (isset($data['grade'])) {
                $data += $this->gradeToCgpa($data['grade']);
                $data[] = ['total_hours', '>', 0];
                $g_flag = $data['grade'];
                unset($data['grade']);
            }
            $students = Student::where($data)
                ->whereRaw('CONCAT(`name`,"\0",`username`,"\0",`national_id`,"\0",`address`,"\0",`mobile`,
            "\0",COALESCE(`landline_phone`,\'\'),"\0",COALESCE(`parents_phone1`,\'\'),"\0",COALESCE(`parents_phone2`,\'\'),
            "\0",COALESCE(`email`,\'\'),"\0",COALESCE(`military_number`,\'\'),"\0",COALESCE(`apply_classification_notes`,\'\'),
            "\0",COALESCE(`position_of_recruitment`,\'\')
            ) LIKE ?', ['%' . $request->search . '%'])->paginate($items_per_pages);
            if (isset($data['academic_advisor'])) {
                $data['academic_advisor'] = $filter_data['academic_advisor'][$data['academic_advisor']];
            }
            if ($g_flag) {
                $data['grade'] = $g_flag;
            }
            $students->appends(['search' => $request->search]);
            $students->appends($data);
        } else {
            $students = Student::orderByRaw('SUBSTRING(username, 2, 6)')->paginate($items_per_pages);
        }
        if ($items_per_pages == 99999999999) {
            $items_per_pages = 'all';
        }
        $students->appends(['per_page' => $items_per_pages]);
        $request->validate([
            'page' => 'nullable|integer|between:1,' . $students->lastPage(),
        ]);
        $students->getCollection()->transform(function ($value) use ($filter_data) {
            if (!is_null($value->academic_advisor) && isset($filter_data['academic_advisor'][$value->academic_advisor])) {
                $value->academic_advisor = $filter_data['academic_advisor'][$value->academic_advisor];
                $value->departments_id   = DB::table('departments')->select('id','name')->where('id', '=', $value->departments_id)->pluck('name')[0];
            }
            return $value;
        });
        $hidden_keys = [2, 4, 5, 10, 15, 16, 21, 25, 29, 31, 36, 37, 39, 40, 41, 42, 43, 44, 45, 46, 47];
        $removed_keys = ['updated_by', 'created_by', 'photo', 'created_at', 'updated_at'];
        $keys = ['الاسم', 'كود الطالب', 'password', 'الرقم القومى', 'جهة الإصدار', 'إجمالي الساعات المسجله',
            'الساعات المكتسبة', 'المعدل التراكمي للدرجات', 'جنسية', 'دولة الميلاد', 'محافظة الميلاد', 'تاريخ الميلاد',
            'الشهادة الحاصل عليها', 'رقم جلوس الشهادة', 'مجموع الطالب %', 'درجات الطالب', 'إجمالي المجموع الدرجات',
            'درجة اللغه الإنجليزيه', 'تاريخ الحصول علي الشهادة', 'تاريخ قيد الطالب بالمعهد', 'تصنيف التقديم',
            'ملاحظات تصنيف التقديم', 'الجنس', 'الديانة', 'العنوان', 'التليفون الارضي', 'رقم الهاتف', 'مهنة ولي الامر',
            'تليفون ولي الامر الاول', 'تليفون ولي الامر الثاني', 'تصنيف الطلاب', 'ملاحظات التصنيف', 'الفرقة الدراسية',
            'التخصص','الشعبة', 'المرشد الاكاديمي', 'الحالة الدراسية', 'طالب وافد', 'البريد الالكترونى', 'بيانات اخري',
            'الرقم العسكري', 'منطقة التجنيد', 'حالة التجنيد', 'موقف الطالب من التجنيد', 'رقم القرار', 'تاريخ القرار',
            'تاريخ الانتهاء الاعفاء', 'ملاحظات التجنيد', 'التربيه العسكريه'];
        return view('student_affairs.students_list')->with([
            'students' => $students,
            'keys' => $keys,
            'primaryKey' => 'username',
            'removed_keys' => $removed_keys,
            'hidden_keys' => $hidden_keys,
            'search' => $request->search,
            'filter_data' => $filter_data,
            'items_per_pages' => $items_per_pages,
            'filter' => $data,
            'departments' => $departments,
        ]);
    }

    public function deleteStudent($username)
    {
        $rule = [
            'username' => ['required', 'regex:/^[RT][0-9]{6}$/u', 'exists:users,username', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if ($this->ticketExists($value, 1))
                        if (auth()->user()->role != 'owner')
                            $fail('لا يمكن حذف الطالب');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        $student = Student::find($username);
        $photo_path = $student->photo;
        $student->updated_by = auth()->id();
        $student->updated_at = Carbon::now();
        $user = User::find($username);
        try {
            if (file_exists(storage_path('app/public/' . $student->photo)) and $student->photo) {
                $path = storage_path('app/public/' . $student->photo);
                $photo_name = File::name($path) . '.' . File::extension($path);
                File::move($path, storage_path('app/public/uploads/photos/deleted/' . $photo_name));
                $student->photo = 'uploads/photos/deleted/' . $photo_name;
            }
            DB::transaction(function () use ($username, $user, $student) {
                DB::table('deleted_students')->insert($student->getAttributes());
                DB::table('registration_years')->where(['student_code' => $username,
                    'year' => $this->getCurrentYear()])->delete();
                $registration = DB::table('registration')->where(['student_code' => $username,
                    'year' => $this->getCurrentYear(), 'semester' => $this->getCurrentSemester()]);
                DB::table('deleted_registration')->insert($registration->get()->transform(function ($value) {
                    return (array)$value;
                })->toArray());
                $registration->delete();
                $student->delete();
                $user->delete();
            });
            return redirect()->back()->with('success', 'تم حذف الطالب ' . $student->name . ' كود ' . $student->username);
        } catch (Exception $ex) {
            $del_path = str_replace('students', 'deleted', $photo_path);
            if (glob(storage_path('app/public/' . $del_path)) != []) {
                File::move(storage_path('app/public/' . $del_path), storage_path('app/public/' . $photo_path));
            }
            return redirect()->back()->with('error', 'لم يتم حذف الطالب بسبب خطأ في الإتصال');
        }
    }

    public function changeStudentDataIndex($username)
    {
        $rule = [
            'username' => 'required|regex:/^[RT][0-9]{6}$/u|exists:users,username|exists:students,username',
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->route('student.list')->with('error', "اسم المستخدم $username غير موجود ");
        }
        $student = $this->getStudentInfo($username);
        $student['photo'] = $this->displayStudentPhoto($student['photo']);
        $data = $this->getStudentData();
        $student['passport_id'] = "";
        $student['other_certificate_obtained'] = "";
        $student['other_birth_country'] = "";
        $student['other_nationality'] = "";
        if (!in_array($student['certificate_obtained'], $data['certificate_obtained'])) {
            $student['other_certificate_obtained'] = $student['certificate_obtained'];
            $student['certificate_obtained'] = "شهاده معادله";
        }
        if (!in_array($student['birth_country'], $data['birth_country'])) {
            $student['other_birth_country'] = $student['birth_country'];
            $student['birth_country'] = "أخرى";
        }
        if (!in_array($student['nationality'], $data['nationality'])) {
            $student['other_nationality'] = $student['nationality'];
            $student['nationality'] = "أخرى";
        }
        if ($student['nationality'] != 'مصري') {
            $student['passport_id'] = $student['national_id'];
            $student['national_id'] = "";
        }
        if ($student['military_number']) {
            $student['military_number_1'] = explode('/', $student['military_number'])[2];
            $student['military_number_2'] = explode('/', $student['military_number'])[1];
            $student['military_number_3'] = explode('/', $student['military_number'])[0];
        } else {
            $student['military_number_1'] = $student['military_number_2'] = $student['military_number_3'] = null;
        }
        $departments = DB::table('departments')->get();
        unset($student['military_number']);
        $student['birth_date'] = date('Y-m-d', strtotime(str_replace("/", "-", $student['birth_date'])));
        return view('student_affairs.student_change_data', compact('student', 'data','departments'));
    }

    public function updateStudentData($username, Request $request)
    {
        $validation_data = $this->getStudentData();
        $validator = Validator::make(['username' => $username], [
            'username' => 'required|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:users,username|
            exists:students,username']);
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'خطأ في كود الطالب');
        }
        $student_info = $this->getStudentInfo($username);
        $rules = [
            'name' => ['required', 'string', 'min:8', 'max:70', 'regex:/^[\x{0621}-\x{064A} ]+$/u',
                function ($attribute, $value, $fail) {
                    if (substr_count($value, ' ') < 2) {
                        $fail("يجب أن يحتوي الحقل $attribute على ثلاثة أسماء أو أكثر");
                    }
                }],
            'national_id' => (($request->nationality === "مصري") ? ['required', 'digits:14',
                function ($attribute, $value, $fail) use ($request) {
                    if ($this->checkUniqueStudent($request->username, 'national_id', $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },] : 'nullable|regex:/^$/i'),
            'passport_id' => (($request->nationality != "مصري") ? ['required', 'string',
                'regex:/^[a-zA-Z]{1}[0-9]{8,10}$/u',
                function ($attribute, $value, $fail) use ($request) {
                    if ($this->checkUniqueStudent($request->username, 'national_id', $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },] : 'nullable|regex:/^$/i'),
            'issuer_national_number' => (($request->nationality === "مصري") ?
                'required|string|max:30|regex:/^[\x{0621}-\x{064A}0-9٠-٩ ]+$/u' : 'nullable|regex:/^$/i'),
            'certificate_obtained' => ['required', Rule::in($validation_data['certificate_obtained'])],
            'other_certificate_obtained' => (($request->certificate_obtained === "شهاده معادله") ? ['required',
                'string', 'max:70', 'regex:/^[\x{0621}-\x{064A}a-zA-Z0-9٠-٩ \(\)\-]+$/u',
                function ($attribute, $value, $fail) use ($validation_data) {
                    if (in_array($this->removeArabicChar($value), $validation_data['certificate_obtained'])) {
                        $fail('برجاء إدخال اسم الشهاده بشكل صحيح');
                    }
                }
            ] : 'nullable|regex:/^$/i'),
            // 'certificate_seating_number' => (($request->certificate_obtained != "شهاده معادله") ?
            //     'required|integer|digits_between:5,8' : 'nullable|integer|digits_between:5,8'),
            'certificate_obtained_date' => 'required|integer|digits:4|between:' . (date('Y') - 15) . ','
                . date('Y'),
            'nationality' => ['required', Rule::in($validation_data['nationality'])],
            'other_nationality' => (($request->nationality === "أخرى") ? ['required', 'regex:/^[\x{0621}-\x{064A} ]+$/u'
                , 'string', 'max:30',
                function ($attribute, $value, $fail) use ($validation_data) {
                    if (in_array($this->removeArabicChar($value), $validation_data['nationality'])) {
                        $fail('برجاء إدخال الجنسية بشكل صحيح');
                    }
                }
            ] : 'nullable|regex:/^$/i'),
            'birth_date' => 'required|date|before:-15 years',
            'birth_province' => (($request->birth_country === "مصر") ?
                ['required', Rule::in($validation_data['birth_province'])] : 'nullable|regex:/^$/i'),
            'birth_country' => ['required', Rule::in($validation_data['birth_country'])],
            'other_birth_country' => (($request->birth_country === "أخرى") ?
                'required|string|max:30|regex:/^[\x{0621}-\x{064A} ]+$/u' : 'nullable|regex:/^$/i'),
            'gender' => ['required', Rule::in(['ذكر', 'أنثى'])],
            'religion' => ['required', Rule::in($validation_data['religion'])],
            'address' => 'required|string|max:100|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \-\/ \(\),]+$/u',
            'landline_phone' => 'nullable|digits:9|regex:/^(0)/',
            'mobile' => ['required', 'digits:11', 'regex:/^(01)[0125]/',
                function ($attribute, $value, $fail) use ($request) {
                    if ($this->checkUniqueStudent($request->username, $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },
                function ($attribute, $value, $fail) use ($request) {
                    if ($this->checkUniqueUser($request->username, $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },],
            'father_profession' => 'nullable|string|max:150|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \(\)\-,]+$/u',
            'parents_phone1' => 'nullable|digits:11',
            'parents_phone2' => 'nullable|digits:11',
            'student_classification' => ['required', Rule::in($validation_data['student_classification'])],
            'classification_notes' => ((in_array($request->student_classification, ['عذر', 'وقف قيد'])) ?
                'nullable|string|max:255|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \/\-+*.\(\)\&,]+$/u' : 'nullable|regex:/^$/i'),
            'apply_classification' => ['required', Rule::in($validation_data['apply_classification'])],
            'apply_classification_notes' => ($request->apply_classification !== 'مرشح') ?
                'nullable|string|max:255|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \/\-+*.\(\)\&,]+$/u' : 'nullable|regex:/^$/i',
            'study_group' => ['required', Rule::in($validation_data['study_group'])],
            'specialization' => ['required', Rule::in($validation_data['specialization']),
                function ($attribute, $value, $fail) use ($request) {
                    if ($value == 'انجليزي') {
                        if (empty($request->english_degree) or $request->english_degree <
                            $this->getData(['english_degree'])['english_degree'][0]) {
                            $fail('درجة الانجليزي اصغر من الحد الادنى');
                        }
                    }
                }],
            'studying_status' => ['required', Rule::in($validation_data['studying_status'])],
            'immigrant_student' => (($request->nationality === "مصري") ? 'nullable|regex:/^$/i' : 'required|in:وافد'),
            'military_education' => 'nullable|in:معفي',
            'email' => ['nullable', 'email:rfc,dns', 'string', 'max:70', 'regex:/^[^\x{0621}-\x{064A}٠-٩ ]+$/u',
                function ($attribute, $value, $fail) use ($request) {
                    if ($this->checkUniqueStudent($request->username, $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },
                function ($attribute, $value, $fail) use ($request) {
                    if ($this->checkUniqueUser($request->username, $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },],
            'notes' => 'nullable|string|max:255|regex:/^[^<>#;*]+$/u',
            'username' => ['required', 'string', 'in:' . $username,
                function ($attribute, $value, $fail) {
                    if ($this->checkUniqueStudent($value, $attribute, $value, true))
                        $fail('قيمة حقل :attribute مُستخدمة من قبل.');
                },],
            'password' => 'required|string|min:8|max:16',
            'military_number' => function ($attribute, $value, $fail) use ($request) {
                if ($request->gender === "ذكر" and $request->nationality === "مصري") {
                    if (!is_array($value))
                        $fail('خطأ في الرقم العسكري');
                    if (!preg_match('/^[0-9]{1,5}$/u', $value[1]) or !preg_match('/^[0-9]{1,5}$/u',
                            $value[2]) or explode('-', $request->birth_date)[0] != $value[0])
                        $fail('خطأ في الرقم العسكري');
                    $military_number = $value[2] . '/' . $value[1] . '/' . $value[0];
                    if ($this->checkUniqueStudent($request->username, $attribute, $military_number, true))
                        $fail('تم تكرار الرقم العسكري من قبل');
                } else {
                    if (!empty($value))
                        $fail('خطأ في الرقم العسكري');
                }
            },
            'recruitment_area' => (($request->gender === "ذكر" and $request->nationality === "مصري") ?
                'required|string|max:50|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \(\)\-,]+$/u' : 'nullable|regex:/^$/i'),
            'enlistment_status' => (($request->gender === "ذكر" and $request->nationality === "مصري") ?
                'required|in:' . implode(',', $validation_data['enlistment_status']) : 'nullable|regex:/^$/i'),
            'position_of_recruitment' => (($request->gender === "ذكر" and $request->nationality === "مصري") ?
                'required|string|max:255|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \/\-.*+\(\)\&,]+$/u' : 'nullable|regex:/^$/i'),
            'decision_number' => (($request->gender === "ذكر" and $request->nationality === "مصري" and
                ($request->enlistment_status === "اعفاء مؤقت" or $request->enlistment_status === "اعفاء نهائي")) ?
                'required|digits_between:3,8' : 'nullable|regex:/^$/i'),
            'decision_date' => (($request->gender === "ذكر" and $request->nationality === "مصري" and
                ($request->enlistment_status === "اعفاء مؤقت" or $request->enlistment_status === "اعفاء نهائي")) ?
                'required|date|before_or_equal:today' : 'nullable|regex:/^$/i'),
            'expiry_date' => (($request->gender === "ذكر" and $request->nationality === "مصري" and
                $request->enlistment_status === "اعفاء مؤقت") ?
                'required|date|after:today' : 'nullable|regex:/^$/i'),
            'recruitment_notes' => (($request->gender === "ذكر" and $request->nationality === "مصري") ?
                'nullable|string|max:255|regex:/^[\x{0621}-\x{064A}0-9٠-٩ \/\-.*+\(\)\&,]+$/u' : 'nullable|regex:/^$/i'),
            'photo' => is_string($request->photo) ? 'nullable|starts_with:data:image/jpeg;base64|not_regex:/[#<>]/u' :
                'nullable|image|max:1024',

            'certificate_degree' => 'required|numeric|min:0|lte:certificate_degree_total',
            'certificate_degree_total' => 'required|numeric|min:0|gte:certificate_degree',
            'certificate_degree_percentage' => ['required', 'numeric', 'between:50,100',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value != round(($request->certificate_degree / $request->certificate_degree_total) * 100, 1))
                        $fail('خطأ في حساب المجموع');
                },
            ],
        ];
        $data = $request->all();
        if ($data['nationality'] == 'أخرى') {
            $data['nationality'] = $this->removeArabicChar($data['other_nationality']);
        }
        if ($data['certificate_obtained'] == 'شهاده معادله') {
            $data['certificate_obtained'] = $this->removeArabicChar($data['other_certificate_obtained']);
        }
        unset($data['other_certificate_obtained']);
        unset($data['other_nationality']);
        if ($data['birth_country'] == 'أخرى') {
            $data['birth_country'] = $this->removeArabicChar($data['other_birth_country']);
        }
        unset($data['other_birth_country']);
        if (isset($data['passport_id'])) {
            $data['national_id'] = $data['passport_id'];
        }
        unset($data['passport_id']);
        if ($data['gender'] == 'أنثى' or $data['nationality'] != 'مصري') {
            $data['military_number'] = null;
            $data['recruitment_area'] = null;
            $data['recruitment_notes'] = null;
            $data['military_education'] = null;
            $data['enlistment_status'] = null;
            $data['position_of_recruitment'] = null;
            $data['decision_number'] = null;
            $data['decision_date'] = null;
            $data['expiry_date'] = null;
        }
        if ($data['nationality'] == 'مصري' and $data['gender'] == 'ذكر') {
            $data['military_number'] = $data['military_number'][2] . '/' . $data['military_number'][1] . '/' .
                $data['military_number'][0];
        }
        if ($data['nationality'] != 'مصري') {
            $data['issuer_national_number'] = null;
        }
        if ($data['apply_classification'] == 'مرشح') {
            $data['apply_classification_notes'] = null;
        }
        if ($data['enlistment_status'] == 'له حق التأجيل') {
            $data['decision_number'] = null;
            $data['decision_date'] = null;
            $data['expiry_date'] = null;
        } elseif ($data['enlistment_status'] == 'اعفاء نهائي') {
            $data['expiry_date'] = null;
        }
        if ($data['nationality'] == 'مصري' and $data['gender'] == 'ذكر' and
            $student_info['military_education'] != 'مجتاز') {
            $data['military_education'] = 'غير متقدم';
        }
        if (!isset($data['classification_notes'])) {
            $data['classification_notes'] = null;
        }
        $data['apply_classification_notes'] = $this->removeArabicChar($data['apply_classification_notes']);
        $data['address'] = $this->removeArabicChar($data['address']);
        $data['recruitment_area'] = $this->removeArabicChar($data['recruitment_area']);
        $data['position_of_recruitment'] = $this->removeArabicChar($data['position_of_recruitment']);
        $data['recruitment_notes'] = $this->removeArabicChar($data['recruitment_notes']);
        $data['father_profession'] = $this->removeArabicChar($data['father_profession']);
        $data['issuer_national_number'] = $this->removeArabicChar($data['issuer_national_number']);
        $data['classification_notes'] = $this->removeArabicChar($data['classification_notes']);
        $data['notes'] = $this->removeArabicChar($data['notes']);
        $data['immigrant_student'] = isset($data['immigrant_student']) ? 'وافد' : 'غير وافد';
        $data['military_education'] = $request->has('military_education') ? 'معفي' : null;
        $data['name'] = $this->removeArabicChar($data['name']);
        $data['updated_at'] = Carbon::now();
        $user_data['name'] = $data['name'];
        $user_data['email'] = $data['email'];
        $user_data['mobile'] = $data['mobile'];
        $user_data['updated_by'] = $data['updated_by'] = auth()->id();
        $user_data['password'] = Hash::make($data['password']);
        $password_changed = $data['password'] != $student_info['password'];
        $registration_data = $registration_year = null;
        if ($student_info['study_group'] != $data['study_group']) {
            $registration_data['study_group'] = $data['study_group'];
            $registration_data['studying_status'] = $data['studying_status'];
            if ($this->getCurrentSemester() == 'ترم صيفي') {
                $registration_year = $this->getNextYear();
            } else {
                $registration_year = $this->getCurrentYear();
            }
        }
        if ($student_info['specialization'] != $data['specialization'] and
            !$this->canChangeSpecialization($student_info)) {
            return redirect()->back()->withErrors(['specialization' => 'لا يمكن تحويل التخصص']);
        }
        $old_photo = '';
        try {
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $fileName = $request->username . '-' . explode('/', $this->getCurrentYear())[1] . '.' . $photo->extension();
                $old_photo = str_replace('students', 'deleted', Student::find($username)->photo);
                if (!empty($old_photo) and File::exists(storage_path('app/public/' .
                        Student::find($username)->photo))) {
                    File::move(storage_path('app/public/' . Student::find($username)->photo),
                        storage_path('app/public/' . $old_photo));
                }
                $photo->move(storage_path('app/public/uploads/photos/students/'), $fileName);
                $data['photo'] = 'uploads/photos/students/' . $fileName;
            } elseif (isset($data['photo']) and is_string($data['photo'])) {
                $fileName = $request->username . '-' . explode('/', $this->getCurrentYear())[1] . '.jpg';
                $old_photo = str_replace('students', 'deleted', Student::find($username)->photo);
                if (!empty($old_photo) and File::exists(storage_path('app/public/' .
                        Student::find($username)->photo))) {
                    File::move(storage_path('app/public/' . Student::find($username)->photo),
                        storage_path('app/public/' . $old_photo));
                }
                file_put_contents(storage_path('app/public/uploads/photos/students/' . $fileName),
                    file_get_contents($data['photo']));
                $data['photo'] = 'uploads/photos/students/' . $fileName;
            }
            DB::transaction(function () use (
                $old_photo, $password_changed, $registration_year, $registration_data, $student_info, $user_data,
                &$data, $username
            ) {
                if ($student_info['specialization'] != $data['specialization'] and
                    $this->canChangeSpecialization($student_info)) {
                    $data['username'] = $user_data['username'] =
                        $this->changeStudentSpecialization($student_info['username'], $data['specialization']);
                }
                Student::find($username)->update($data);
                User::find($username)->update($user_data);
                if ($password_changed) {
                    $response = $this->updateMoodlePassword($username, $data['password']);
                   // $response_book = $this->updateMoodleBookPassword($username, $data['password']);
                    //if ($response == 'error' || $response_book == 'error')
                     if ($response == 'error' ){
                        abort(500);
                    }
                }
                if ($student_info['study_group'] != $data['study_group']) {
                    DB::table('registration_years')->where('student_code', $username)
                        ->where('year', $registration_year)->update($registration_data);
                }
                if (glob(storage_path('app/public/' . $old_photo)) != []) {
                    File::delete(storage_path('app/public/' . $old_photo));
                }
            });
            return redirect()->route('student.change.data', ['username' => $data['username']])
                ->with(['success' => 'تم تغير بيانات الطالب بنجاح', 'data' => $data]);
        } catch (Exception $ex) {
                    dd($ex);
            if (!empty($old_photo) and glob(storage_path('app/public/' . $old_photo)) != []) {
                if (glob(storage_path('app/public/uploads/photos/students/' . $request->username . '-' .
                        explode('/', $this->getCurrentYear())[1] . '.*')) != []) {
                    File::delete(glob(storage_path('app/public/uploads/photos/students/' .
                        $request->username . '-' . explode('/', $this->getCurrentYear())[1] . '.*'))[0]);
                }
                File::move(storage_path('app/public/' . $old_photo),
                    storage_path('app/public/' . Student::find($username)->photo));
            }
            if ($password_changed) {
                $this->updateMoodlePassword($username, $student_info['password']);
                //$this->updateMoodleBookPassword($username, $student_info['password']);
            }
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function studentStatus(Request $request)
    {
        $rules = [
            'username' => 'nullable|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:users,username|exists:students,username',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('student.status')->withErrors($validator)->withInput();
        } else {
            if (!is_null($request->username)) {
                $student[] = $this->getStudentInfo($request->username)['name'];
                $student[] = $request->username;
                [$registrations, $grades, $trans_courses] = $this->getStudentRegistrationStatus($request->username);
                $seating_numbers = DB::table('seating_numbers')->where('student_code', $request->username)
                    ->get()->groupBy(['year'])->toArray();
                return view('student_affairs.student_status',
                    compact('registrations', 'student', 'grades', 'trans_courses', 'seating_numbers'));
            }
            return view('student_affairs.student_status');
        }
    }

    public function searchStudentDataList(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'search' => ['nullable', 'string', 'not_regex:/[#;<>]/u',
                    function ($attribute, $value, $fail) {
                        if (!Student::whereRaw('CONCAT(`name`,"\0",`username`) LIKE ?', ['%' . $value . '%'])->exists()
                            or !User::whereRaw('CONCAT(`name`,"\0",`username`) LIKE ?', ['%' . $value . '%'])->exists())
                            $fail(' القيمة المحددة :attribute غير موجودة.');
                    },
                ],
            ]);
            if ($validator->fails()) {
                return Response('بيانات غير صحيحة', 400);
            }
            $students = Student::whereRaw('CONCAT(`name`,"\0",`username`) LIKE ?', ['%' . $request->search . '%'])
                ->select('username', 'name')->limit(10)->get();
            return Response($students, 200);
        }
        abort(404);
    }

    public function studentAlertIndex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'nullable|regex:/^[RT][0-9]{6}$/u|exists:students,username'
        ]);
        if ($validator->fails()) {
            return redirect()->route('student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if (!empty($data)) {
            $alerts = $this->getAlerts($data['username'], 'شئون الطلاب');
            return view('student_affairs.student_alerts', compact('alerts'));
        }
        return view('student_affairs.student_alerts');
    }

    public function studentAlert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usernames' => ['required', 'regex:/^([RT][0-9]{6})((,)([RT][0-9]{6}))*$/u',
                function ($attribute, $value, $fail) {
                    $codes = explode(',', $value);
                    $arr = [];
                    foreach ($codes as $code) {
                        if (is_null(Student::find($code))) {
                            $arr[] = $code;
                        }
                    }
                    if (!empty($arr))
                        $fail('هذه الاكواد غير صحيحة ' . implode(',', $arr));
                }
            ],
            'reason' => 'required|string|max:255|not_regex:/[#;<>]/u',
            'status' => 'required|in:danger,warning',
        ]);
        if ($validator->fails()) {
            return redirect()->route('student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        try {
            $codes = explode(',', $data['usernames']);
            DB::transaction(function () use ($data, $codes) {
                foreach ($codes as $code) {
                    DB::table('students_alerts')->insert([
                        'student_code' => $code,
                        'category' => 'شئون الطلاب',
                        'reason' => $data['reason'],
                        'status' => $data['status'],
                        'created_by' => auth()->id(),
                    ]);
                }
            });
            return redirect()->route('student.alerts')->with('success', 'تم اضافة التنبيه بنجاح');
        } catch (Exception $ex) {
            return redirect()->route('student.alerts')->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function deleteStudentAlert($student_code, Request $request)
    {
        $rule = [
            'student_code' => 'required|regex:/^[RT][0-9]{6}$/u|exists:students,username|
            exists:students_alerts,student_code',
        ];
        $validator = Validator::make(['student_code' => $student_code], $rule);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "البيانات غير صحيحة");
        }
        $validator = Validator::make($request->all(), [
            'alert' => 'required|array|min:1',
            'alert.*' => ['required', 'in:1',
                function ($attribute, $value, $fail) use ($student_code) {
                    if (!DB::table('students_alerts')->where('id', explode('.', $attribute)[1])
                        ->where('student_code', $student_code)->where('category', 'شئون الطلاب')
                        ->exists())
                        $fail('البيانات غير صحيحة');
                }
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->route('student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        try {
            $num = 0;
            DB::transaction(function () use ($data, $student_code, &$num) {
                $num = DB::table('students_alerts')->where('student_code', $student_code)
                    ->where('category', 'شئون الطلاب')->whereIn('id', array_keys($data['alert']))
                    ->delete();
            });
            if ($num > 1)
                return redirect()->route('student.alerts')->with('success', 'تم حذف التنبيهات بنجاح');
            else
                return redirect()->route('student.alerts')->with('success', 'تم حذف التنبيه بنجاح');
        } catch (Exception $ex) {
            return redirect()->route('student.alerts')->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function searchTransferStudentDataList(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'search' => ['nullable', 'string', 'not_regex:/[#;<>]/u',
                    function ($attribute, $value, $fail) {
                        if (!Student::whereRaw('CONCAT(`name`,"\0",`username`) LIKE ?', ['%' . $value . '%'])
                                ->whereIn('apply_classification', ['محول'])->exists()
                            or !User::whereRaw('CONCAT(`name`,"\0",`username`) LIKE ?', ['%' . $value . '%'])
                                ->exists())
                            $fail(' القيمة المحددة :attribute غير موجودة.');
                    },
                ],
            ]);
            if ($validator->fails()) {
                return Response('بيانات غير صحيحة', 400);
            }
            $students = Student::whereRaw('CONCAT(`name`,"\0",`username`) LIKE ?', ['%' . $request->search . '%'])
                ->whereIn('apply_classification', ['محول'])
                ->select('username', 'name')->limit(10)->get();
            return Response($students, 200);
        }
        abort(404);
    }

    public function addCoursesToTransferIndex(Request $request)
    {
        $rules = [
            'username' => ['nullable', 'string', 'min:7', 'max:7', 'regex:/^[RT][0-9]{6}$/u', 'exists:users,username',
                function ($attribute, $value, $fail) {
                    if (!Student::where('username', $value)
                        ->whereIn('apply_classification', ['محول'])->exists())
                        $fail(' القيمة المحددة :attribute غير موجودة.');
                },
            ],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('add.courses.transfer')->withErrors($validator)->withInput();
        }
            $students  =  $this->getStudentInfo($request->username);
        if (!is_null($request->username)) {
            return view('student_affairs.student_transfer_courses')->with(
                ['student' => $this->getStudentInfo($request->username),
                'departments' => $departments

                ]
            );
        }
        return view('student_affairs.student_transfer_courses');
    }

    public function addCoursesToTransfer(Request $request)
    {
         $year = $this->getCurrentYear();
        $student_code = $request->validate([
            'student_code' => ['required', 'string', 'regex:/^[RT][0-9]{6}$/u', 'exists:users,username',
                function ($attribute, $value, $fail) {
                    if (!Student::where('username', $value)
                        ->whereIn('apply_classification', ['محول'])->exists())
                        $fail(' القيمة المحددة :attribute غير موجودة.');
                },
            ],
        ])['student_code'];
        $student = $this->getStudentInfo($student_code);
        $data = $request->validate([
            'name' => 'required|in:' . $student['name'],
            'study_group' => 'required|in:' . $student['study_group'],
            'specialization' => 'required|in:' . $student['specialization'],
            // 'departments_id' => 'required|in:' . $student['departments_id'],
            'grades' => 'required|file|mimes:csv,xls,xlsx'
        ]);
        $grades = Excel::toArray(null, $data['grades'])[0];
        $courses = array_diff_key($grades, array_flip(["0"]));
        $validator = Validator::make(
            [
                'grades' => $grades,
                'courses' => $courses
            ],
            [
                'grades' => 'array|min:2',
                'grades.*' => 'array|size:2',
                'grades.*.*' => 'required|string',
                'grades.0.0' => 'required|in:course',
                'grades.0.1' => 'required|in:grade',
                'courses.*.0' => [
                    function ($attribute, $value, $fail) use ($student) {
                        if (!DB::table('courses')->where('type',
                            $student['specialization'] == 'ترميم الاثار و المقتنيات الفنية' ? 'R' :
                                ($student['specialization'] == 'سياحة' ? 'T' : ''))
                            ->where('full_code', $value)->exists())
                            $fail('كود الماده غير صحيح');
                    }
                ],
                'courses.*.1' => [
                    function ($attribute, $value, $fail) use ($student) {
                        if (!in_array($value, array_keys($this->gradeToPoint()[0])))
                            $fail('تقدير الماده غير صحيح');
                    }
                ],
            ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        try {
            DB::transaction(function () use ($courses, $student_code) {
                foreach ($courses as $course) {
                    $check_data = [
                        'student_code' => $student_code,
                        'course_code' => $course[0],
                    ];
                    $insert_data = [
                        'grade' => $course[1],
                    ];
                    DB::table('transferred_students_courses')->updateOrInsert($check_data, $insert_data);
                }
                 $level_hours = [
                        'الاولي' => [27, 'الثانية'],
                        'الثانية' => [60, 'الثالثة'],
                        'الثالثة' => [93, 'الرابعة'],
                        'الرابعة' => [132, 'خريج']
                    ];
                Student::find($student_code)->update($this->calculateCGPAStudent($student_code));
                 if($this->calculateCGPAStudent($student_code)['earned_hours'] >= $level_hours[$student['study_group']][0]){
                        $new_study_group = $level_hours[$student['study_group']][1];
                        DB::table('students')->where('username',$student_code)->update([
                            'study_group' => $new_study_group
                            ]);
                            DB::table('registration_years')->where('student_code',$student_code)->where('year',$year)->update([
                                        'study_group' => $new_study_group
                                ]);
                    }
            });
            return redirect()->route('add.courses.transfer')->with(['success' => 'تم تسجيل المواد بنجاح']);
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function printStatus()
    {
        $session = session()->get('courses');
        $student = $this->getStudentInfo($session['username']);
        $student['study_group'] = str_replace(['الاولي', 'الثانية', 'الثالثة', 'الرابعة'],
            ['الاول', 'الثانى', 'الثالث', 'الرابع'], $student['study_group']);
        $year = $session['year'];
        $courses = $session['courses'];
        $grades = $session['grades'];
        $note = DB::table('students_notes')->where('student_code', $student['username'])
            ->where('year', $year);
        $notes = $note->exists() ? $note->orderBy('semester')->get()->toArray() : null;
        $total_hour = 0;
        $total_earned_hour = 0;
        foreach ($grades as $item) {
            $total_hour += $item['hours'];
            $total_earned_hour += $item['earned_hours'];
        }
        return view('student_affairs.print_status', compact('student', 'year', 'courses',
            'grades', 'total_hour', 'total_earned_hour', 'notes'));
    }

  public function addExcuseIndex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_code' => ['nullable', 'string', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if (!$this->checkExcuse($value) and !$this->checkExcuse($value, null, 'year'))
                        $fail('ليس له اعذار هذه السنة الدراسية');
                },
            ],]);
        if ($validator->fails()) {
            return redirect()->route('add.excuses.index')->withErrors($validator)->withInput();
        }
        $data_filter = $this->getDistinctValues('registration_semester', ['year']);
        if (!is_null($request->student_code)) {
            $student_code = $request->student_code;
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $excuses = DB::table('students_excuses')->where('student_code', $student_code)
                ->orderBy('year')->orderBy('semester')->get()
                ->map(function ($value) use ($semester, $year) {
                    $value->remove = ($value->year == $year and
                        ($value->semester == $semester or ($value->semester == 'year' and $semester == 'ترم أول')));
                    return $value;
                })->toArray();
            $student = $this->getStudentInfo($student_code);
            return view('student_affairs.student_excuse', compact('student', 'excuses','data_filter'));
        }
        return view('student_affairs.student_excuse',compact('data_filter'));
    }

    public function getStudentRegisteredCourses(Request $request)
    {
        if ($request->ajax()) {
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $validator = Validator::make($request->all(), [
                'student_code' => ['required', 'string', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                    function ($attribute, $value, $fail) use ($year, $semester) {
                        if (!$this->registrationExists($value, $semester, $year))
                            $fail('الطالب لم يسجل هذا الترم');
                    },
                ],
            ]);
            if ($validator->fails()) {
                return Response($validator->errors()->messages()['student_code'][0], 400);
            }
            $student_code = $validator->validated()['student_code'];
            return Response($this->getRegisteredCourses($student_code, $year, $semester), 200);
        }
        abort(404);
    }

     public function addExcuse(Request $request)
    {
        $semester = $request->semester;
        if ($semester == 'ترم صيفي') {
            return redirect()->back()->with(['error' => 'لا يمكن وضع عذر فى الترم الصيفي']);
        }
        $year = $request->year;
        $check_year_semester = DB::table('change_status_details')->where('year', $request->year)->where('semester', $request->semester)->exists();
        if(!$check_year_semester){
             return redirect()->back()->with('error', 'لايمكن اضافة عذر مستقبلي')->withInput();
        }
        $student_code = $request->validate([
            'username' => ['required', 'string', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) use ($year) {
                    if ($this->checkExcuse($value, $year) or $this->checkExcuse($value, $year, 'year'))
                        $fail('لا يمكن وضع اكثر من عذر فى ترم واحد');
                },
            ],
        ])['username'];
        $courses = $this->getRegisteredCourses($student_code, $year, $semester);
        $data = $request->validate([
            'type' => 'required|in:عذر عن ماده او اكثر,عذر عن ترم,وقف قيد',
            'excuse' => 'required|string|max:255|not_regex:/[#;<>]/u',
            'courses' => 'required_if:type,عذر عن ماده او اكثر|exclude_unless:type,عذر عن ماده او اكثر|array|
            between:1,' . count($courses),
            'courses.*' => 'required_if:type,عذر عن ماده او اكثر|exclude_unless:type,عذر عن ماده او اكثر|
            in:' . implode(',', array_column($courses, 'full_code'))
        ]);
        if ($data['type'] == 'وقف قيد' and $semester != 'ترم أول') {
            return redirect()->back()->with('error', 'وقف القيد فى الترم الأول فقط')->withInput();
        }
        if (($data['type'] == 'وقف قيد' or $data['type'] == 'عذر عن ترم') and
            $this->registrationExists($student_code, $semester, $year)) {
            $grades = collect($courses)->pluck('grade', 'full_code')->toArray();
            foreach ($grades as $grade) {
                if ($grade != 'P') {
                    return redirect()->back()->withErrors(['courses' => 'لا يمكن وضع عذر لماده لها نتيجه'])->withInput();
                }
            }
        }
        if ($data['type'] == 'عذر عن ماده او اكثر') {
            $grades = collect($courses)->pluck('grade', 'full_code')->toArray();
            foreach ($data['courses'] as $code) {
                if ($grades[$code] != 'P') {
                    return redirect()->back()->withErrors(['courses' => 'لا يمكن وضع عذر لماده لها نتيجه'])->withInput();
                }
            }
        }
        try {
            DB::transaction(function () use ($student_code, $data, $year, $semester) {
                if ($data['type'] == 'عذر عن ماده او اكثر') {
                    DB::table('registration')->where([
                        ['student_code', $student_code], ['year', $year], ['semester', $semester]
                    ])->whereIn('course_code', $data['courses'])->update(['grade' => 'IC']);
                }
                if (($data['type'] == 'وقف قيد' or $data['type'] == 'عذر عن ترم') and
                    $this->registrationExists($student_code, $semester, $year)) {
                    DB::table('registration')->where([
                        ['student_code', $student_code], ['year', $year], ['semester', $semester],
                        ['grade', 'P']
                    ])->update(['grade' => 'IC']);
                }
                if ($data['type'] == 'وقف قيد') {
                    $semester = 'year';
                    DB::table('students')->where('username', $student_code)
                        ->update(['student_classification' => 'وقف قيد']);
                }
                if ($data['type'] == 'عذر عن ترم') {
                    DB::table('students')->where('username', $student_code)
                        ->update(['student_classification' => 'عذر']);
                }
                DB::table('students_excuses')->insert([
                    'student_code' => $student_code,
                    'year' => $year,
                    'semester' => $semester,
                    'type' => $data['type'],
                    'excuse' => $data['excuse']
                ]);
                $warning_threshold = (float)$this->getData(['warning_threshold'])['warning_threshold'][0];
                $data_results = $this->getStudentRegistrationStatus($student_code)[1];
                // add Not Restrict ////////////////////////////////
                      $warningIncrements = 0;
                      $skipFirstSemester = true;
                    //   dd($data_results);
                    $warning_threshold = (float) $this->getData(['warning_threshold'])['warning_threshold'][0];
                $data_results = $this->getStudentRegistrationStatus($student_code)[1];
                $warningIncrements = 0;
                $skipFirstSemester = true;

                 foreach ($data_results as $year => $semesters) {
    if ($year !== 'courses') {
        foreach ($semesters as $semester => $result_array) {
            if ($skipFirstSemester) {
                $skipFirstSemester = false;
                continue;
            }

            $restricted = !DB::table('students_excuses')
                ->where('student_code', $student_code)
                ->where('year', $year)
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
                ->where('student_code', $student_code)
                ->whereRaw('CONCAT(year, \'-\', semester) != ?', [$year . '-' . $semester])
                ->count();

            if ($restricted && $previous_semesters >= 1) {
                if ($result_array['cgpa'] < $warning_threshold && in_array($semester, ['ترم ثاني', 'ترم أول'])) {
                    $warningIncrements++;
                    DB::table('students_current_warning')
                        ->where('student_code', $student_code)
                        ->update(['warning' => $warningIncrements]);
                } elseif ($result_array['cgpa'] >= $warning_threshold && in_array($semester, ['ترم صيفي', 'ترم ثاني', 'ترم أول'])) {
                    $warningIncrements = 0;
                    DB::table('students_current_warning')
                        ->where('student_code', $student_code)
                        ->update(['warning' => $warningIncrements]);
                }
            }
        }
    }
}

            });
            return redirect()->back()->with(['success' => 'تم تسجيل العذر بنجاح']);
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function deleteStudentExcuse($student_code, $year, $semester)
    {
        $year = str_replace('-', '/', $year);
        $data = compact('student_code', 'year', 'semester');
        $validator = Validator::make($data, [
            'year' => 'required|in:' . $this->getCurrentYear(),
            'student_code' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if (!($data['semester'] == $this->getCurrentSemester() or ($data['semester'] == 'year' and
                $this->getCurrentSemester() == 'ترم أول'))) {
            return redirect()->back()->with('error', 'لا يمكن حذف العذر')->withInput();
        }
        try {
            DB::transaction(function () use ($data) {
                DB::table('students_excuses')->where($data)->delete();
                $data['semester'] = ($data['semester'] == 'year') ? 'ترم أول' : $data['semester'];
                DB::table('students')->where('username', $data['student_code'])
                    ->update(['student_classification' => 'مقيد']);
                DB::table('registration')->where($data)->where('grade', 'IC')
                    ->update(['grade' => 'P']);
            });
            return redirect()->route('add.excuses.index')->with(['success' => 'تم تسجيل العذر بنجاح']);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function createTicket(Request $request)
    {

        $rule = [
            'username' => ['nullable', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if ($this->ticketExists($value, 0, 'دراسية'))
                        return;
                    if (!$this->oldPaymentExists($value) and
                        $this->ticketSemesterExists($value, 1, 'دراسية'))
                        $fail('تم الإنتهاء من مالية الطالب ' . $value);
                }
            ],
        ];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return redirect()->route('create.ticket')->withErrors($validator->errors());
        } elseif (!is_null($request->username)) {
            $username = $request->username;
            if ($this->ticketExists($username, 0, 'دراسية')) {
                $student = $this->getStudentInfo($username);
                $ticket = DB::table('payment_tickets')->where('student_code', $username)
                    ->where('type', 'دراسية')->where('used', 0)->first();
                $payment = DB::table('students_payments')->where('student_code', $username)
                    ->where('year', $ticket->year)->where('semester', $ticket->semester)->first();
                $ticket_id = $ticket->ticket_id;
                $total_payment = $payment->payment;
                $total_discount = $this->getTotalStudyDiscount($username, $ticket->year, $ticket->semester);
                $wallet = (object)['withdrawn' => $payment->paid_payments];
                $last_pay = $ticket->amount;
                $has_ticket = true;
                $request->merge(['ticket_id' => $ticket_id]);
                $request->merge(['withdrawn' => $wallet->withdrawn]);
                $request->merge(['amount' => $ticket->amount]);

            } else {
                $student = $this->getStudentInfo($username);
                $payment = $this->getLastPayment($username);
                if (is_null($payment)) {
                    return redirect()->back()->with(['error' => 'تم الإنتهاء من مالية الطالب ' . $username]);
                }
                $wallet = $this->getStudentWallet($username);
                $year = $payment->year;
                $semester = $payment->semester;
                $total_payment = $this->getTotalStudyPay($username, $year, $semester);
                $total_discount = $this->getTotalStudyDiscount($username, $year, $semester);
                if (!is_null($wallet)) {
                    if ($wallet->amount >= $payment->payment) {
                        $wallet->withdrawn = $payment->payment;
                        $wallet->amount -= $payment->payment;
                        $payment->payment = 0;
                    } else {
                        $wallet->withdrawn = $wallet->amount;
                        $payment->payment -= $wallet->amount;
                        $wallet->amount = 0;
                    }
                } else {
                    $wallet = (object)['withdrawn' => 0, 'amount' => 0];
                }
                $ticket_id = date('ymdHis') . $username;
                $last_pay = $total_payment - $total_discount - $wallet->withdrawn - $payment->paid_payments;
                $has_ticket = false;
                $request->merge(['ticket_id' => $ticket_id]);
                $request->merge(['withdrawn' => $wallet->withdrawn]);
                $request->merge(['amount' => $last_pay]);
            }
         // return view('student_affairs.payment_ticket', compact('student', 'payment',
             //   'ticket_id', 'total_payment', 'total_discount', 'wallet', 'last_pay', 'has_ticket'));
        }
      //  return view('student_affairs.payment_ticket');
            $this->storeTicket($request,$request->username);
    }

    public function storeTicket(Request $request,$student_code)
    {

        // $ticket = DB::table('payment_tickets')->where('student_code', $student_code)
        // ->where('type', 'دراسية')->where('used', 0)->first();
        //  $payment = DB::table('students_payments')->where('student_code', $student_code)
        // ->where('year', $ticket->year)->where('semester', $ticket->semester)->first();

        $student =(object) $this->getStudentInfo($student_code);
        $payment = (object) $this->getLastPayment($student_code);
        $year = $payment->year;
        $semester = $payment->semester;
        $total_payment = $this->getTotalStudyPay($student_code, $year, $semester);
        $total_discount = $this->getTotalStudyDiscount($student_code, $year, $semester);
        $departments = DB::table('departments')->select('id','name')->where('id',$student->departments_id)->pluck('name')[0];
        $request->merge(['name' => $student->name]);
        $request->merge(['study_group' => $student->study_group]);
        $request->merge(['specialization' => $student->specialization]);
        $request->merge(['departments_id' => $departments]);
        $request->merge(['hours' => $payment->hours]);
        $request->merge(['payment' => $total_payment]);
        $request->merge(['date' =>  Carbon::now()]);
        $request->merge(['total_discount' => $total_discount]);
        $request->merge(['semester' => $year.'-'.$semester]);
        $request->merge(['used' => 1]);
        $validator = Validator::make($request->all(), ['student_code' => ['required', 'regex:/^[RT][0-9]{6}$/u',
            'exists:students,username',
            function ($attribute, $value, $fail) {
                if ($this->checkPayment($value))
                    $fail('تم الإنتهاء من مالية الطالب ' . $value);
            }, function ($attribute, $value, $fail) {
                if (!$this->oldPaymentExists($value) and ($this->ticketSemesterExists($value, 0, 'دراسية') or
                        $this->ticketSemesterExists($value, 1, 'دراسية')))
                    $fail('تم الإنتهاء من مالية الطالب ' . $value);
            }
        ],]);
        if ($validator->fails()){
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }

        $username = $validator->validated()['student_code'];
        if ($this->ticketExists($username, 0, 'دراسية')) {
            $s = $this->getStudentInfo($username);
            $student['name'] = $s['name'];
            $student['student_code'] = $username;
            $student['study_group'] = $s['study_group'];
            $student['specialization'] = $s['specialization'];
            $student['departments_id'] = $s['departments_id'];
            $ticket = DB::table('payment_tickets')->where('student_code', $username)
                ->where('type', 'دراسية')->where('used', 0)->first();
            $payment = DB::table('students_payments')->where('student_code', $username)
                ->where('year', $ticket->year)->where('semester', $ticket->semester)->first();
            $student['hours'] = $payment->hours;
            $student['ticket_id'] = $ticket->ticket_id;
            $student['date'] = $ticket->date;
            $student['semester'] = $ticket->year . '-' . $ticket->semester;
            $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
            $student['payment'] = $this->getTotalStudyPay($username, $ticket->year, $ticket->semester);
            $student['amount'] = $ticket->amount;
            $student['total_discount'] = $this->getTotalStudyDiscount($username, $ticket->year, $ticket->semester);
            $student['withdrawn'] = $payment->paid_payments;
            return view('student_affairs.print_ticket', compact('student','departments'));
        }
        $student = $this->getStudentInfo($username);
        $payment = $this->getLastPayment($username);
        $year = $payment->year;
        $semester = $payment->semester;
        $total_payment = $this->getTotalStudyPay($username, $year, $semester);
        $total_discount = $this->getTotalStudyDiscount($username, $year, $semester);
        $wallet = $this->getStudentWallet($username);
        if (!is_null($wallet)) {
            if ($wallet->amount >= $payment->payment) {
                $wallet->withdrawn = $payment->payment;
                $wallet->amount -= $payment->payment;
                $payment->payment = 0;
            } else {
                $wallet->withdrawn = $wallet->amount;
                $payment->payment -= $wallet->amount;
                $wallet->amount = 0;
            }
        } else {
            $wallet = (object)['withdrawn' => 0, 'amount' => 0];
        }
        $last_pay = $total_payment - $total_discount - $wallet->withdrawn - $payment->paid_payments;
        $rules = [
            'name' => 'required|in:' . $student['name'],
            'student_code' => 'required|in:' . $student['username'],
            'study_group' => 'required|in:' . $student['study_group'],
            'specialization' => 'required|in:' . $student['specialization'],
            'departments_id' => 'required|in:' . $student['departments_id'],
            'hours' => 'required|in:' . $payment->hours,
            'payment' => 'required|in:' . $total_payment,
            'total_discount' => 'required|in:' . $total_discount,
            'withdrawn' => 'required|in:' . $wallet->withdrawn,
            'ticket_id' => 'required|string|size:19|unique:payment_tickets,ticket_id|starts_with:' . date('ymdH')
                . '|ends_with:' . $student['username'],
            'date' => 'required|date|before_or_equal:now',
             'amount' => 'required|numeric|size:' . $payment->payment,
             'used' =>'required',
            'semester' => ['required', 'string', function ($attribute, $value, $fail) use ($payment) {
                $semester = explode('-', $value);
                if ($semester[0] != $payment->year or $semester[1] != $payment->semester) {
                    $fail('قيمة حقل :attribute غير صحيحه.');
                }
            },],
        ];
         $student = $data = $request->validate($rules);
        unset($data['name'], $data['study_group'], $data['specialization'], $data['hours'], $data['semester'],
            $data['payment'], $data['total_discount'], $data['withdrawn']);
        $data['year'] = explode('-', $student['semester'])[0];
        $data['semester'] = explode('-', $student['semester'])[1];
        $data['type'] = 'دراسية';
        $data['created_at'] = Carbon::now();
        $data['created_by'] = auth()->id();
        $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
        try {
            DB::transaction(function () use ($wallet, $data , $request) {
                if ($wallet->withdrawn > 0 or $wallet->amount > 0) {
                    DB::table('students_wallet')->where('student_code', $data['student_code'])
                        ->update(['amount' => $wallet->amount]);
                    DB::table('students_wallet_transaction')->insert([
                        'student_code' => $data['student_code'],
                        'year' => $data['year'],
                        'semester' => $data['semester'],
                        'amount' => $wallet->withdrawn,
                        'date' => Carbon::now(),
                        'type' => 'سحب',
                        'reason' => 'سحب مصاريف دراسية',
                    ]);
                }
                DB::table('students_payments')->where('student_code', $data['student_code'])
                    ->where('year', $data['year'])->where('semester', $data['semester'])
                    ->update(['paid_payments' => $wallet->withdrawn]);
                    $request->amount = $wallet->withdrawn;
                DB::table('payment_tickets')->insert($data);
                DB::table('payment_tickets')->where('ticket_id',$request->ticket_id)->update(['amount'=> $wallet->withdrawn]);
            });
            session()->flash('success', 'تم إنشاء حافظة بنجاح');
            return view('student_affairs.print_ticket', compact('student','departments'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }


    public function deleteTicket($ticket_id)
    {
        $username = substr($ticket_id, 12);
        $validator = Validator::make(['student_code' => $username, 'ticket_id' => $ticket_id],
            [
                'student_code' => 'required|regex:/^[RT][0-9]{6}$/u|exists:students,username',
                'ticket_id' => ['required', 'string', 'exists:payment_tickets,ticket_id',
                    function ($attribute, $value, $fail) use ($username) {
                        if (!$this->ticketExists($username, 0, 'دراسية', $value))
                            $fail("هذه الحافظه $value تم دفعها");
                    }]]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        $data = $validator->validated();
        try {
            DB::transaction(function () use ($data) {
                $ticket = DB::table('payment_tickets')->where($data);
                $payment = DB::table('students_payments')->where(['student_code' => $data['student_code'],
                    'year' => $ticket->first()->year, 'semester' => $ticket->first()->semester]);
                if ($payment->first()->paid_payments > 0) {
                    DB::table('students_wallet')->where('student_code', $data['student_code'])
                        ->increment('amount', $payment->first()->paid_payments);
                    DB::table('students_wallet_transaction')->insert([
                        'student_code' => $data['student_code'],
                        'year' => $ticket->first()->year,
                        'semester' => $ticket->first()->semester,
                        'amount' => $payment->first()->paid_payments,
                        'date' => Carbon::now(),
                        'type' => 'ايداع',
                        'reason' => 'استرجاع مصاريف دراسية من حذف الحافظة',
                    ]);
                    $payment->update(['paid_payments' => 0]);
                }
                $ticket->delete();
            });
            return redirect()->back()->with('success', 'تم حذف حافظة المصاريف الدراسية');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

        public function storeOtherTicket(Request $request)
    {
        $validator = Validator::make($request->all(), ['student_code' => ['required', 'regex:/^[RT][0-9]{6}$/u',
            'exists:students,username',
            function ($attribute, $value, $fail) {
                if ($this->ticketExists($value, 0, 'اخرى'))
                    return;
                if (!$this->oldPaymentExists($value, true))
                    $fail('تم الإنتهاء من مالية الطالب ' . $value);
            }]]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }
        $username = $validator->validated()['student_code'];
        $student = $this->getStudentInfo($username);
        if ($this->ticketExists($username, 0, 'اخرى')) {
            $ticket = DB::table('payment_tickets')->where('student_code', $username)
                ->where('type', 'اخرى')->where('used', 0)->first();
            $payment = DB::table('students_other_payments')->where('student_code', $username)
                ->where('year', $ticket->year)->where('semester', $ticket->semester)
                ->orderBy('id', 'desc')->first();
            $student['student_code'] = $username;
            $student['type'] = $payment->type;
            $student['ticket_id'] = $ticket->ticket_id;
            $student['date'] = $ticket->date;
            $student['semester'] = $ticket->year . '-' . $ticket->semester;
            $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);;
            $student['amount'] = $ticket->amount;
            $student['payment'] = $payment->payment;
            $student['withdrawn'] = $payment->paid_payments;
            return view('student_affairs.print_other_ticket', compact('student'));
        }
        $payment = $this->getLastOtherPayment($username);
        if (is_null($payment)) {
            return redirect()->back()->with(['error' => 'خطأ في الإتصال']);
        }
        $wallet = $this->getStudentWallet($username);
        $total_payment = $payment->payment;
        if (!is_null($wallet)) {
            if ($wallet->amount >= $payment->payment) {
                $wallet->withdrawn = $payment->payment;
                 //$wallet->amount -= $payment->payment;
                 $payment->payment =0;
            } else {
                $wallet->withdrawn = $wallet->amount;
                // $payment->payment -= $wallet->amount;
                 $wallet->amount = 0;
            }
        } else {
            $wallet = (object)['withdrawn' => 0, 'amount' => 0];
        }
        $rules = [
            'name' => 'required|in:' . $student['name'],
            'student_code' => 'required|in:' . $student['username'],
            'study_group' => 'required|in:' . $student['study_group'],
            'specialization' => 'required|in:' . $student['specialization'],
            'type' => 'required|in:' . $payment->type,
            'ticket_id' => 'required|string|size:19|unique:payment_tickets,ticket_id|starts_with:' . date('ymdH')
                . '|ends_with:' . $student['username'],
            'date' => 'required|date|before_or_equal:now',
            'payment' => 'required|numeric|size:' . $total_payment,
           // 'withdrawn' => 'required|numeric|size:' . $wallet->withdrawn,
            'amount' => 'required|numeric',
            'semester' => ['required', 'string', function ($attribute, $value, $fail) use ($payment) {
                $semester = explode('-', $value);
                if ($semester[0] != $payment->year or $semester[1] != $payment->semester) {
                    $fail('قيمة حقل :attribute غير صحيحه.');
                }
            },],
        ];
        $student = $data = $request->validate($rules);
        unset($data['name'], $data['study_group'], $data['specialization'], $data['hours'], $data['semester'],
            $data['withdrawn'], $data['payment']);
        $data['year'] = explode('-', $student['semester'])[0];
        $data['semester'] = explode('-', $student['semester'])[1];
        $data['type'] = 'اخرى';
        $data['created_at'] = Carbon::now();
        $data['created_by'] = auth()->id();
        $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
        try {
            if (!$this->ticketExists($username, 0, 'اخرى')) {
                DB::transaction(function () use ($payment, $student, $wallet, $data) {
                    DB::table('students_other_payments')->where('student_code', $data['student_code'])
                        ->where('year', $data['year'])->where('semester', $data['semester'])
                        ->where('id', $payment->id)
                        ->update(['paid_payments' => $wallet->withdrawn]);
                    DB::table('payment_tickets')->insert($data);
                    // if ($wallet->withdrawn > 0 or $wallet->amount > 0) {
                    //     DB::table('students_wallet')->where('student_code', $data['student_code'])
                    //         ->update(['amount' => $wallet->amount]);
                    //     DB::table('students_wallet_transaction')->insert([
                    //         'student_code' => $data['student_code'],
                    //         'year' => $data['year'],
                    //         'semester' => $data['semester'],
                    //         'amount' => $wallet->withdrawn,
                    //         'date' => Carbon::now(),
                    //         'type' => 'سحب',
                    //         'reason' => 'سحب مصاريف اخرى ' . $student['type'],
                    //     ]);
                    // }
                });
                session()->flash('success', 'تم إنشاء حافظة بنجاح');
            }
            return view('student_affairs.print_other_ticket', compact('student'));
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }
    public function addOtherPayment(Request $request)
    {
        $rule = [
            'username' => ['nullable', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if ($this->ticketExists($value, 0, 'اخرى'))
                        return;
                    if (!$this->oldPaymentExists($value, true))
                        $fail('تم الإنتهاء من مالية الطالب ' . $value);
                }
            ],
        ];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return redirect()->route('add.other.ticket')->withErrors($validator->errors());
        } elseif (!is_null($request->username)) {
            $username = $request->username;
            $student = $this->getStudentInfo($username);
            if ($this->ticketExists($username, 0, 'اخرى')) {
                $ticket = DB::table('payment_tickets')->where('student_code', $username)
                    ->where('type', 'اخرى')->where('used', 0)->first();
                $ticket_id = $ticket->ticket_id;
                $payment = DB::table('students_other_payments')->where('student_code', $username)
                    ->where('year', $ticket->year)->where('semester', $ticket->semester)
                    ->orderBy('id', 'desc')->first();
                $total_payment = $payment->payment;
                $wallet = (object)['withdrawn' => $payment->paid_payments];
                $last_pay = $total_payment - $wallet->withdrawn;
                return view('student_affairs.add_other_payment', compact('student', 'payment',
                    'ticket_id', 'total_payment', 'wallet', 'last_pay'));
            }
            $payment = $this->getLastOtherPayment($username);
            if (is_null($payment)) {
                return redirect()->to(url()->previous())->with(['error' => 'خطأ في الإتصال']);
            }
            $wallet = $this->getStudentWallet($username);
            $total_payment = $payment->payment;
            if (!is_null($wallet)) {
                if ($wallet->amount >= $payment->payment) {
                    $wallet->withdrawn = $payment->payment;
                    $wallet->amount -= $payment->payment;
                    $payment->payment = 0;
                } else {
                    $wallet->withdrawn = $wallet->amount;
                    $payment->payment -= $wallet->amount;
                    $wallet->amount = 0;
                }
            } else {
                $wallet = (object)['withdrawn' => 0, 'amount' => 0];
            }
            $ticket_id = date('ymdHis') . $username;
            $last_pay = $total_payment - $wallet->withdrawn - $payment->paid_payments;
            return view('student_affairs.add_other_payment', compact('student', 'payment',
                'ticket_id', 'total_payment', 'wallet', 'last_pay'));
        }
        return view('student_affairs.add_other_payment');
    }

    public function storeOtherPayment(Request $request)
    {
        $request->validate([
            'username' => ['required', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if ($this->oldPaymentExists($value, true))
                        $fail('يجب الانتهاء من الماليه السابقة');
                }, function ($attribute, $value, $fail) {
                    if ($this->ticketExists($value, 0, 'اخرى'))
                        $fail("هناك تذكرة للطالب $value لم يتم الإنتهاء منها بعد");
                }
            ],]);
        $data = $request->validate([
            'username' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
            'type' => 'required|string|max:255|not_regex:/[#;<>]/u',
            'payment' => 'required|numeric|min:1'
        ]);
        try {
            $data['year'] = $this->getCurrentYear();
            $data['semester'] = $this->getCurrentSemester();
            $data['student_code'] = $data['username'];
            unset($data['username']);
            DB::table('students_other_payments')->insert($data);
            return redirect()->route('add.other.ticket')->with('success', 'تم وضع المصاريف')->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function deleteOtherPayment($id)
    {
        $validator = Validator::make(['id' => $id], ['id' => 'required|integer|exists:students_other_payments,id']);
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'خطأ في البيانات')->withInput();
        }
        $payment_delete = DB::table('students_other_payments')->where('id', $id)->first();
        $payments = DB::table('students_other_payments')
            ->where('student_code', $payment_delete->student_code)
            ->where('year', $payment_delete->year)
            ->where('semester', $payment_delete->semester)
            ->orderBy('id', 'desc');
        $last_payment = DB::table('students_other_payments')
            ->where('student_code', $payment_delete->student_code)
            ->where('year', $payment_delete->year)
            ->where('semester', $payment_delete->semester)
            ->orderBy('id', 'desc')->first();
        if (is_null($last_payment) or $last_payment->id != $id) {
            return redirect()->back()->with('error', 'خطأ فى البيانات');
        }
        $tickets = DB::table('payment_tickets')
            ->where('student_code', $payment_delete->student_code)
            ->where('type', 'اخرى')->where('year', $payment_delete->year)
            ->where('semester', $payment_delete->semester)
            ->orderBy('created_at', 'desc');
        if ($tickets->get()->count() < $payments->get()->count()) {
            try {
                DB::transaction(function () use ($payment_delete, $id) {
                    // if ($payment_delete->paid_payments > 0) {
                    //     DB::table('students_wallet_transaction')->insert([
                    //         'student_code' => $payment_delete->student_code,
                    //         'year' => $payment_delete->year,
                    //         'semester' => $payment_delete->semester,
                    //         'amount' => $payment_delete->paid_payments,
                    //         'date' => Carbon::now(),
                    //         'type' => 'ايداع',
                    //         'reason' => 'استرجاع مصاريف اخرى',
                    //     ]);
                    //     DB::table('students_wallet')->where('student_code', $payment_delete->student_code)
                    //         ->increment('amount', $payment_delete->paid_payments);
                    // }
                    DB::table('students_other_payments')->where('id', $id)->delete();
                });
                return redirect()->route('add.other.ticket')->with('success', 'تم حذف المصاريف الاخرى');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
            }
        }
        $ticket = $tickets->first();
        $payment = $payments->first();
        if ($ticket->used == 0) {
            try {
                DB::transaction(function () use ($ticket, $payment, $id) {
                    // if ($payment->paid_payments > 0) {
                    //     DB::table('students_wallet_transaction')->insert([
                    //         'student_code' => $payment->student_code,
                    //         'year' => $payment->year,
                    //         'semester' => $payment->semester,
                    //         'amount' => $payment->paid_payments,
                    //         'date' => Carbon::now(),
                    //         'type' => 'ايداع',
                    //         'reason' => 'استرجاع مصاريف اخرى',
                    //     ]);
                    //     DB::table('students_wallet')->where('student_code', $payment->student_code)
                    //         ->increment('amount', $payment->paid_payments);
                    // }
                    DB::table('students_other_payments')->where('id', $id)->delete();
                    DB::table('payment_tickets')->where('ticket_id', $ticket->ticket_id)->delete();
                });
                return redirect()->route('add.other.ticket')->with('success', 'تم حذف المصاريف الاخرى');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
            }
        }
        return redirect()->back()->with('error', 'لا يمكن الحذف')->withInput();
    }
       public function createAdministrativeExpenses(Request $request){
        $rule = [
            'username' => 'nullable|regex:/^[RT][0-9]{6}$/u|exists:students,username',
        ];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }elseif (!is_null($request->username)) {
            $username = $request->username;
            $student = $this->getStudentInfo($username);
            $departments  = DB::table('departments')->select('id','name')->where('id',$student['departments_id'])->pluck('name')[0];
            $wallet_administrative_expenses = $this->getStudentAdministrativeExpenses($username);
            $payments_administrative_expenses = $this->getpaymentsAdministrativeExpenses($username);
            $amount = $payments_administrative_expenses[0] + $payments_administrative_expenses[1]+
            $payments_administrative_expenses[2] + $payments_administrative_expenses[3]+
            $payments_administrative_expenses[4] + $payments_administrative_expenses[5];
            $next_year = $this->getNextYear();
            $getExtraFees = $this->getExtrFees();
            $getDetailsFeesActive = $this->getDetailsFeesActive();
            $payments_extra_fees   = $this->getStudentExtraFees($username);
            $ticket_id = date('ymdHis') . $username;;
            $year = $this->getCurrentYear();
             return view('student_affairs.wallet_administrative-expenses', compact('student', 'year', 'amount','departments','next_year',
             'ticket_id','payments_administrative_expenses','wallet_administrative_expenses',
                'getExtraFees','payments_extra_fees','getDetailsFeesActive'));
        }
        return view('student_affairs.wallet_administrative-expenses');
    }
    public function getAmountExtraFees(Request $request){
        $type = $request->input('type');
        $amount = DB::table('extra_fees')->where('name_fees', $type)->value('amount');
          return response()->json(['amount' => $amount]);
    }
     public function storeExtraFees(Request $request){
        $validator = Validator::make($request->all(), ['student_code' => ['required', 'regex:/^[RT][0-9]{6}$/u',
        'exists:students,username']]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }
        $departments = DB::table('departments')->pluck('name')->toArray();
        $username = $validator->validated()['student_code'];
        $student = $this->getStudentInfo($username);
        $getDetailsFeesActive = $this->getDetailsFeesActive();
        $name_fees = array_map(function ($getDetailsFeesActive){
                    return $getDetailsFeesActive->name_fees;
        },$getDetailsFeesActive);
        $year = $this->getCurrentYear();
        $rules = [
            'name' => 'required|in:' . $student['name'],
            'student_code' => [
                'required','in:' . $student['username'],function($attr,$value,$fail) use($year){
                    $exists = DB::table('payments_extra_fees')
                    ->where('student_code', $value)
                    ->where('year', $year)
                    ->exists();
                    if($exists){
                        $fail('تم انشاء الحافظة لهذا الطالب من قبل');
                    }
                }
            ],
            'student_code' => 'required|in:' . $student['username'],
            'study_group' => 'required|in:' . $student['study_group'],
            'specialization' => 'required|in:' . $student['specialization'],
            'departments_id' => 'required|in:' . implode(',', $departments),
            'ticket_id' => 'required|string|size:19|unique:payments_extra_fees,ticket_id|starts_with:' . date('ymdH')
            . '|ends_with:' . $student['username'],
            'date' => 'required|date|before_or_equal:now',
            'amount' => 'required|numeric|between:0,15000',
            'type' => 'required|string|in:' . implode(',', $name_fees)
        ];
        $student = $data = $request->validate($rules);
        unset($data['name'], $data['study_group'], $data['specialization'], $data['departments_id']);
        $data['year'] = $year;
        $data['created_by'] = auth()->id();
        try {
            DB::table('payments_extra_fees')->insert($data);
            session()->flash('success', 'تم إنشاء حافظة بنجاح');
            return view('student_affairs.print_extra_fees', compact('student'));
        } catch (Exception $e) {
            dd($e);
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function storeAdministrativeExpenses(Request $request){
        $validator = Validator::make($request->all(), ['student_code' => ['required', 'regex:/^[RT][0-9]{6}$/u',
        'exists:students,username']]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }
        $username = $validator->validated()['student_code'];
        $student = $this->getStudentInfo($username);
        $year = $this->getCurrentYear();
        $next_year = $this->getNextYear();
        if($request->year == $next_year){
                $check_student = DB::table('registration_semester')->where('student_code',$username)->where('year',$year)->where('semester','ترم صيفي')->exists();
               return redirect()->back()->with('error', 'هذا الطالب له ترم صيفي')->withInput();
        }
        $rules = [
            'name' => 'required|in:' . $student['name'],
            'student_code' => [
                'required','in:' . $student['username'],function($attr,$value,$fail) use($year){
                    $exists = DB::table('payments_administrative_expenses')
                    ->where('student_code', $value)
                    ->where('year', $year)
                    ->exists();
                    if($exists){
                        $fail('تم انشاء الحافظة لهذا الطالب من قبل');
                    }
                }
            ],
            'student_code' => 'required|in:' . $student['username'],
            'study_group' => 'required|in:' . $student['study_group'],
            'specialization' => 'required|in:' . $student['specialization'],
            'ticket_id' => 'required|string|size:19|unique:payments_administrative_expenses,ticket_id|starts_with:' . date('ymdH')
            . '|ends_with:' . $student['username'],
            'date' => 'required|date|before_or_equal:now',
            'insurance' =>'required|numeric|between:0,15000',
            'profile_expenses' =>'required|numeric|between:0,15000',
            'registration_fees' =>'required|numeric|between:0,15000',
            'card_and_email' =>'required|numeric|between:0,15000',
            'renew_card_and_email' =>'required|numeric|between:0,15000',
            'military_expenses' =>'required|numeric|between:0,15000',
            'amount' => 'required|numeric|between:500,15000',
        ];
        $student = $data = $request->validate($rules);
        unset($data['name'], $data['study_group'], $data['specialization']);
        $data['year'] = $year;
        $data['created_by'] = auth()->id();
        try {
            DB::table('payments_administrative_expenses')->insert($data);
            session()->flash('success', 'تم إنشاء حافظة بنجاح');
            return view('student_affairs.print_wallet_administrative_expenses', compact('student'));
        } catch (Exception $e) {
           // dd($e);
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }


    public function createWalletTicket(Request $request)
    {
        $rule = [
            'username' => 'nullable|regex:/^[RT][0-9]{6}$/u|exists:students,username',
        ];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return redirect()->route('create.wallet.ticket')->withErrors($validator->errors());
        } elseif (!is_null($request->username)) {
            $username = $request->username;
            $wallet = $this->getStudentWallet($username);
            $student = $this->getStudentInfo($username);
            $departments = DB::table('departments')->select('id','name')->where('id',$student['departments_id'])->pluck('name')[0];
            if ($this->ticketExists($username, 0, 'محفظة')) {
                $student['student_code'] = $username;
                $ticket = DB::table('payment_tickets')->where('student_code', $username)
                    ->where('type', 'محفظة')->where('used', 0)->first();
                $student['ticket_id'] = $ticket->ticket_id;
                $student['date'] = $ticket->date;
                $student['semester'] = $ticket->year . '-' . $ticket->semester;
                $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
                $student['amount'] = $ticket->amount;
                return view('student_affairs.wallet_ticket', compact('student', 'wallet'));
            }
            $ticket_id = date('ymdHis') . $username;
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $transactions = DB::table('students_wallet_transaction')->where('student_code', $username)
                ->orderBy('date')->get()->toArray();
            return view('student_affairs.wallet_ticket', compact('student', 'year', 'semester',
                'ticket_id', 'wallet', 'transactions','departments'));
        }
        return view('student_affairs.wallet_ticket');
    }

    public function storeWalletTicket(Request $request)
    {
        $validator = Validator::make($request->all(), ['student_code' => ['required', 'regex:/^[RT][0-9]{6}$/u',
            'exists:students,username',
            function ($attribute, $value, $fail) {
                if ($this->ticketExists($value, 0, 'محفظة'))
                    return;
                if ($this->ticketExists($value))
                    $fail("هناك تذكرة للطالب $value لم يتم الإنتهاء منها بعد");
            },
        ]]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first())->withInput();
        }
        $username = $validator->validated()['student_code'];
        $student = $this->getStudentInfo($username);
        if ($this->ticketExists($username, 0, 'محفظة')) {
            $ticket = DB::table('payment_tickets')->where('student_code', $username)
                ->where('type', 'محفظة')->where('used', 0)->first();
            $student['student_code'] = $username;
            $student['ticket_id'] = $ticket->ticket_id;
            $student['date'] = $ticket->date;
            $student['semester'] = $ticket->year . '-' . $ticket->semester;
            $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
            $student['amount'] = $ticket->amount;
            $student['note'] = $ticket->note;
            $student['type'] = $ticket->type;
            return view('student_affairs.print_wallet_ticket', compact('student'));
        }
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $rules = [
            'name' => 'required|in:' . $student['name'],
            'student_code' => 'required|in:' . $student['username'],
            'study_group' => 'required|in:' . $student['study_group'],
            'specialization' => 'required|in:' . $student['specialization'],
            'ticket_id' => 'required|string|size:19|unique:payment_tickets,ticket_id|starts_with:' . date('ymdH')
                . '|ends_with:' . $student['username'],
            'date' => 'required|date|before_or_equal:now',
            'amount' => 'required|numeric|between:0.25,30000',
            'note' => 'nullable',
            'semester' => ['required', 'string', function ($attribute, $value, $fail) use ($semester, $year) {
                $sem = explode('-', $value);
                if ($sem[0] != $year or $sem[1] != $semester) {
                    $fail('قيمة حقل :attribute غير صحيحه.');
                }
            },],
        ];
        $student = $data = $request->validate($rules);
        unset($data['name'], $data['study_group'], $data['specialization'], $data['semester']);
        $data['year'] = explode('-', $student['semester'])[0];
        $data['semester'] = explode('-', $student['semester'])[1];
        $student['type'] = $data['type'] = 'محفظة';
        $student['note'] = $data['note'];
        $data['created_at'] = Carbon::now();
        $data['created_by'] = auth()->id();
        $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
        try {
            DB::table('payment_tickets')->insert($data);
            session()->flash('success', 'تم إنشاء حافظة بنجاح');
            return view('student_affairs.print_wallet_ticket', compact('student'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }


    public function deleteWalletTicket($student_code, $ticket_id)
    {
        $validator = Validator::make(compact('student_code', 'ticket_id'),
            [
                'student_code' => 'required|regex:/^[RT][0-9]{6}$/u|exists:students,username|
                exists:payment_tickets,student_code',
                'ticket_id' => 'required|string|size:19|exists:payment_tickets,ticket_id|ends_with:' . $student_code,
            ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        if (!$this->ticketExists($student_code, 0, 'محفظة', $ticket_id)) {
            return redirect()->back()->with('error', 'خطاء فى البيانات');
        }
        try {
            DB::table('payment_tickets')->where('ticket_id', $ticket_id)->delete();
            return redirect()->back()->with('success', 'تم حذف حافظة بنجاح')->withInput();
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function printStudentCardsIndex()
    {
        $data_filter = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        return view('student_affairs.students_card', compact('data_filter'));
    }

    public function printStudentCards(Request $request)
    {
        $action = $request->validate(['action' => 'required|in:student,students'])['action'];
        $type = $request->validate(['type' => 'required|in:normal,magnetic'])['type'];
        if ($action == 'student') {
            $usernames = $request->validate([
                'usernames' => 'required|array|distinct|min:1',
                'usernames.*' => 'required|string|size:7|exists:students,username',
            ])['usernames'];
            $students = DB::table('students')->whereIn('username', $usernames)
                ->get(['name', 'username', 'study_group', 'studying_status', 'specialization','departments_id', 'photo'])
                ->map(function ($value) {
                    $value->photo = $this->displayStudentPhoto($value->photo);
                    $value->has_photo = (!empty($value->photo));
                    $value->departments_id = DB::table('departments')->select('id','name')->where('id', '=', $value->departments_id)->pluck('name')[0];
                    return $value;
                });
        } else {
            $data_filter = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
            $data = $request->validate([
                'study_group' => 'required|string|in:' . implode(',', $data_filter['study_group']),
                'specialization' => 'required|string|in:' . implode(',', $data_filter['specialization']),
                'departments_id' => 'required|string|in:' . implode(',', $data_filter['departments_id']),
            ]);
            $count = $request->validate([
                'count' => ['required', function ($attribute, $value, $fail) {
                    $arr = explode('-', $value);
                    if (count($arr) != 2)
                        $fail('خطأ فى البيانات');
                    if (((int)$arr[0]) != $arr[0] or ((int)$arr[1]) != $arr[1])
                        $fail('خطأ فى البيانات');
                    if ((int)$arr[0] < 1 or (int)$arr[1] < 1)
                        $fail('خطأ فى البيانات');
                }],
            ])['count'];
            $index = explode('-', $count)[0];
            $students = DB::table('students')->where($data)->orderBy('username')
                ->get(['name', 'username', 'study_group', 'studying_status', 'specialization', 'departments_id','photo'])
                ->forPage($index, 200)->map(function ($value) {
                    $value->photo = $this->displayStudentPhoto($value->photo);
                    $value->has_photo = (!empty($value->photo));
                    $value->departments_id = DB::table('departments')->select('id','name')->where('id', '=', $value->departments_id)->pluck('name')[0];
                    return $value;
                });
        }
        $drop = $students->where('has_photo', false)->pluck('name', 'username')->toArray();
        $students = $students->where('has_photo', true)->toArray();
        $year = $this->getCurrentYear();
        if (empty($students)) {
            return redirect()->back()->with('no-img', $drop);
        }
        if (!empty($drop)) {
            session()->flash('no-img', $drop);
        }
        if ($type == 'normal') {
            return view('student_affairs.print_student_card', compact('students', 'year'));
        } else {
            return view('student_affairs.print_student_magnetic_card', compact('students', 'year'));
        }
    }

    public function checkStudentCards(Request $request)
    {
        if ($request->ajax()) {
            $data_filter = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
            $validator = Validator::make($request->all(), [
                'study_group' => 'required|string|in:' . implode(',', $data_filter['study_group']),
                'specialization' => 'required|string|in:' . implode(',', $data_filter['specialization']),
                //'departments_id' => 'required|string|in:' . implode(',', $data_filter['departments_id']),
            ]);
            if ($validator->fails()) {
                return Response()->json(['error' => 'بيانات غير صالحة'], 400);
            }
            $data = $validator->validate();
            $count = DB::table('students')->where($data)->count();
            $output = '';
            for ($i = 1; $i <= (int)($count / 200); $i++) {
                $output .= "<option>$i-200</option>";
            }
            $output .= "<option>$i-" . ($count % 200) . "</option>";
            return Response($output, 200);
        }
        abort(404);
    }

    public function printStudentSeatingNumberCardsIndex()
    {
        $data_filter = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $departments = DB::table('departments')->select('id','name')->pluck('name')[0];
        return view('student_affairs.student_seating_number_card', compact('data_filter','departments'));
    }

    public function printStudentSeatingNumberCards(Request $request)
    {
        $action = $request->validate(['action' => 'required|in:student,students'])['action'];
        $year = $this->getCurrentYear();
        if ($action == 'student') {
            $usernames = $request->validate([
                'usernames' => 'required|array|distinct|min:1',
                'usernames.*' => 'required|string|size:7|exists:students,username',
            ])['usernames'];
            $students = DB::table('students')->whereIn('username', $usernames)
                ->get(['name', 'username', 'study_group', 'studying_status', 'specialization','departments_id', 'photo']);
        } else {
            $data_filter = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
            $data = $request->validate([
                'study_group' => 'required|string|in:' . implode(',', $data_filter['study_group']),
                'specialization' => 'required|string|in:' . implode(',', $data_filter['specialization']),
                'departments_id' => 'required|string|in:' . implode(',', $data_filter['departments_id']),
            ]);
            $count = $request->validate([
                'count' => ['required', function ($attribute, $value, $fail) {
                    $arr = explode('-', $value);
                    if (count($arr) != 2)
                        $fail('خطأ فى البيانات');
                    if (((int)$arr[0]) != $arr[0] or ((int)$arr[1]) != $arr[1])
                        $fail('خطأ فى البيانات');
                    if ((int)$arr[0] < 1 or (int)$arr[1] < 1)
                        $fail('خطأ فى البيانات');
                }],
            ])['count'];
            $index = explode('-', $count)[0];
            $students = DB::table('students')->where($data)
                ->join('registration_semester', 'registration_semester.student_code', '=',
                    'students.username')->where(['year' => $year,
                    'semester' => $this->getCurrentSemester()])
                    ->orderBy('name')
                ->get(['name', 'username', 'study_group', 'studying_status', 'specialization','departments_id', 'photo'])
                ->forPage($index, 200);
        }
        $students = $students->map(function ($value) use ($year) {
            $value->photo = $this->displayStudentPhoto($value->photo);
            $value->has_photo = (!empty($value->photo));
            $value->departments_id = DB::table('departments')->select('id','name')->where('id', '=', $value->departments_id)->pluck('name')[0];
            $seating_number = DB::table('seating_numbers')->where(['student_code' => $value->username,
                'year' => $year]);
            $exam_place = DB::table('exam_place')->where(['student_code' => $value->username , 'year' => $year ,
            'semester' => $this->getCurrentSemester()])->first();
            $value->seating_number = $seating_number->exists() ? $seating_number->first()->seating_number : null;
            $value->exam_place = $exam_place ? $exam_place->place : null;
            return $value;
        });
        $drop_photo = $students->where('has_photo', false)->pluck('name', 'username')->toArray();
        $drop_seating_number = $students->whereNull('seating_number')->pluck('name', 'username')
            ->toArray();
        $students = array_values($students->where('has_photo', true)->whereNotNull('seating_number')
            ->toArray());
        if (empty($students)) {
            return redirect()->back()->with(['no-img' => $drop_photo, 'no-number' => $drop_seating_number]);
        }
        if (!empty($drop_photo)) {
            session()->flash('no-img', $drop_photo);
        }
        if (!empty($drop_seating_number)) {
            session()->flash('no-number', $drop_seating_number);
        }
        return view('student_affairs.print_student_seating_number_card', compact('students',
            'year'));
    }

    public function checkStudentSeatingNumberCards(Request $request)
    {
        if ($request->ajax()) {
            $data_filter = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
            $validator = Validator::make($request->all(), [
                'study_group' => 'required|string|in:' . implode(',', $data_filter['study_group']),
                'specialization' => 'required|string|in:' . implode(',', $data_filter['specialization']),
              //  'departments_id' => 'required|in:' . implode(',', $data_filter['departments_id']),
            ]);
            if ($validator->fails()) {
                return Response()->json(['error' => 'بيانات غير صالحة'], 400);
            }
            $data = $validator->validate();
            $count = DB::table('students')->where($data)
                ->join('registration_semester', 'registration_semester.student_code', '=',
                    'students.username')->where(['year' => $this->getCurrentYear(),
                    'semester' => $this->getCurrentSemester()])->count();
            $output = '';
            for ($i = 1; $i <= (int)($count / 200); $i++) {
                $output .= "<option>$i-200</option>";
            }
            $output .= "<option>$i-" . ($count % 200) . "</option>";
            return Response($output, 200);
        }
        abort(404);
    }

    public function examPlaceAndTimeIndex()
    {
        $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $departments = DB::table('departments')->select('id','name')->pluck('name')[0];
        return view('student_affairs.exam_place_time', compact('filter_data','departments'));
    }

    public function updateExamPlaces(Request $request)
    {
        $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $data = $request->validate([
            'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
            'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
            'places' => 'required|file|mimes:csv,xls,xlsx',
        ]);
        $places = Excel::toArray(null, $data['places'])[0];
        $validator = Validator::make(['places' => $places], [
            'places' => 'array|min:2',
            'places.*' => 'array|size:3',
            'places.0.0' => 'required|in:كود الطالب',
            'places.0.1' => 'required|in:رقم اللجنة,رقم اللجنه',
            'places.0.2' => 'required|in:رقم الجلوس',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->toArray());
        }
        switch ($data['study_group']) {
            case 'الاولي':
                $start_digit = 1;
                break;
            case 'الثانية':
                $start_digit = 2;
                break;
            case 'الثالثة':
                $start_digit = 3;
                break;
            case 'الرابعة':
                $start_digit = 4;
                break;
            default:
                $start_digit = 109101;
                break;
        }
        unset($places[0]);
        $validator = Validator::make(['places' => $places], [
            'places.*.0' => ['required', 'string', 'distinct', 'exists:students,username',
                function ($attribute, $value, $fail) use ($data) {
                    if (!DB::table('students')->where('study_group', $data['study_group'])
                        ->where('specialization', $data['specialization'])->where('departments_id',$data['departments_id'])
                    ->where('username', $value)
                        ->exists())
                        $fail("كود الطالب $value غير موجود");
                }],
            'places.*.1' => 'required|string|max:30',
            'places.*.2' => 'required|integer|digits:5|distinct|starts_with:' . $start_digit,
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->toArray());
        }
        try {
            DB::transaction(function () use ($places) {
                $year = $this->getCurrentYear();
                $semester = $this->getCurrentSemester();
                foreach ($places as $place) {
                    DB::table('exam_place')->updateOrInsert([
                        'student_code' => $place[0],
                        'year' => $year,
                        'semester' => $semester,
                    ], ['place' => $place[1]]);
                    DB::table('seating_numbers')->updateOrInsert([
                        'student_code' => $place[0],
                        'year' => $year,
                    ], ['seating_number' => $place[2]]);
                }
            });
            return redirect()->back()->with('success', 'تم رفع البيانات بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function updateExamTime(Request $request)
    {
        $data = $request->validate([
            'time' => 'required|file|mimes:csv,xls,xlsx',
        ]);
        $time = Excel::toArray(null, $data['time'])[0];
        $validator = Validator::make(['time' => $time], [
            'time' => 'array|min:2',
            'time.*' => 'array|size:3',
            'time.0.0' => 'required|in:كود المادة,كود الماده',
            'time.0.1' => 'required|in:التاريخ',
            'time.0.2' => 'required|in:المعاد',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->toArray());
        }
        unset($time[0]);
        $validator = Validator::make(['time' => $time], [
            'time.*.0' => ['required', 'string', 'distinct', 'exists:courses,full_code',
                function ($attribute, $value, $fail) use ($data) {
                    if (!DB::table('courses')->where('is_selected', 1)
                        ->where('full_code', $value)->exists())
                        $fail("كود المادة $value غير موجود");
                }],
            'time.*.1' => 'required|string',
            'time.*.2' => 'required|string|max:255|not_regex:/[#;<>]/u',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors()->toArray());
        }
        try {
            DB::transaction(function () use ($time) {
                $year = $this->getCurrentYear();
                $semester = $this->getCurrentSemester();
                foreach ($time as $t) {
                    //$date = Carbon::createFromFormat('j/n/Y', $t[1]);
                    DB::table('exam_table')->updateOrInsert([
                        'year' => $year,
                        'semester' => $semester,
                        'course_code' => $t[0],
                    ], ['exam_date' => $t[1], 'exam_time' => $t[2]]);
                }
            });
            return redirect()->back()->with('success', 'تم رفع البيانات بنجاح');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

        public function smartIdIndex(){
            $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
            $filter_data = array_merge($filter_data,
            $this->getDistinctValues('students_semesters', ['year', 'semester'], false));
            return view('student_affairs.smart_card',compact('filter_data'));
        }

        public function smartIdReport(Request $request){
            set_time_limit(0);
            $semester = $this->getCurrentSemester();
            $year = $this->getCurrentYear();
            if ($semester == '') {
                return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
            }
            $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization' ,'departments_id']);
            $data = $request->validate([
                'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
                'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
                'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
            ]);
            $students = Student::where($data)
            ->join('registration_semester', 'students.username', '=', 'registration_semester.student_code')
            ->where(['payment' => 1, 'registration_semester.year' => $year, 'registration_semester.semester' => $semester])
            ->join('smart_id', 'students.username', '=', 'smart_id.student_code')
            ->select('smart_id.student_code','smart_id.card_code')
            ->get()
            ->toArray();
            switch ($data['study_group']) {
                case 'الاولي':
                    $sem = [1, 2];
                    break;
                case 'الثانية':
                    $sem = [3, 4];
                    break;
                case 'الثالثة':
                    $sem = [5, 6];
                    break;
                default:
                    $sem = [7, 8];
                    break;
            }

            $type = $data['specialization'] == 'ترميم الاثار و المقتنيات الفنية' ? 'R' : 'T';

            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود الكارنيه'
                    ],
                ],
            ];

            $export_data = [];
            $i = 0;
            foreach ($students as $student) {
                $export_data[$i][] = $i + 1;
                $export_data[$i][] = $student['card_code'];
                $export_data[$i][] = $student['student_code'];
                $i++;
            }

            try {
                return Excel::download(
                    new ReportsExport([], $headers, $export_data),
                    'كشف كارنيهات الطلاب المسددين الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] . '.xlsx'
                );
            } catch (Exception $e) {
                return redirect()->back()->withErrors('خطأ في الإتصال');
            }
        }

        public function uploadSmartIdReport(Request $request){
                $validated = $request->validate([
                    'file' => 'required|file|mimes:csv,xlsx,xls'
                ]);
           Excel::import(new SmartIDImport,$request->file);
           return redirect()->back()->with('success', 'تم رفع البيانات بنجاح');
        }

        public function convertAdministraitve(){
        return view('student_affairs.convert_administrative');
    }

    public function storeconvertedAdministraitve(Request $request)
        {
            $request->validate([
                'student_code1' => 'required|exists:payments_administrative_expenses,student_code',
                'student_code' => 'required|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:students,username',
            ]);

            $oldCode = $request->input('student_code1');
            $newCode = $request->input('student_code');

            $codePayment = DB::table('payments_administrative_expenses')->where('student_code', $oldCode)->first();

            if (!$codePayment) {
                return redirect()->back()->with('error', 'خطا لايوجد هذا الطالب المراد استبداله.')->withInput();
            }

            DB::table('payments_administrative_expenses')->where('student_code', $oldCode)->update(['student_code' => $newCode]);

            return redirect()->back()->with('success', 'تم تغير الطالب بنجاح.')->withInput();
        }

        public function importAdministraitve(Request $request){
             $request->validate([
                'file' => 'required|mimes:xlsx,xls',
            ]);

            Excel::import(new PaymentsAdministrativeExpensesImport, $request->file('file'));

            return redirect()->back()->with('success', 'تم تغير الطالب بنجاح.')->withInput();
        }



}
