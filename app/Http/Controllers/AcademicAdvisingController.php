<?php

namespace App\Http\Controllers;

use App\Http\Traits\DataTrait;
use App\Http\Traits\StudentTrait;
use App\Http\Traits\FinanceTrait;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentAffairsController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use  Carbon\Carbon;
use App\Exports\ReportsExport;

use Excel;
class AcademicAdvisingController extends Controller
{
    use DataTrait, StudentTrait,FinanceTrait;

    public function confirmationIndex(Request $request)
    {
        $items_per_pages = 50;
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $sort = DB::table('registration_semester')->where('year', $year)->where('semester', $semester)
            ->join('students', 'students.username', '=', 'registration_semester.student_code')
            ->where('academic_advisor', auth()->id())->orderBy('guidance', 'DESC')
            ->orderBy('student_code')->get()->pluck('student_code')->toArray();
        $orderedIds = implode('\',\'', $sort);
        $students = Student::select('students.name', 'students.username', 'cgpa', 'earned_hours', 'total_hours'
            , 'studying_status', 'student_classification', 'specialization', 'study_group')
            ->where('academic_advisor', auth()->id())
            ->orderByRaw(DB::raw('FIELD(username,\'' . $orderedIds . '\') DESC'))
            ->orderByRaw('SUBSTRING(username, 2, 6)')->paginate($items_per_pages);
        if (isset($request->search)) {
            $students = Student::select('students.name', 'students.username', 'cgpa', 'earned_hours', 'total_hours'
                , 'studying_status', 'student_classification', 'specialization', 'study_group')
                ->where('academic_advisor', auth()->id())
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
        $keys = ['الاسم', 'Code', 'المعدل التراكمي للدرجات', 'الساعات المكتسبة', 'إجمالي الساعات المسجلة', 'الحالة الدراسية', 'تصنيف الطلاب',
            'التخصص', 'الفرقة الدراسية'];
        $status = [];
        foreach ($students->pluck('username')->toArray() as $username) {
            $status[$username] = $this->checkGuidance($username);
        }
        $edit = 'show.registration';
        $delete = 'registrations.delete';
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
                    if ($this->getStudentInfo($value)['academic_advisor'] != auth()->id())
                        $fail('');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->back()->with('error', "تسجيل الطالب  $username غير موجود او ان البيانات غير صحيحة");
        }
        if ($this->checkPayment($username, $year, $semester)) {
            return redirect()->back()->with('error', "لا يمكن حذف تسجيل الطالب $username");
        }
        if ($this->getTotalStudyPaid($username, $year, $semester) > 0) {
            return redirect()->back()->with('error', "لا يمكن حذف تسجيل الطالب $username");
        }
        try {
            DB::transaction(function () use ($semester, $year, $username) {
                DB::table('students_payments')->where([
                    ['student_code', '=', $username],
                    ['year', '=', $year],
                    ['semester', '=', $semester]
                ])->delete();
                DB::table('payment_tickets')->where([
                    ['student_code', '=', $username],
                    ['type', '=', 'دراسية'],
                    ['year', '=', $year],
                    ['semester', '=', $semester]
                ])->delete();
                DB::table('students_discounts')->where([
                    ['student_code', '=', $username],
                    ['type', '=', 'دراسية'],
                    ['year', '=', $year],
                    ['semester', '=', $semester]
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
                $type = 'danger';
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
                    if ($this->getStudentInfo($value)['academic_advisor'] != auth()->id())
                        $fail('');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->route('registrations')
                ->with('error', "تسجيل الطالب  $username غير موجود او ان البيانات غير صحيحة");
        }
        $student = $this->getStudentInfo($username);
        $courses = $this->getStudentCourses($student);
        $previous_courses = collect($courses[2])->sortByDesc(['registration_year', 'registration_semester',
            'elective'])->groupBy(['registration_year', 'registration_semester'])->toArray();
        $registered_courses = $this->getRegisteredCourses($username, $year, $semester);
        return view('academic_advising.student_subjects', compact('courses', 'semester',
            'registered_courses','previous_courses', 'year', 'student'));
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
                    if ($this->getStudentInfo($value)['academic_advisor'] != auth()->id())
                        $fail('هذا الطالب ليس من صلاحياتك');
                    if ($this->checkGuidance($value))
                        $fail('تم تأكيد التسجيل من قبل');
                },
            ],
        ];
        $validator = Validator::make(['username' => $username], $rule);
        if ($validator->fails()) {
            return redirect()->route('registrations')->with('error', $validator->errors()->toArray()['username'][0]);
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
         $selected_courses = $this->getAllCourses(array_keys($data['guidance']));
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
        $data = $request->validate($rules);
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
            $previous_elective = $this->getElectiveCourses($student);
            $total_elective_bygroup = null;

            foreach ($previous_elective as $key => $value) {
               $search_key = strval($key);
                foreach ($merged_array as $merged_key => $merged_value) {
                    $merged_keys = explode('-', $merged_key);
                }
           }
            $selected_courses_elective = array_filter($all_courses, function ($item) {
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
                return redirect()->route('registrations')->with([
                    'success' => "تم تأكيد المواد المختارة للطالب $username بنجاح",
                    'error' => "تم حذف المواد الغير مختاره ايضاً"
                ]);
            } else {
                return redirect()->route('registrations')
                    ->with('success', "تم تأكيد المواد المختارة للطالب $username بنجاح");
            }
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }

    public function studentAlertIndex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['nullable', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if (!Student::where('username', $value)->where('academic_advisor', auth()->id())->exists())
                        $fail('هذا الكود غير صحيحة ' . $value);
                }
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->route('aa.student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if (!empty($data)) {
            $alerts = $this->getAlerts($data['username'], 'الارشاد الاكاديمى');
            return view('academic_advising.student_alerts', compact('alerts'));
        }
        return view('academic_advising.student_alerts');
    }

    public function studentAlert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'usernames' => ['required', 'regex:/^([RT][0-9]{6})((,)([RT][0-9]{6}))*$/u',
                function ($attribute, $value, $fail) {
                    $codes = explode(',', $value);
                    $arr = [];
                    foreach ($codes as $code) {
                        if (!Student::where('username', $code)->where('academic_advisor', auth()->id())->exists()) {
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
            return redirect()->route('aa.student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        try {
            $codes = explode(',', $data['usernames']);
            DB::transaction(function () use ($data, $codes) {
                foreach ($codes as $code) {
                    DB::table('students_alerts')->insert([
                        'student_code' => $code,
                        'category' => 'الارشاد الاكاديمى',
                        'reason' => $data['reason'],
                        'status' => $data['status'],
                        'created_by' => auth()->id(),
                    ]);
                }
            });
            return redirect()->back()->with('success', 'تم اضافة التنبيه بنجاح');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function deleteStudentAlert($student_code, Request $request)
    {
        $rule = [
            'student_code' => ['required', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                'exists:students_alerts,student_code',
                function ($attribute, $value, $fail) {
                    if (!Student::where('username', $value)->where('academic_advisor', auth()->id())->exists())
                        $fail('هذا الكود غير صحيحة ' . $value);
                }
            ]
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
                        ->where('student_code', $student_code)->where('category', 'الارشاد الاكاديمى')
                        ->exists())
                        $fail('البيانات غير صحيحة');
                }
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->route('aa.student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        try {
            $num = 0;
            DB::transaction(function () use ($data, $student_code, &$num) {
                $num = DB::table('students_alerts')->where('student_code', $student_code)
                    ->where('category', 'الارشاد الاكاديمى')
                    ->whereIn('id', array_keys($data['alert']))->delete();
            });
            if ($num > 1)
                return redirect()->route('aa.student.alerts')->with('success', 'تم حذف التنبيهات بنجاح');
            else
                return redirect()->route('aa.student.alerts')->with('success', 'تم حذف التنبيه بنجاح');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function studentRegisterIndex(Request $request)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $validator = Validator::make($request->all(), [
            'username' => ['nullable', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) use ($year, $semester) {
                    if ($this->getStudentInfo($value)['academic_advisor'] != auth()->id())
                        $fail('هذا الطالب ليس من صلاحياتك');
                    if ($this->registrationExists($value, $semester, $year))
                        $fail('هذا الطالب مسجل في الترم');
                }
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->route('student.register')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if (!empty($data)) {
            $student = $this->getStudentInfo($data['username']);
            $courses = $this->getStudentCourses($student);
            $previous_courses = collect($courses[2])->sortByDesc(['registration_year', 'registration_semester',
                'elective'])->groupBy(['registration_year', 'registration_semester'])->toArray();
            return view('academic_advising.student_register', compact('courses', 'semester',
                'year', 'student', 'previous_courses'));
        }
        return view('academic_advising.student_register');
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
        if (!$this->canAcademicRegistration()) {
            return redirect()->back()->with('error', 'لا يمكن التسجيل الان');
        }
        $rule = [
            'username' => ['required', 'exists:students,username',
                function ($attribute, $value, $fail) use ($year, $semester) {
                    if ($this->getStudentInfo($value)['academic_advisor'] != auth()->id())
                        $fail('هذا الطالب ليس من صلاحياتك');
                    if ($this->oldPaymentExists($value) or $this->oldPaymentExists($value, true))
                        $fail('يجب الانتهاء من مالية الطالب اولاً');
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
        $elective_counter = $this->getElectiveCourseCount();
        $previous_elective = $this->getElectiveCourses($student_code);
        // foreach ($previous_elective as $key => $value) {
        //     $elective_counter[$key] = $elective_counter[$key] - count($value);
        //     if ($elective_counter[1]+$elective_counter[2] < 0 and $elective_counter[3]+$elective_counter[4] < 0 and $elective_counter[5]+$elective_counter[6] < 0 and $elective_counter[7]+$elective_counter[8] < 0) {
        //         return redirect()->back()->with('error', 'خطأ في اللائحة')->withInput();
        //     }
        // }
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
            DB::table('registration')->insert($insert_courses);
            DB::table('registration_semester')->insert($reg_sem);
            $this->confirmStudentSemester($student_code, $year, $semester);
            DB::table('students_payments')->insert($payment_data);
            DB::commit();
            return redirect()->route('student.register')
                ->with('success', "تم تسجيل و تأكيد المواد المختارة للطالب $student_code بنجاح");
        } catch (Exception $ex) {
            dd($ex);
            DB::rollback();
            return redirect()->back()->with('error', 'خطأ في الإتصال');
        }
    }
                public function getRegistration()
        {
            $semester = $this->getCurrentSemester();
            $year = $this->getCurrentYear();
            if ($semester == '') {
                return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
            }
            $filter_data = $this->getDistinctValues('registration', ['course_code', 'year']);


            return view('control.registerations' , compact('filter_data'));
        }


        public function exportRegistration(Request $request){
            set_time_limit(0);
            $semester = $this->getCurrentSemester();
            $year = $this->getCurrentYear();
            if ($semester == '') {
                return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
            }
            $filter_data = $this->getDistinctValues('registration', ['course_code', 'year'],false);
            $data = $request->validate([
                'course_code' => 'required|in:' . implode(',', $filter_data['course_code']),
                'year' => 'required|in:' . implode(',', $filter_data['year']),
            ]);
           $regists =   DB::table('registration')->select(['registration.student_code','registration.course_code','registration.year',
          'registration.semester','registration.yearly_performance_score','registration.written','registration.grade', 'students.name'])
          ->where('registration.course_code', $data['course_code'])->where('year', $data['year'])
          ->join('students', function ($join) use ($data) {
            $join->on('students.username', '=', 'registration.student_code');
          })->get();
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
                        'text' => 'اسم الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود المقرر الدراسي'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'السنة الدراسية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'الفصل الدراسي'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'اعمال السنه'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'التحريري'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => ' التقدير'
                    ],
                ],
            ];

            $export_data = [];
            $i = 0;
            foreach ($regists as $regist) {
                $export_data[$i][] = $regist->student_code;
                $export_data[$i][] = $regist->name;
                $export_data[$i][] = $regist->course_code;
                $export_data[$i][] = $regist->year;
                $export_data[$i][] = $regist->semester;
                $export_data[$i][] = $regist->yearly_performance_score;
                $export_data[$i][] = $regist->written;
                $export_data[$i][] = $regist->grade;
                $i++;
            }
            try {
                return Excel::download(new ReportsExport([], $headers, $export_data),
                    'كشف الطلاب المسجلين في مقرر '
                    . ' عام ' . str_replace('/', '-', $data['year']) . ' ' . $data['course_code'] . '.xlsx');
            } catch (Exception $e) {
                return redirect()->back()->withErrors('خطأ في الإتصال');
            }
           }
}
