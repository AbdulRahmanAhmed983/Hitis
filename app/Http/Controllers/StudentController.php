<?php

namespace App\Http\Controllers;

use App\Http\Traits\DataTrait;
use App\Http\Traits\MoodleTrait;
use App\Http\Traits\StudentTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    use DataTrait, StudentTrait, MoodleTrait;

    public function subjectsRegistrationIndex()
    {
        $student_info = $this->getStudentInfo(auth()->id());
        $courses = $this->getStudentWithoutUpLevelCourses($student_info);
        $year = $this->getCurrentYear();
        $can_register = $this->canRegistration();
        $semester = $this->getCurrentSemester();
        $student_new = $this->studentIsNew();
        $student_semester = $this->getStudentCurrentSemester($student_info);
        // $can_summer = (count($courses[3]) >= 5 and $semester == 'ترم صيفي');
        $can_summer = false;
         $payment = ($this->oldPaymentExists(auth()->id()) or $this->oldPaymentExists(auth()->id(), true));
        $ticket = $this->ticketExists(auth()->id());
        $excuse = ($this->checkExcuse(auth()->id()) or $this->checkExcuse(auth()->id(), $year, 'year'));
        $academic = $this->getAcademicAdvisor($student_info['academic_advisor']);
        $mobile = str_contains(strtolower($_SERVER['HTTP_USER_AGENT']), 'android');
        return view('student.student_subjects_form', compact('student_info', 'courses', 'year',
            'can_register', 'payment','semester', 'academic', 'mobile', 'can_summer', 'excuse', 'ticket',
            'student_new', 'student_semester'));
    }

    public function subjectRegistration(Request $request)
    {
        if (!$this->canRegistration() or $this->oldPaymentExists(auth()->id()) or !$this->studentIsNew() or
            $this->oldPaymentExists(auth()->id(), true) or $this->ticketExists(auth()->id())) {
            return redirect()->route('student.new.subjects');
        }
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        if ($this->registrationExists(auth()->id(), $semester, $year)) {
            return redirect()->back()->with(['error' => 'تم التسجيل فى هذا الترم من قبل']);
        }
        $student_info = $this->getStudentInfo(auth()->id());
        $warning_value = DB::table('students_current_warning')
        ->where('student_code', $student_info['username'])
        ->value('warning');
         $half_load= $this->getData(['load_hours'])['load_hours'][0];
         $high_load= $this->getData(['load_hours'])['load_hours'][1];
         $cgpa_high_load= $this->getData(['load_hours'])['load_hours'][2];


        $courses = $this->getStudentCourses($student_info);
        $courses_code = [];
        $elective_counter = $this->getElectiveCourseCount();
        $previous_elective = $this->getElectiveCourses(auth()->id());
        // foreach ($previous_elective as $key => $value) {
        //     $elective_counter[$key] = $elective_counter[$key] - count($value);
        //     if ($elective_counter[$key] < -1) {
        //         return redirect()->back()->with('error', 'خطأ في اللائحة')->withInput();
        //     }
        //}
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
        if ($semester == 'ترم صيفي') {
            foreach ($courses[4] as $course) {
                $courses_code[$course->full_code] = $course->hours;
            }
        }
        $registration_hour =
            ($student['cgpa'] >= $cgpa_high_load || $student['study_group']=='الرابعة' ) ? $high_load  :
                $this->getStudentsRegistrationHour($student['specialization'], $student['study_group']);

        $rules = [
            'semester' => 'required|string|in:' . $semester,
            'year' => 'required|string|in:' . $year,
            'courses' => ['required', 'array',
                function ($attribute, $value, $fail) use (
                    $student_info, $courses, $semester, $registration_hour,$warning_value,
                    $courses_code
                ) {
                    $hour = 0;
                    if ($semester != 'ترم صيفي') {
                        if (count(array_intersect($value, array_column($courses[1], 'full_code'))) !=
                            count(array_column($courses[1], 'full_code'))) {
                            return $fail('تأكد من تسجيل المواد الغير مجتازه اولاً');
                        } else {
                            foreach (array_column($courses[1], 'full_code') as $item) {
                                unset($value[array_search($item, $value)]);
                            }
                            $value = array_values($value);
                            $hour += collect($courses[1])->sum('hours');
                        }
                    } else {
                        if (collect($courses[1])->sum('hours') <= $registration_hour) {
                            if (count(array_intersect($value, array_column($courses[1], 'full_code'))) ==
                                count(array_column($courses[1], 'full_code'))) {
                                foreach (array_intersect($value, array_column($courses[1], 'full_code')) as $course) {
                                    if (!in_array($course, array_column($courses[1], 'full_code'))) {
                                        return $fail('تأكد من تسجيل المواد الغير مجتازه اولاً');
                                    }
                                }
                            } else {
                                return $fail('تأكد من تسجيل المواد الغير مجتازه اولاً');
                            }
                        } else {
                            if (count(array_intersect($value, array_column($courses[1], 'full_code'))) <=
                                count($value)) {
                                foreach ($value as $course) {
                                    if (!in_array($course, array_column($courses[1], 'full_code'))) {
                                        return $fail('تأكد من تسجيل المواد الغير مجتازه اولاً');
                                    }
                                }
                            } else {
                                return $fail('تأكد من تسجيل المواد الغير مجتازه اولاً');
                            }
                        }
                        foreach (array_column($courses[1], 'full_code') as $item) {
                            if (is_numeric(array_search($item, $value))) {
                                unset($value[array_search($item, $value)]);
                                $hour += collect($courses[1])->where('full_code', $item)->first()->hours;
                                $value = array_values($value);
                            }
                        }
                    }
                    if ($semester == 'ترم صيفي') {
                        $courses_available = collect($courses[0])->merge($courses[4])
                            ->where('semester', '<=',
                                $this->getStudentCurrentSemester($student_info))->sortBy('elective');
                    } else {
                        $courses_available = collect($courses[0])->where('semester',
                            $this->getStudentCurrentSemester($student_info))->sortBy('elective');

                    }
                    foreach ($value as $item) {
                        if (!$courses_available->contains('full_code', $item)) {
                            $fail('تأكد من تسجيل مادة غير ' . $item . ' اولاً');
                        } else {
                            $hour += $courses_available->where('full_code', $item)->first()->hours;
                        }
                        if ($hour > $registration_hour) {
                            return $fail('يجب أن تسجل عدد اساعات اقل من او يساوى ' . $registration_hour);
                        }
                        if ($warningValue>=1 && $hour >  $half_load){
                            return redirect()->back()->with('error', 'لا يمكن التسجيل لقد تم تجاوز ' .  $half_load . ' الساعة');
                            }
                    }
                    if ($semester != 'ترم صيفي') {
                        if ($hour != $registration_hour) {
                            $fail('يجب أن تسجل ' . $registration_hour . ' ساعه دراسيه');
                        }
                    } else {
                        if ($hour > $registration_hour) {
                            return $fail('يجب أن تسجل عدد اساعات اقل من او يساوى ' . $registration_hour);
                        }
                    }
                }

            ],
       //|in:' . implode(',', array_keys($courses_code))
            'courses.*' => 'required|string|distinct',
        ];
        $data = $request->validate($rules);

        $data['username'] = auth()->id();
        $registration_data = [];
        foreach ($data['courses'] as $course_code) {
            $registration_data[] = [
                'student_code' => $data['username'],
                'course_code' => $course_code,
                'year' => $data['year'],
                'semester' => $data['semester'],
            ];
        }
        $registration_semester = $registration_data[0];
        unset($registration_semester['course_code']);
        $registration_semester['created_at'] = now();
        $registration_semester['created_by'] = $data['username'];
        try {
            DB::transaction(function () use ($registration_semester, $registration_data) {
                DB::table('registration')->insert($registration_data);
                DB::table('registration_semester')->insert($registration_semester);
            });
            return redirect()->back()->with(['success' => 'تم تسجيل المواد بنجاح']);
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => 'حدث خطأ فى الاتصال من فضلك حاول من جديد']);
        }
    }


    public function showRegistrationIndex()
    {
        $can_register = $this->canRegistration();
        $student_info = $this->getStudentInfo(auth()->id());
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $regis_exists = $this->registrationExists($student_info['username'], $semester, $year);
        if (!$regis_exists) {
            return redirect()->route('student.new.subjects')->with(['error' => 'برجاء تسجيل المواد الدراسية اولاً']);
        }
        $courses = $this->getRegisteredCourses($student_info['username'], $year, $semester);
        $year = explode('/', $this->getCurrentYear());
        $can_delete = (!$this->checkGuidance(auth()->id()) and !$this->checkPayment(auth()->id()));
        $can_print = ($this->checkGuidance(auth()->id()) and $this->checkPayment(auth()->id()));
        return view('student.student_registration', compact('can_register', 'regis_exists',
            'courses', 'can_delete', 'student_info', 'semester', 'year', 'can_print'));
    }

    public function deleteRegistration(Request $request)
    {
        if (!$this->canRegistration()) {
            return redirect()->back();
        }
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        if (!$this->registrationExists(auth()->id(), $semester, $year)) {
            return redirect()->route('student.new.subjects')->with(['error' => 'برجاء تسجيل المواد الدراسية اولاً']);
        }
        if ($this->checkGuidance(auth()->id())) {
            return redirect()->back()->with(['error' => 'لا يمكن حذف التسجيل الأن']);
        }
        if ($this->checkPayment(auth()->id()) and !$this->oldPaymentExists(auth()->id())) {
            return redirect()->back()->with(['error' => 'تم الانتهاء من الماليه فلا يمكن الحذف']);
        }
        $rules = [
            'semester' => 'required|string|in:' . $semester,
            'year' => 'required|string|in:' . $year,
        ];
        $data = $request->validate($rules);
        try {
            DB::transaction(function () use ($data) {
                DB::table('registration')->where('student_code', auth()->id())
                    ->where('year', $data['year'])->where('semester', $data['semester'])->delete();
                DB::table('registration_semester')->where('student_code', auth()->id())
                    ->where('year', $data['year'])->where('semester', $data['semester'])->delete();
                $dis_del = DB::table('students_discounts')->where('student_code', auth()->id())
                    ->where('year', $data['year'])->where('semester', $data['semester'])
                    ->where('type', 'دراسية');
                if ($dis_del->exists()) {
                    $dis_del->delete();
                    DB::table('students_alerts')->insert([
                        'student_code' => auth()->id(),
                        'category' => 'شئون الطلاب',
                        'reason' => 'تم حذف التسجيل و مالية الترم بما فى
                         ذالك الخصومات ايضاً برجاء التوجه الى شئون الطلاب ل اضافتها من جديد',
                        'status' => 'warning',
                        'created_by' => auth()->id(),
                    ]);
                }
                DB::table('payment_tickets')->where('student_code', auth()->id())
                    ->where('year', $data['year'])->where('semester', $data['semester'])
                    ->where('type', 'دراسية')->where('used', 0)->delete();
                DB::table('students_payments')->where('student_code', auth()->id())
                    ->where('year', $data['year'])->where('semester', $data['semester'])->delete();
            });
            return redirect()->route('student.new.subjects')->with(['success' => 'تم حذف المواد بنجاح']);
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => 'حدث خطأ فى الاتصال من فضلك حاول من جديد']);
        }
    }

    public function printRegistration()
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        if (!$this->registrationExists(auth()->id(), $semester, $year)) {
            return redirect()->route('student.new.subjects')->with(['error' => 'برجاء تسجيل المواد الدراسية اولاً']);
        }
        if (!($this->checkGuidance(auth()->id()) and $this->checkPayment(auth()->id()))) {
            return redirect()->back()->with('error', 'لا يمكن طباعة التسجيل إلا بعد موافقة الإرشاد و شؤون المالية');
        }
        try {
            $courses = $this->getRegisteredCourses(auth()->id(), $year, $semester);
            $student = $this->getStudentInfo(auth()->id());
            return view('student.print_registration', compact('courses', 'year',
                'student', 'semester'));
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => 'حدث خطأ فى الاتصال من فضلك حاول من جديد']);
        }
    }

    public function studentTranscript()
    {
        if ($this->oldPaymentExists(auth()->id()) or $this->oldPaymentExists(auth()->id(), true) or
            $this->ticketExists(auth()->id())) {
            return redirect()->route('dashboard')->withErrors(['alert' => 'يرجى مراجعة شئون المالية']);
        }
        $department = $this->getStudentInfo(auth()->id())['departments_id'];
        $departments = [1 ,$department];
        $registrations = DB::table('registration')->where('registration.student_code', auth()->id())
            ->join('courses', function ($join) use ($departments) {
            $join->on('courses.full_code', '=', 'registration.course_code')
            ->whereIn('courses.departments_id', $departments);
            })
            ->join('registration_semester', function ($join) {
                $join->on('registration_semester.student_code', '=', 'registration.student_code')
                    ->on('registration_semester.year', '=', 'registration.year')
                    ->on('registration_semester.semester', '=', 'registration.semester');
            })
            ->select(['registration.*', 'courses.name', 'courses.hours', 'courses.elective', 'guidance', 'payment'])
            ->orderBy('year')->orderBy('registration.semester')
            ->orderBy('courses.semester')->orderBy('course_code')
            ->get()->transform(function ($i) {
                unset($i->student_code);
                return $i;
            })->groupBy(['year', 'semester'])->toArray();
        if (DB::table('transferred_students_courses')
            ->where('student_code', auth()->id())->exists()) {
            array_unshift($registrations, [DB::table('transferred_students_courses')
                ->where('student_code', auth()->id())
                ->join('courses', 'full_code', '=', 'course_code')
                ->orderBy('courses.semester')->orderBy('course_code')
                ->select(['transferred_students_courses.course_code', 'grade', 'courses.name', 'courses.hours',
                    'courses.elective'])->get()->toArray()]);
        }
        $grades = $this->getRegistrationData($registrations);
        $student[] = $this->getStudentInfo(auth()->id())['name'];
        $student[] = auth()->id();
        $trans_courses = null;
        if (isset($registrations[0][0])) {
            $trans_courses = $registrations[0][0];
            unset($registrations[0]);
        }
        $seating_numbers = DB::table('seating_numbers')->where('student_code', auth()->id())
            ->get()->groupBy(['year'])->toArray();
        return view('student.student_transcript',
            compact('registrations', 'student', 'grades', 'trans_courses', 'seating_numbers'));
    }

    public function loginToMoodleQuiz()
    {
        if ($this->oldPaymentExists(auth()->id())) {
            return redirect()->route('dashboard')->withErrors(['alert' => 'يجب الانتهاء من المصاريف اولاً']);
        }
        $student = $this->getStudentInfo(auth()->id());
        $username = strtolower($student['username']);
        return redirect()
            ->away("https://hitis-platform.ahi-egypt.net/login/external_login.php?username" .
                "={$username}&password={$student['password']}");
    }

    public function loginToMoodleBook()
    {
        if ($this->oldPaymentExists(auth()->id())) {
            return redirect()->route('dashboard')->withErrors(['alert' => 'يجب الانتهاء من المصاريف اولاً']);
        }
        $student = $this->getStudentInfo(auth()->id());
        $username = strtolower($student['username']);
        return redirect()
            ->away("https://hitis.egy-x.com/login/external_login.php?username" .
                "={$username}&password={$student['password']}");
    }

      public function checkPaymentApi($username, Request $request)
        {
            if ($this->canMoodleLogin()) {
                if ($request->token == 'moodle site' and DB::table('students')
                        ->where('username', strtoupper($username))->exists()) {
                    return response()->json(['check' => ($this->oldPaymentExists(strtoupper($username)) or
                        $this->checkTotalPayment(strtoupper($username))[0])] or
                        $this->checkTotalAdministrativeExpenses(strtoupper($username))[0], 200);

                }
                return response()->json('ERROR', 403);
            }
            return response()->json(['check' => 'login closed'], 200);
        }


        public function getEntertainmentLogin(Request $request)
            {
                if ($request->token == 'entertainment site') {
                    $validator = Validator::make($request->all(), [
                        'username' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
                        'password' => 'required|string|between:8,16|exists:students,password',
                    ]);

                    if ($validator->fails()) {
                        return response()->json([
                            'login' => false,
                            'errors' => $validator->errors()->all(),
                        ]);
                    }
                    $username = $validator->validated()['username'];
                    return response()->json([
                        'login' => true,
                        'check' => ($this->oldPaymentExists(strtoupper($username)) or
                            $this->checkTotalPayment(strtoupper($username))[0])
                    ]);
                }
                return response()->json([], 404);
            }

    public function getEntertainmentStatus(Request $request)
    {
        if ($request->token == 'entertainment site') {
            $validator = Validator::make($request->all(), [
                'usernames' => 'required|array|between:1,12|distinct',
                'usernames.*' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()->all(),
                ]);
            }
            $usernames = $validator->validated()['usernames'];
            $response = [];
            foreach ($usernames as $username) {
                $response[$username] = ($this->oldPaymentExists(strtoupper($username)) or
                    $this->checkTotalPayment(strtoupper($username))[0]);
            }
            return response()->json([
                'codes' => $response,
                'errors' => false
            ]);
        }
        return response()->json([], 404);
    }

    public function showStudentData()
    {
        $data_errors = [];
        $data_show = [];
        $data = $this->getStudentData();
        $student = $this->getStudentInfo(auth()->id());
        $student['photo'] = $this->displayStudentPhoto($student['photo']);
        $advisor = $this->getAcademicAdvisor($student['academic_advisor']);
        $student['academic_advisor'] = $advisor->name;
        $data_errors['photo'] = ($student['photo'] == '');
        $data_errors['certificate_obtained'] = ($student['certificate_obtained'] == 'لا يوجد شهاده');
        $data_errors['certificate_degree'] = ($student['certificate_degree'] == 0 or
            $student['certificate_degree_total'] == 0 or $student['certificate_degree_percentage'] == 0);
        $data_show['english_degree'] = !(in_array($student['certificate_obtained'],
            array_slice($data['certificate_obtained'], 4, 5)));
        $data_errors['english_degree'] = (is_null($student['english_degree']));
        $data_errors['email'] = (is_null($student['email']));
        $data_show['birth_province'] = ($student['birth_country'] == 'مصر');
        if ($data_show['birth_province']) {
            $data_errors['birth_province'] = !(in_array($student['birth_province'], $data['birth_province']));
        } else {
            $data_errors['birth_province'] = false;
        }
        $data_show['national_id'] = ($student['nationality'] == 'مصري');
        if ($data_show['national_id']) {
            $data_errors['issuer_national_number'] = (empty($student['issuer_national_number']));
        } else {
            $data_errors['issuer_national_number'] = false;
        }
        return view('student.student_data', compact('student', 'data', 'data_errors',
            'data_show'));
    }
}
