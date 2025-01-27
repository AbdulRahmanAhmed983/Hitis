<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait DataTrait
{
    public function getStudentData(): array
    {
        $data = $this->getData([
            'birth_country', 'birth_province', 'certificate_obtained', 'nationality', 'religion', 'specialization',
            'student_classification', 'studying_status', 'study_group', 'certificate_degree', 'apply_classification',
            'enlistment_status'
        ]);
        $degrees = $data['certificate_degree'];
        unset($data['certificate_degree']);
        foreach ($degrees as $degree) {
            $arr = explode('|', $degree);
            $data['certificate_degree'][$arr[0]] = $arr[1];
        }
        return $data;
    }

    public function getData(array $keys): array
    {
        $data = DB::table('data')->select('data_key as key', 'value')
            ->whereIn('data_key', $keys)->orderBy('sorting_index')->orderBy('value')->get();
        $info = [];
        foreach ($data as $value) {
            $info[$value->key][] = $value->value;
        }
        return $info;
    }

    public function getPreviousYear($year = null): string
    {
        $year = $year ?: $this->getCurrentYear();
        $y = explode('/', $year)[1];
        return $y . '/' . ($y - 1);
    }

    public function getNextYear($year = null): string
    {
        $year = $year ?: $this->getCurrentYear();
        $y = explode('/', $year)[0];
        return ($y + 1) . '/' . $y;
    }

    public function getCurrentYear(): string
    {
        if ($this->getCurrentSemester() == 'ترم أول') {
            if (!Carbon::now()->between('01-08-' . date('Y'), '01-01-' . (date('Y') + 1))) {
                return date('Y') . '/' . (date('Y') - 1);
            }
            return (date('Y') + 1) . '/' . date('Y');
        } else if ($this->getCurrentSemester() == 'ترم ثاني') {
            return date('Y') . '/' . (date('Y') - 1);
        } else if ($this->getCurrentSemester() == 'ترم صيفي') {
            return date('Y') . '/' . (date('Y') - 1);
        }
        abort(302, '', ['Location' => redirect()->back()
            ->with('rate-limit', 'السنة الدراسية الان مغلقه')->getTargetUrl()]);
    }

    public function getCurrentSemester()
    {
        $semesters = (array)DB::table('semester')->first();
        if ($semesters['first_semester']) {
            return 'ترم أول';
        } else if ($semesters['second_semester']) {
            return 'ترم ثاني';
        } else if ($semesters['summer_semester']) {
            return 'ترم صيفي';
        }
        abort(302, '', ['Location' => redirect()->back()->with('rate-limit', 'الترم الدراسى الان مغلق')
            ->getTargetUrl()]);
    }

    public function checkStatusAction($year, $semester): array
    {
        $action_exist = DB::table('change_status_details')->where(compact('year', 'semester'))
            ->exists();
        $grade_finished = DB::table('registration')->where(compact('year', 'semester'))
            ->where('grade', 'P')->exists();
        return compact('action_exist', 'grade_finished');
    }

        public function getAllCourses($codes = []): array
        {
            $courses = [];
            if (empty($codes)) {
                $cs = DB::table('courses')->select()->get()->toArray();
                foreach ($cs as $course) {
                    $courses[$course->type][$course->semester][$course->elective][$course->departments_id][] = (array)$course;
                }
            } else {
                $cs = DB::table('courses')->select()->whereIn('full_code', $codes)
                ->orderBy('elective')->get()->toArray();
                foreach ($cs as $course) {
                    $courses[] = (array)$course;
                }
            }
            return $courses;
        }

    public function removeArabicChar(?string $string): ?string
    {
        if (!is_null($string)) {
            $string = str_replace("ى", "ي", $string);
            $string = str_replace("ة", "ه", $string);
            $string = str_replace(["أ", "آ", "إ"], "ا", $string);
            $string = str_replace("پ", "ب", $string);
            $string = str_replace("ژ", "ز", $string);
            $string = str_replace("ڤ", "ف", $string);
            $string = str_replace("گ", "ك", $string);
            $string = str_replace("ﷲ", "الله", $string);
        }
        return $string;
    }

    public function getDistinctValues(string $table, array $columns, bool $sort = true): array
    {
        $filter_data = [];
        foreach ($columns as $column) {
            $query = DB::table($table)->select($column)->distinct()->whereNotNull($column)->groupBy($column);
            if ($sort) {
                $query = $query->orderByRaw("COUNT($column) DESC");
            }
            $query = $query->pluck($column)->toArray();
            $filter_data[$column] = $query;
        }
        return $filter_data;
    }

    public function gradeToPoint(): array
    {
        $grade = DB::table('data')->where('data_key', 'grade')->select('value')
            ->pluck('value')->toArray();
        $grades = [];
        $degree = [];
        foreach ($grade as $value) {
            $arr = explode('|', $value);
            $grades[$arr[0]] = $arr[1];
            $degree[$arr[0]] = $arr[2];
        }
        return [$grades, $degree];
    }

    public function degreeToGrade(float $degree, array $degrees = null): string
    {
        $degrees = $degrees ?: $this->gradeToPoint()[1];
        arsort($degrees);
        foreach ($degrees as $grade => $deg) {
            if ($degree >= explode('-', $deg)[0] and $degree <= explode('-', $deg)[1]) {
                return $grade;
            }
        }
        return '';
    }

    public function gradeToCgpa(string $grade): array
    {
        $data = [];
        switch ($grade) {
            case 'ممتاز':
                $data[] = ['cgpa', '>=', '3.40'];
                break;
            case 'جيد جدا':
                $data[] = ['cgpa', '<', '3.40'];
                $data[] = ['cgpa', '>=', '3.00'];
                break;
            case 'جيد':
                $data[] = ['cgpa', '<', '3.00'];
                $data[] = ['cgpa', '>=', '2.40'];
                break;
            case 'مقبول':
                $data[] = ['cgpa', '<', '2.40'];
                $data[] = ['cgpa', '>=', '2.00'];
                break;
            case 'ضعيف':
                $data[] = ['cgpa', '<', '2.00'];
                break;
        }
        return $data;
    }

    public function getSectionNumbers($specialization, $study_group = null)
    {
        if ($specialization == 'ترميم الاثار و المقتنيات الفنية') {
            $numbers = (array)DB::table('hour_payment_arabic')->where('id', 5)->first();
        } else {
            $numbers = (array)DB::table('hour_payment_english')->where('id', 5)->first();
        }
        if (!is_null($study_group)) {
            switch ($study_group) {
                case 'الاولي':
                    return $numbers['first'];
                case 'الثانية':
                    return $numbers['second'];
                case 'الثالثة':
                    return $numbers['third'];
                case 'الرابعة':
                    return $numbers['fourth'];
            }
        }
        return $numbers;
    }

    public function getElectiveCourseCount(): array
    {
        return [
            '1' => 1,
            '2' => 1,

            '3' => 0,
            '4' => 0,

            '5' => 2,
            '6' => 1,

            '7' => 1,
            '8' => 2,
        ];

    }
    public function getElectiveCourseCountByGroup(): array
    {

        return [
            'الاولي' => 2,
            'الثانية' => 0,
            'الثالثة' => 3,
            'الرابعة' => 3,
        ];

    }
}
