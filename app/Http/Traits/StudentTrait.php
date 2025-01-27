<?php

namespace App\Http\Traits;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

trait StudentTrait
{
    use DataTrait, FinanceTrait;

    public function getNewUsername($type): string
    {
        if (Carbon::now()->between('01-07-' . date('Y'), '01-01-' . (date('Y') + 1))) {
            $y = date('y');
        } else {
            $y = date('y') - 1;
        }
        $code = max(DB::table('deleted_students')->selectRaw('MAX(SUBSTRING(`username`,2,6)) as code')
            ->whereRaw('substring(`username`,2,2) = ?', $y)
            ->first()->code, Student::selectRaw('MAX(SUBSTRING(`username`,2,6)) as code')
            ->whereRaw('substring(`username`,2,2) = ?', $y)->first()->code);
        if ($code) {
            return $type . ($code + 1);
        } else {
            return $type . date('y') . '0001';
        }

    }

    public function getAcademicAdvisor($advisor_username)
    {
        return DB::table('academic_advisors')->where('username', $advisor_username)->first();
    }

    public function canRegistration(): bool
    {
        $semesters = (array)DB::table('semester')->first();
        if ($semesters['first_semester'] == 1 or $semesters['second_semester'] == 1 or $semesters['summer_semester'] == 1) {
            return true;
        }
        return false;
    }

    public function canAcademicRegistration(): bool
    {
        return DB::table('semester')->first()->academic_registration == 1;
    }

    public function studentIsNew(string $username = ''): bool
    {
        $username = $username ?: auth()->id();
        return ($this->getStudentInfo($username)['studying_status'] == 'مستجد');
    }

    public function getStudentCourses($student_info): array
    {
        $department = $student_info['departments_id'];
        $courses = [];
        $semester = $this->getCurrentSemester();
        $type = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'R' : 'T';
        if ($semester == 'ترم أول') {
            if ($student_info['study_group'] == 'الاولي') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 1)
                and $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 3);
            } else if ($student_info['study_group'] == 'الثانية') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 3);
            }
            else if ($student_info['study_group'] == 'الثالثة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 5)
                and $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 7);
            }
             else if ($student_info['study_group'] == 'الرابعة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 7);
            }
        } else if ($semester == 'ترم ثاني') {

            if ($student_info['study_group'] == 'الاولي') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 2)
                and  $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 4);
            } else if ($student_info['study_group'] == 'الثانية') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 4);
            } else if ($student_info['study_group'] == 'الثالثة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 6)
                  and  $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 8);
            } else if ($student_info['study_group'] == 'الرابعة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 8);
            }
        } else if ($semester == 'ترم صيفي') {
            $courses = $this->getAvailableCourses($student_info['username'], $type, $department, 8);
        }
        return $courses;
    }
    public function getStudentWithoutUpLevelCourses($student_info): array
    {
        $department = $student_info['departments_id'];
        $courses = [];
        $semester = $this->getCurrentSemester();
        $type = ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'R' : 'T';
        if ($semester == 'ترم أول') {
            if ($student_info['study_group'] == 'الاولي') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 1);
            } else if ($student_info['study_group'] == 'الثانية') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 3);
            }
            else if ($student_info['study_group'] == 'الثالثة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 5);
            }
             else if ($student_info['study_group'] == 'الرابعة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 7);
            }
        } else if ($semester == 'ترم ثاني') {

            if ($student_info['study_group'] == 'الاولي') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 2);
            } else if ($student_info['study_group'] == 'الثانية') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 4);
            } else if ($student_info['study_group'] == 'الثالثة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 6);
            } else if ($student_info['study_group'] == 'الرابعة') {
                $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 8);
            }
        } else if ($semester == 'ترم صيفي') {
            $courses = $this->getAvailableCourses($student_info['username'], $type,$department , 8);
        }
        return $courses;
    }

    public function getAvailableCourses($student_code, $type, $department,$semester): array
    {
        /**F grad Courses is selected**/
        $courses_f = DB::table('courses')->where('is_selected', 1)->distinct()
            ->where('type', $type)->where('departments_id', $department)
            ->join('registration', 'courses.full_code', '=', 'registration.course_code')
            ->where('student_code', $student_code)->where('grade', 'F')
            ->select('courses.*', 'grade');
        $courses_f = $courses_f->union(
            DB::table('courses')->where('is_selected', 1)->distinct()
                ->where('type', $type)->where('departments_id', $department)
                ->join('transferred_students_courses', 'courses.full_code', '=',
                    'transferred_students_courses.course_code')
                ->where('student_code', $student_code)->where('grade', 'F')
                ->select('courses.*', 'grade'))->orderBy('elective')->get();
        /**F grad Courses ALL**/
        $courses_f_all = DB::table('courses')->distinct()
            ->where('type', $type)->where('departments_id', $department)
            ->join('registration', 'courses.full_code', '=', 'registration.course_code')
            ->where('student_code', $student_code)->where('grade', 'F')
            ->select('courses.*', 'grade');
        $courses_f_all = $courses_f_all->union(
            DB::table('courses')->distinct()
                ->where('type', $type)->where('departments_id', $department)
                ->join('transferred_students_courses', 'courses.full_code', '=',
                    'transferred_students_courses.course_code')
                ->where('student_code', $student_code)->where('grade', 'F')
                ->select('courses.*', 'grade'))->get();
        /**Succeeded in Courses**/
        $courses_s = DB::table('courses')->where('type', $type)->where('departments_id', $department)
            ->join('registration', 'courses.full_code', '=', 'registration.course_code')
            ->where('student_code', $student_code)->whereNotIn('grade', ['P', 'F'])
            ->orderBy('registration.year')
            ->orderByDesc('registration.semester')
            ->select('courses.*', 'grade', 'registration.year as registration_year',
                'registration.semester as registration_semester');

        $courses_s = $courses_s->union(
            DB::table('courses')->where('type', $type)->where('departments_id', $department)
                ->join('transferred_students_courses', 'courses.full_code', '=',
                    'transferred_students_courses.course_code')
                ->where('student_code', $student_code)->where('grade', '!=', 'F')
                ->selectRaw('courses.* ,grade ,"مواد معادلة من الخارج" as registration_year,
                 "" as registration_semester'))->orderBy('semester')->get();

        foreach ($courses_f as $key => $value) {
            if ($courses_s->contains('full_code', $value->full_code)) {
                $courses_f->forget($key);
            }
        }
        foreach ($courses_f_all as $key => $value) {
            if ($courses_s->contains('full_code', $value->full_code)) {
                $courses_f_all->forget($key);
            }
        }
        $courses_a = DB::table('courses')->where('is_selected', 1)
            ->where('type', $type)->where('departments_id', $department)->where('semester', '<=', $semester)
            ->whereNotIn('full_code', $courses_s->pluck('full_code')->toArray())
            ->whereNotIn('full_code', $courses_f->pluck('full_code')->toArray())
            ->select('courses.*')->orderBy('elective')
            ->orderBy('semester')->orderBy('full_code')->get();
        $prerequisite = DB::table('prerequisite')->select()
            ->whereIn('main_cousre_code', $courses_a->pluck('full_code')->toArray())
            ->whereNotIn('required_cousrse_code', $courses_s->pluck('full_code')->toArray())->get();
        /**Available Courses**/
        $courses_a = $courses_a->whereNotIn('full_code', $prerequisite->pluck('main_cousre_code')->toArray())
            ->sortByDesc('semester');
        $courses_s_ava = $courses_s->where('is_selected', 1);
        return [
            $courses_a->toArray(),
            $courses_f->toArray(),
            $courses_s->toArray(),
            $courses_f_all->toArray(),
            $courses_s_ava->toArray()
        ];
    }

    public function getStudentCurrentSemester($student_info): int
    {
        $semester = $this->getCurrentSemester();
        if ($semester == 'ترم أول') {
            if ($student_info['study_group'] == 'الاولي') {
                return 1;
            } else if ($student_info['study_group'] == 'الثانية') {
                return 3;
            } else if ($student_info['study_group'] == 'الثالثة') {
                return 5;
            } else if ($student_info['study_group'] == 'الرابعة') {
                return 7;
            }
        } else if ($semester == 'ترم ثاني') {
            if ($student_info['study_group'] == 'الاولي') {
                return 2;
            } else if ($student_info['study_group'] == 'الثانية') {
                return 4;
            } else if ($student_info['study_group'] == 'الثالثة') {
                return 6;
            } else if ($student_info['study_group'] == 'الرابعة') {
                return 8;
            }
        } else if ($semester == 'ترم صيفي') {
            return 8;
        }
        return 0;
    }

    public function getCoursePrerequisite(array $courses_code): array
    {
        if (!empty($courses_code)) {
            return DB::table('prerequisite')->whereIn('main_cousre_code', $courses_code)->get()
                ->groupBy('main_cousre_code')->toArray();
        }
        return [];
    }

    public function getElectiveCourses($student_code): array
    {
        return DB::table('registration')->distinct()->where('student_code', $student_code)
            ->join('courses', 'courses.full_code', '=', 'registration.course_code')
            ->where('elective', 1)->select('course_code', 'courses.semester as courses_semester')->orderBy('courses_semester','DESC')
            ->get()->groupBy('courses_semester')->toArray();
    }

    public function getRegisteredCourses($username, $year, $semester): array
    {
        if ($this->registrationExists($username, $semester, $year)) {
            $courses = DB::table('registration')->select()
                ->where('student_code', $username)
                ->where('year', $year)
                ->where('semester', $semester)->get();
            $courses2 = $this->getAllCourses($courses->pluck('course_code')->toArray());
            for ($i = 0; $i < count($courses2); $i++) {
                foreach ($courses as $course) {
                    if ($courses2[$i]['full_code'] == $course->course_code) {
                        $courses2[$i]['guidance'] = $this->checkGuidance($username, $year, $semester);
                        $courses2[$i]['payment'] = $this->checkPayment($username, $year, $semester);
                        $courses2[$i]['grade'] = $course->grade;
                    }
                }
            }
            return $courses2;
        }
        return [];
    }

    public function registrationExists($username, $semester, $year): bool
    {
        return (DB::table('registration')->where([
                ['student_code', '=', $username],
                ['year', '=', $year],
                ['semester', '=', $semester]
            ])->exists() and DB::table('registration_semester')->where([
                ['student_code', '=', $username],
                ['year', '=', $year],
                ['semester', '=', $semester]
            ])->exists());
    }

    public function checkUniqueStudent($username, $col, $value, $del = false): bool
    {
        $output = false;
        if (Student::select('username')->where($col, $value)->exists()) {
            if (Student::select('username')->where($col, $value)->first()->username != $username) {
                $output = true;
            }
        }
        if ($del and DB::table('deleted_students')->where($col, $value)->exists()) {
            $output |= true;
        }
        return $output;
    }

    public function getStudentInfo($username): array
    {
        return is_null(Student::find($username)) ? (array)DB::table('deleted_students')
            ->where('username', $username)->first() : Student::find($username)->getOriginal();
    }

    public function getStudentSectionNumber($student_code, $year, $semester)
    {
        return DB::table('section_number')->where(compact('student_code', 'year', 'semester'))
            ->first();
    }

    public function getStudentExamPlace($student_code)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        return DB::table('exam_place')->where(compact('student_code', 'year', 'semester'))
            ->first();
    }

    public function getStudentExamTable($student_code): array
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $courses = $this->getRegisteredCourses($student_code, $year, $semester);
        if (!empty($courses)) {
            $courses = collect($courses)->map(function ($value) use ($student_code, $semester, $year) {
                $time = DB::table('exam_table')->where(['year' => $year, 'semester' => $semester,
                    'course_code' => $value['full_code']]);
                if ($time->exists()) {
                    $date = Carbon::make($time->first()->exam_date);
                    $value['day'] = $date->format('d/m/Y');
                    $value['time'] = $time->first()->exam_time;
                } else {
                    $value['day'] = $value['time'] = null;
                }
                $value['remaining'] = $this->checkCourseRemaining($student_code, $value['full_code']);
                return $value;
            })->sort(function ($a, $b) {
                if (!empty($a['day']) and !empty($b['day'])) {
                    return Carbon::createFromFormat('d/m/Y', $a['day'])->gt(
                        Carbon::createFromFormat('d/m/Y', $b['day']));
                }
                if (!empty($a['day'])) {
                    return -1;
                }
                return 1;
            })->toArray();
        }
        return $courses;
    }

    public function checkCourseRemaining($student_code, $course): bool
    {
        return DB::table('registration')->where(['student_code' => $student_code, 'course_code' => $course,
            'grade' => 'F'])->exists();
    }

    public function checkGuidance($username, $year = null, $semester = null): ?int
    {
        if ($year == null) {
            $year = $this->getCurrentYear();
        }
        if ($semester == null) {
            $semester = $this->getCurrentSemester();
        }
        $registration_status = DB::table('registration_semester')
            ->where([
                ['student_code', '=', $username],
                ['semester', '=', $semester],
                ['year', '=', $year],
            ])->get()->toArray();
        return empty($registration_status) ? null : $registration_status[0]->guidance;
    }

    public function checkExcuse($username, $year = null, $semester = null): ?bool
    {
        if ($year == null) {
            $year = $this->getCurrentYear();
        }
        if ($semester == null) {
            $semester = $this->getCurrentSemester();
        }
        return DB::table('students_excuses')
            ->where([
                ['student_code', '=', $username],
                ['semester', '=', $semester],
                ['year', '=', $year],
            ])->exists();
    }

    public function getYearGrade($student_code, $year = null): string
    {
        if (is_null($year)) {
            $year = $this->getCurrentYear();
        }
        $registrations = DB::table('registration')->where('student_code', $student_code)
            ->join('courses', 'courses.full_code', '=', 'registration.course_code')
            ->select(['registration.*', 'courses.name', 'courses.hours', 'courses.elective',])
            ->where('year', $year)->orderBy('year')->orderBy('registration.semester')
            ->get()->groupBy(['year', 'semester'])->toArray();
        if (empty($registrations)) {
            return '';
        } else {
            return $this->cgpaToGrade(end($this->getRegistrationData($registrations)[$year])['cgpa']);
        }
    }

    public function cgpaToGrade(float $cgpa): string
    {
        if ($cgpa >= 3.7) {
            return 'ممتاز';
        } else if ($cgpa < 3.7 and $cgpa >= 3) {
            return 'جيد جدا';
        } else if ($cgpa < 3 and $cgpa >= 2.4) {
            return 'جيد';
        } else if ($cgpa < 2.4 and $cgpa >= 2) {
            return 'مقبول';
        } else {
            return 'ضعيف';
        }
    }

    public function getRegistrationData($registrations): array
    {
        $grades = [];
        $grade = $this->gradeToPoint()[0];
        foreach ($registrations as $year => $registration) {
            foreach ($registration as $semester => $courses) {
                $grades[$year][$semester]['gpa'] = $this->calculateGPA($courses);
                $grades[$year][$semester]['cgpa'] = 0;
                $grades[$year][$semester]['hours'] = 0;
                $grades[$year][$semester]['earned_hours'] = 0;
                $grades[$year][$semester]['total_earned_hours'] = 0;
                $grades[$year][$semester]['courses'] = [];
                foreach ($courses as $course) {
                    if (!in_array($course->grade, ['IC', 'W'])) {
                        $grades['courses'][] = $course;
                        $grades[$year][$semester]['hours'] += $course->hours;
                        $grades[$year][$semester]['courses'][$course->course_code] = $course->hours * $grade[$course->grade];
                        if ($grade[$course->grade] > 0) {
                            $grades[$year][$semester]['earned_hours'] += $course->hours;
                        }
                    } else {
                        $grades[$year][$semester]['courses'][$course->course_code] = 'غير مكتمل لا تحتسب';
                    }
                }
                $grades[$year][$semester]['cgpa'] = $this->calculateGPA($grades['courses']);
                $grades[$year][$semester]['total_earned_hours'] = $this->calculateTotalHours($grades['courses']);
            }
        }
        return $grades;
    }

    public function calculateGPA($courses): float
    {
        $points = 0;
        $total_hours = 0;
        $grade = $this->gradeToPoint()[0];
        $courses = collect($courses)->whereNotIn('grade', ['IC', 'W'])->groupBy(['course_code'])->toArray();
        foreach ($courses as $course) {
            $n = count($course);

            if ($course[$n - 1]->grade == 'P' and $n > 1) {
                $points += $course[$n - 2]->hours * $grade[$course[$n - 2]->grade];

                $total_hours += $course[$n - 2]->hours;
            } elseif ($course[$n - 1]->grade != 'P') {
                $points += $course[$n - 1]->hours * $grade[$course[$n - 1]->grade];
                $total_hours += $course[$n - 1]->hours;
            }
        }
        if ($total_hours == 0) {
            return 0;
        }
        return round($points / $total_hours, 4);
    }

    public function calculateTotalHours($courses): int
    {
        $hours = 0;
        $grade = $this->gradeToPoint()[0];
        foreach ($courses as $course) {
            if ($grade[$course->grade] > 0) {
                $hours += $course->hours;
            }
        }
        return $hours;
    }

    public function getAlerts($student_code, $category = null): array
    {
        if (is_null($category)) {
            return DB::table('students_alerts')->where('student_code', $student_code)
                ->orderBy('status', 'desc')->orderBy('id')->get()->toArray();
        } else {
            return DB::table('students_alerts')->where('student_code', $student_code)
                ->where('category', $category)->orderBy('status', 'desc')
                ->orderBy('id')->get()->toArray();
        }
    }

    public function checkStudentClassification($student_code): int
    {
        $classification = Student::find($student_code)->student_classification;
        if ($classification == 'مقيد')
            return 1;
        if (in_array($classification, ['عذر', 'وقف قيد']))
            return 2;
        else
            return 0;
    }

    public function checkTotalPayment($student_code): array
    {
        if (!DB::table('students_payments_exception')->where(compact('student_code'))->exists()) {
            $paid = $this->getTotalStudyPaid($student_code, $this->getCurrentYear());
            $discount = $this->getTotalStudyDiscount($student_code, $this->getCurrentYear());
            $wallet = $this->getStudentWallet($student_code);
            $sum =  (is_null($wallet) ? 0 : $wallet->amount);
            $student_info = $this->getStudentInfo($student_code);
            if ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') {
                $payments = (array)DB::table('hour_payment_arabic')->where('id', 6)->first();
            } else {
                $payments = (array)DB::table('hour_payment_english')->where('id', 6)->first();
            }
            $payment = 0;
            switch ($student_info['study_group']) {
                case 'الاولي':
                    $payment = $payments['first'];
                    break;
                case 'الثانية':
                    $payment = $payments['second'];
                    break;
                case 'الثالثة':
                    $payment = $payments['third'];
                    break;
                case 'الرابعة':
                    $payment = $payments['fourth'];
                    break;
            }
       //     dd($sum,$payment,$paid);
            return [$sum < $payment, $payment - $sum];
        }
        return [false, 0];
    }
          // Start AdministrativeExpenses
            public function checkTotalAdministrativeExpenses($student_code):array{
                $student_info = $this->getStudentInfo($student_code);
            if ($student_info['username']) {
                $payments = (array)DB::table('administrative_expenses')->where('id', 7)->first();
            }
                $payment = 0;
                switch ($student_info['study_group']) {
                    case 'الاولي':
                        $payment = $payments['first'];
                        break;
                    case 'الثانية':
                        $payment = $payments['second'];
                        break;
                    case 'الثالثة':
                        $payment = $payments['third'];
                        break;
                    case 'الرابعة':
                        $payment = $payments['fourth'];
                        break;
                }
                return [$payment];
    }
     public function getpaymentsAdministrativeExpenses($student_code):array {

            $student_info = $this->getStudentInfo($student_code);
            if ($student_info['username']) {
                    $payments_first = (array)DB::table('administrative_expenses')->pluck('first');
                    $payments_second = (array)DB::table('administrative_expenses')->pluck('second');
                    $payments_third = (array)DB::table('administrative_expenses')->pluck('third');
                $payments_fourth = (array)DB::table('administrative_expenses')->pluck('fourth');
                }
                $payments_first = $payments_first["\x00*\x00items"];
                $payments_second = $payments_second["\x00*\x00items"];
                $payments_third = $payments_third["\x00*\x00items"];
                $payments_fourth = $payments_fourth["\x00*\x00items"];
                if ($student_info['studying_status'] == 'مستجد') {
                switch ($student_info['study_group']) {
                    case 'الاولي':
                        $payment1 = $payments_first[0];
                        $payment2 = $payments_first[1];
                        $payment3 = $payments_first[2];
                        $payment4 = $payments_first[3];
                        $payment5 = $payments_first[4];
                        $payment6 = $payments_first[5];
                        break;
                    case 'الثانية':
                        $payment1 = $payments_second[0];
                        $payment2 = $payments_second[1];
                        $payment3 = $payments_second[2];
                        $payment4 = $payments_second[3];
                        $payment5 = $payments_second[4];
                        $payment6 = $payments_second[5];
                         break;
                    case 'الثالثة':
                        $payment1 = $payments_third[0];
                        $payment2 = $payments_third[1];
                        $payment3 = $payments_third[2];
                        $payment4 = $payments_third[3];
                        $payment5 = $payments_third[4];
                        if($student_info['gender'] == 'ذكر' and $student_info['nationality'] == 'مصري' and  $student_info['military_education'] != 'مجتاز'  and $student_info['military_education'] !='معفي' ){
                          $payment6 = $payments_third[5];
                        }else{
                            $payment6 = 0;
                        }
                         break;
                    case 'الرابعة':
                        $payment1 = $payments_fourth[0];
                        $payment2 = $payments_fourth[1];
                        $payment3 = $payments_fourth[2];
                        $payment4 = $payments_fourth[3];
                        $payment5 = $payments_fourth[4];
                         if($student_info['gender'] == 'ذكر' and $student_info['nationality'] == 'مصري'  and $student_info['military_education']== 'غير مجتاز'){
                          $payment6 = $payments_third[5];
                        }else{
                            $payment6 = 0;
                        }
                         break;

            }
             }
             elseif($student_info['studying_status'] !='مستجد' ){
                switch ($student_info['study_group']) {
                    case 'الاولي':
                        $payment1 = 0;
                        $payment2 = 0;
                        $payment3 = 300;
                        $payment4 = 0;
                        $payment5 = 0;
                        $payment6 = 0;
                        break;
                    case 'الثانية':
                       $payment1 = 0;
                        $payment2 = 0;
                        $payment3 = 300;
                        $payment4 = 0;
                        $payment5 = 0;
                        $payment6 = 0;
                         break;
                    case 'الثالثة':
                        $payment1 = 0;
                        $payment2 = 0;
                        $payment3 = 300;
                        $payment4 = 0;
                        $payment5 = 0;
                        if($student_info['gender'] == 'ذكر' and $student_info['nationality'] == 'مصري' and  $student_info['military_education'] != 'مجتاز'  and $student_info['military_education'] !='معفي' ){
                          $payment6 = $payments_third[5];
                        }else{
                            $payment6 = 0;
                        }
                         break;
                    case 'الرابعة':
                        $payment1 = 0;
                        $payment2 = 0;
                        $payment3 = 300;
                        $payment4 = 0;
                        $payment5 = 0;
                         if($student_info['gender'] == 'ذكر' and $student_info['nationality'] == 'مصري'  and $student_info['military_education']== 'غير مجتاز'){
                          $payment6 = $payments_third[5];
                        }else{
                            $payment6 = 0;
                        }
                         break;

               }
             }
                return [$payment1,$payment2,$payment3,$payment4,$payment5,$payment6];
     }

     function generateTicketId($username) {
                $currentYear = date('Y');
                $uniqueNumber = rand(1, 100000);
                return $currentYear. $uniqueNumber . $username;
    }


            public function checkUsedAdministrativeExpenses($username): int
            {
                $year = $this->getCurrentYear();
                $payment = DB::table('payments_administrative_expenses')
                    ->where('student_code', $username)->where('year',$year)
                    ->first();


                if ($payment !== null) {
                    return $payment->used;
                } else {
                    return 0;
                }
            }

    public function getStudentsRegistrationHour(string $specialization, string $study_group): int
    {
        $semester = $this->getCurrentSemester();
        if ($specialization == 'ترميم الاثار و المقتنيات الفنية') {
            $registration_hour = DB::table('students_registration_hour')->where('id', 1);
        } else {
            $registration_hour = DB::table('students_registration_hour')->where('id', 2);
        }
        if ($semester == 'ترم أول' or $semester == 'ترم ثاني') {
            switch ($study_group) {
                case 'الاولي':
                    $registration_hour = $registration_hour->first('study_group_1')->study_group_1;
                    break;
                case 'الثانية':
                    $registration_hour = $registration_hour->first('study_group_2')->study_group_2;
                    break;
                case 'الثالثة':
                    $registration_hour = $registration_hour->first('study_group_3')->study_group_3;
                    break;
                case 'الرابعة':
                    $registration_hour = $registration_hour->first('study_group_4')->study_group_4;
                    break;
            }
        } elseif ($semester == 'ترم صيفي') {
            $registration_hour = $registration_hour->first('summer')->summer;
        } else {
            return 0;
        }
        return $registration_hour;
    }

    public function calculateCGPAStudent($username): array
    {
        $courses = DB::table('transferred_students_courses')->where('student_code', $username)
            ->union(DB::table('registration')->where('student_code', $username)
                ->select('course_code', 'grade', 'courses.hours')
                ->join('courses', 'full_code', '=', 'course_code')
                ->orderBy('registration.year')->orderBy('registration.semester'))
            ->join('courses', 'full_code', '=', 'course_code')
            ->select('course_code', 'grade', 'courses.hours')
            ->get()->whereNotIn('grade', ['P', 'IC', 'W'])->groupBy(['course_code'])->toArray();
        $grades = $this->gradeToPoint()[0];
        $points = 0;
        $earned_hours = 0;
        $total_hours = 0;
        foreach ($courses as $course) {
            $points += last($course)->hours * $grades[last($course)->grade];
            $total_hours += last($course)->hours;
            if ($grades[last($course)->grade] > 0) {
                $earned_hours += last($course)->hours;
            }
        }
        $cgpa = ($total_hours > 0) ? round($points / $total_hours, 4) : 0;
        return compact('cgpa', 'earned_hours', 'total_hours');
    }

    public function getStudentRegistrationStatus($username): array
    {
         $department = $this->getStudentInfo($username)['departments_id'];
         $departments = [1 ,$department];
         $registrations = DB::table('registration')->where('registration.student_code', $username)
            ->join('courses', function ($join) use ($departments) {
             $join->on('courses.full_code', '=', 'registration.course_code')
             ->whereIn('courses.departments_id', $departments);
         })
            ->join('registration_semester', function ($join) {
                $join->on('registration_semester.student_code', '=', 'registration.student_code')
                    ->on('registration_semester.year', '=', 'registration.year')
                    ->on('registration_semester.semester', '=', 'registration.semester');
            })
            ->select(['registration.*', 'courses.name', 'courses.hours', 'courses.elective', 'guidance', 'payment',
            'courses.semester as courses_semester'])->orderBy('year')
            ->orderBy('registration.semester')->orderBy('courses.full_code')->get();

        $registrations = $registrations->groupBy(['year', 'semester'])->toArray();
        if (DB::table('transferred_students_courses')
            ->where('student_code', $username)->exists()) {
            array_unshift($registrations, [DB::table('transferred_students_courses')
                ->where('student_code', $username)
                ->join('courses', 'full_code', '=', 'course_code')
                ->orderBy('courses.semester')->orderBy('course_code')
                ->select(['transferred_students_courses.*', 'courses.name', 'courses.hours', 'courses.elective',
                    'courses.semester as courses_semester'])->get()->toArray()]);
        }
        $grades = $this->getRegistrationData($registrations);
        $trans_courses = null;
        if (isset($registrations[0][0])) {
            $trans_courses = $registrations[0][0];
            unset($registrations[0]);
        }
        return [$registrations, $grades, $trans_courses];
    }

     public function getStudentRegistrationSummer($username): array
        {
            $department = $this->getStudentInfo($username)['departments_id'];
            $departments = [1 ,$department];
            $registrations = DB::table('registration')->where('registration.student_code', $username)
            ->join('courses', function ($join) use ($departments) {
                $join->on('courses.full_code', '=', 'registration.course_code')
                ->whereIn('courses.departments_id', $departments);
            })
            ->join('registration_semester', function ($join) {
                $join->on('registration_semester.student_code', '=', 'registration.student_code')
                    ->on('registration_semester.year', '=', 'registration.year')
                    ->on('registration_semester.semester', '=', 'registration.semester');
            })
            ->select(['registration.*', 'courses.name', 'courses.hours', 'courses.elective', 'guidance', 'payment',
                'courses.semester as courses_semester'])
            ->orderBy('year')
            ->orderBy('registration.semester')
            ->orderBy('courses.full_code')
            ->get();
             $registrations = $registrations->groupBy(['year', 'semester'])->toArray();
            if (DB::table('transferred_students_courses')->where('student_code', $username)->exists()) {
            array_unshift($registrations, [DB::table('transferred_students_courses')
                ->where('student_code', $username)
                ->join('courses', 'full_code', '=', 'course_code')
                ->orderBy('courses.semester')
                ->orderBy('course_code')
                ->select(['transferred_students_courses.*', 'courses.name', 'courses.hours', 'courses.elective',
                    'courses.semester as courses_semester'])
                ->get()
                ->toArray()
            ]);
        }
        $grades = $this->getRegistrationData($registrations);
        $trans_courses = null;
        if (isset($registrations[0][0])) {
            $trans_courses = $registrations[0][0];
            unset($registrations[0]);
        }

        $hasSummerSemester = false;
        foreach ($registrations as $year => $semesters) {
            foreach ($semesters as $semester => $data) {
                if ($semester === 'ترم صيفي') {
                    $hasSummerSemester = true;
                    break 2;
                }
            }
        }
        if (!$hasSummerSemester) {
           return  $registrations = [];
        }
        return [$registrations, $grades, $trans_courses];
    }


    public function displayStudentPhoto($filename): string
    {
        if (!is_null($filename)) {
            $path = storage_path('app/public/' . $filename);
            if (File::exists($path)) {
                $file = File::get($path);
                $type = File::mimeType($path);
                return 'data:' . $type . ';base64,' . base64_encode($file);
            }
        }
        return '';
    }

    public function canChangeSpecialization(array $student_info): bool
    {
        return ($student_info['study_group'] == 'الاولي' and $student_info['total_hours'] == 0);
    }

    public function changeStudentSpecialization(string $username, string $to): string
    {
        $u = $username;
        if ($to == 'سياحة') {
            $username[0] = 'T';
        } else {
            $username[0] = 'R';
        }
        $new_username = $username;
        $username = $u;
        $tables = ['email_verification' => 'username', 'notifications' => 'username', 'password_resets' => 'username',
            'payment_tickets' => 'student_code', 'registration_years' => 'student_code',
            'students_alerts' => 'student_code', 'students_discounts' => 'student_code',
            'students_excuses' => 'student_code', 'students_notes' => 'student_code', 'exam_place' => 'student_code',
            'students_other_payments' => 'student_code', 'students_wallet_transaction' => 'student_code',
            'students_wallet' => 'student_code', 'track' => 'username', 'transferred_students_courses' => 'student_code',
            'students_current_warning' => 'student_code', 'students_semesters' => 'student_code',
            'students_payments_exception' => 'student_code',
            'payments_administrative_expenses' => 'student_code'
        ];
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        if ($this->registrationExists($username, $semester, $year)) {
            $payed = $this->getTotalStudyPay($username, $year, $semester);
            $wallet = $this->getStudentWallet($username);
            if ($wallet) {
                $wallet->amount += $payed;
                $wallet->student_code = $new_username;
                $wallet = (array)$wallet;
            } else {
                $wallet = ['student_code' => $new_username, 'amount' => $payed];
            }
            DB::table('students_wallet')->updateOrInsert(['student_code' => $username], $wallet);
            DB::table('students_wallet_transaction')->insert([
                'student_code' => $new_username,
                'year' => $year,
                'semester' => $semester,
                'amount' => $payed,
                'date' => Carbon::now(),
                'type' => 'ايداع',
                'reason' => 'استرجاع مصاريف دراسية من تحويل التخصص',
            ]);
            DB::table('payment_tickets')->where(['student_code' => $username, 'type' => 'دراسية', 'year' => $year,
                'semester' => $semester])->update(['type' => 'محفظة']);
            DB::table('students_payments')->where('student_code', $username)->delete();
            DB::table('registration')->where('student_code', $username)->delete();
            DB::table('registration_semester')->where('student_code', $username)->delete();
            DB::table('seating_numbers')->where('student_code', $username)->delete();
            DB::table('section_number')->where('student_code', $username)->delete();
        }
        foreach ($tables as $table => $column) {
            DB::table($table)->where($column, $username)->update([$column => $new_username]);
        }
        return $new_username;
    }

    public function getSeatingNumber($student_code, $year = null)
    {
        $year = $year ?: $this->getCurrentYear();
        return DB::table('seating_numbers')->where(compact('student_code', 'year'))->get()
            ->first();
    }

    public function getCurrentWarning($student_code)
    {
        return DB::table('students_current_warning')->where(compact('student_code'))->get()->first();
    }

    public function checkSeatingNumber($student_code, $seating_number, $year = null): bool
    {
        $year = $year ?: $this->getCurrentYear();
        return DB::table('seating_numbers')->where(compact('student_code', 'seating_number',
            'year'))->exists();
    }
    public function checkStatusGraduated($username, $study_group)
    {
        $count_cgpa = DB::table('students_semesters')->where('student_code', $username)
            ->where('CGPA', '<', 3)->count();

        $count_semester = DB::table('students_semesters')->where('student_code', $username)
            ->count();


        if ($count_cgpa == 0 and $study_group == 'الرابعة' and $count_semester >= 7) {

            DB::table('students')
                ->where('username', $username)
                ->update(['status_graduated' => 'خريج مع مرتبه الشرف']);
        }
    }



}
