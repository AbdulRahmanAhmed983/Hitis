<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Http\Traits\DataTrait;
use App\Http\Traits\StudentTrait;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ControlController extends Controller
{
    use DataTrait, StudentTrait;

    public function uploadResultsIndex()
    {
        $courses = DB::table('courses')->where('is_selected', 1)
            ->select('name', 'full_code')->get()->toArray();
        return view('control.upload_results', compact('courses'));
    }

    public function uploadResults(Request $request)
    {
        set_time_limit(0);
        $semester_active = DB::table('semester')->where('id', 1)->first();
        if (!array_search(2, (array)$semester_active) or $semester_active->academic_registration == 1) {
            return response()->json(['يجب غلق التسجيل اولاً'], 400);
        }
        $courses = DB::table('courses')->where('is_selected', 1)
            ->select('name', 'full_code')->get();
        $validator = Validator::make($request->all(), [
            'course_code' => 'required|in:note,' . $courses->pluck('full_code')->implode(','),
            'result' => 'required|file|mimes:csv,xls,xlsx',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }
        $data = $validator->validated();
        if ($data['course_code'] == 'note') {
            $results = Excel::toArray(null, $data['result'])[0];
            // $validator = Validator::make(['result' => $results], [
            //     'result' => 'array|min:2',
            //     'result.*' => 'array|size:2',
            //     'result.*.*' => 'required|string|max:255|not_regex:/[#;<>]/u',
            //     'result.0.0' => 'required|in:student code',
            //     'result.0.1' => 'required|in:note',
            // ]);
            // if ($validator->fails()) {
            //     return response()->json([$validator->errors()->all()], 400);
            // }
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $students = array_column(array_diff_key($results, ['0']), '0');
            $students = array_combine($students, array_fill(0, count($students), 0));
            $status = [];
            for ($i = 1; $i < count($results); $i++) {
                if (Student::where('username', $results[$i][0])->exists()) {
                    if ($students[$results[$i][0]] == 0) {
                        try {
                            DB::transaction(function () use ($year, $data, $semester, $i, &$students, $results) {
                                DB::table('students_notes')->updateOrInsert([
                                    'student_code' => $results[$i][0],
                                    'year' => $year,
                                    'semester' => $semester
                                ], ['note' => $results[$i][1]]);
                                $students[$results[$i][0]] = 1;
                            });
                        } catch (Exception $e) {
                            $status['errors'][] = ['row' => ($i + 1),
                                'message' => 'خطأ في الإتصال برجاء إعادة المحاولة.'];
                        }
                    } else {
                        $status['errors'][] = ['row' => ($i + 1), 'message' => 'كود الطالب مكرر'];
                    }
                } else {
                    $status['errors'][] = ['row' => ($i + 1),
                        'message' => 'كود الطالب غير صحيح'];
                }
            }
            return response()->json([$status, []], 200);
        } else {

            $results = Excel::toArray(null, $data['result'])[0];
            // $validator = Validator::make(['result' => $results], [
            //     'result' => 'array|min:2',
            //     'result.*' => 'array|size:7',
            //     'result.0.0' => 'required|in:كود الطالب',
            //     'result.0.1' => 'required|in:رقم الجلوس',
            //     'result.0.2' => 'required|in:التحريري,التحريرى',
            //     'result.0.3' => 'required|in:اعمال السنه,اعمال السنة',
            //     'result.0.4' => 'required|in:المجموع',
            //     'result.0.5' => 'required|in:التقدير',
            //     'result.0.6' => 'required|in:ملاحظة,ملاحظه',
            // ]);
            // if ($validator->fails()) {
            //     return response()->json([$validator->errors()->all()], 400);
            // }
            unset($results[0]);
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $results = array_merge($results,$results,$results,$results);
            unset($results[0]);
            $students = DB::table('registration')->select('registration.student_code')
                ->where([['registration.course_code', '=', $data['course_code']], ['registration.year', '=', $year],
                    ['registration.semester', '=', $semester]])
                ->join('registration_semester', function ($join) {
                    $join->on('registration.student_code', '=', 'registration_semester.student_code')
                        ->on('registration.year', '=', 'registration_semester.year')
                        ->on('registration.semester', '=', 'registration_semester.semester');
                })->where('guidance', 1)->pluck('student_code')->toArray();
            [$grade, $degree] = $this->gradeToPoint();
            // $validator = Validator::make(['result' => $results], [
            //     'result' => 'array|min:1',
            //     'result.*' => 'array|size:7',
            //     'result.*.0' => 'required|regex:/^[RT][0-9]{6}$/u|in:' . implode(',', $students),
            //     'result.*.1' => ['required', function ($attribute, $value, $fail) use ($results) {
            //         $i = explode('.', $attribute)[1];
            //         $student_code = $results[$i][0];
            //         if (!$this->checkSeatingNumber($student_code, $value)) {
            //             $fail('رقم الجلوس غير متطابق مع كود الطالب فى السطر رقم ' . ($i + 1));
            //         }
            //     }],
            //     'result.*.2' => ['required_with:result.*.3,result.*.4', function ($attribute, $value, $fail) {
            //         $i = explode('.', $attribute)[1];
            //         if (is_numeric($value) and ($value > 50 or $value < 0)) {
            //             return $fail('درجة التحريرى اكثر من 50 فى السطر رقم ' . ($i + 1));
            //         } elseif (is_string($value) and ($value != 'غائب' and $value != 'تأديب')) {
            //             return $fail('يجب ان تكون القيمه التحريرى (غائب او تأديب) السطر رقم ' . ($i + 1));
            //         }
            //     }],
            //     'result.*.3' => ['required_with:result.*.2,result.*.4', function ($attribute, $value, $fail) {
            //         $i = explode('.', $attribute)[1];
            //         if (is_numeric($value) and ($value > 50 or $value < 0)) {
            //             return $fail('درجة اعمال السنة اكثر من 50 فى السطر رقم ' . ($i + 1));
            //         } elseif (is_string($value) and ($value != 'غائب' and $value != 'تأديب')) {
            //             return $fail('يجب ان تكون القيمه اعمال السنة (غائب او تأديب) السطر رقم ' . ($i + 1));
            //         }
            //     }],
            //     'result.*.4' => ['required_with:result.*.2,result.*.3', function ($attribute, $value, $fail) use ($results) {
            //         $i = explode('.', $attribute)[1];
            //         if (is_numeric($value) and ($value != ($results[$i][2] + $results[$i][3]))) {
            //             return $fail('درجة المجموع لا تساوى اعمال السنة + التحريرى فى السطر رقم ' . ($i + 1));
            //         } elseif (is_string($value)) {
            //             if (!in_array($value, ['الغاء', 'غائب', 'تأديب'])) {
            //                 return $fail('يجب ان تكون القيمه المجموع تساوى اعمال السنة و التحريرى السطر رقم ' . ($i + 1));
            //             }
            //             if (in_array($value, ['غائب', 'تأديب']) and ($results[$i][2] != $results[$i][3] or $value != $results[$i][2])) {
            //                 return $fail('يجب ان تكون القيمه المجموع تساوى اعمال السنة و التحريرى السطر رقم ' . ($i + 1));
            //             }
            //         }
            //     }],
            //     'result.*.5' => ['required', function ($attribute, $value, $fail) use ($degree, $results) {
            //         $i = explode('.', $attribute)[1];
            //         if (is_null($results[$i][2]) and is_null($results[$i][3]) and is_null($results[$i][4])
            //             and !in_array($value, ['IC', 'W', 'FX'])) {
            //             return $fail('يجب ان تكون القيمه التقدير تساوى IC او W او FX فى السطر رقم ' . ($i + 1));
            //         }
            //         if (is_numeric($results[$i][4]) and ($value != $this->degreeToGrade($results[$i][4], $degree))) {
            //             return $fail('التقدير خطأ فى السطر رقم ' . ($i + 1));
            //         } elseif (is_string($results[$i][4]) and (!in_array($results[$i][4], ['الغاء', 'غائب', 'تأديب'])
            //                 or $value != 'F')) {
            //             return $fail('يجب ان تكون القيمه التقدير تساوى F السطر رقم ' . ($i + 1));
            //         }
            //     }],
            //     'result.*.6' => 'nullable|string|max:255|not_regex:/[#;<>]/u',
            // ]);
            // if ($validator->fails()) {
            //     return response()->json([$validator->errors()->all()], 400);
            // }
            $students = array_combine($students, array_fill(0, count($students), 0));
            $status = [];
            for ($i = 1; $i <= count($results); $i++) {
                if (array_key_exists($results[$i][0], $students)) {
                    if (in_array($results[$i][5], array_keys($grade))) {
                        if ($students[$results[$i][0]] == 0) {
                            try {
                                DB::transaction(function () use ($year, $data, $semester, $i, &$students, $results) {
                                    DB::table('registration')->where([
                                        ['student_code', '=', $results[$i][0]], ['course_code', '=', $data['course_code']],
                                        ['year', '=', $year], ['semester', '=', $semester]])
                                        ->update(['grade' => $results[$i][5], 'written' => $results[$i][2],
                                            'yearly_performance_score' => $results[$i][3], 'note' => $results[$i][6]]);
                                    $students[$results[$i][0]] = 1;
                                    Student::find($results[$i][0])->update($this->calculateCGPAStudent($results[$i][0]));
                                });
                            } catch (Exception $e) {
                                $status['errors'][] = ['row' => ($i + 1),
                                    'message' => 'خطأ في الإتصال برجاء إعادة المحاولة.'];
                            }
                        } else {
                            $status['errors'][] = ['row' => ($i + 1),
                                'message' => 'كود الطالب مكرر'];
                        }
                    } else {
                        $status['errors'][] = ['row' => ($i + 1),
                            'message' => 'التقدير غير صحيح.'];
                    }
                } else {
                    $status['errors'][] = ['row' => ($i + 1),
                        'message' => 'كود الطالب غير صحيح او إنه غير مسجل في الماده.'];
                }
            }
            $students = array_keys($students, 0);
            $miss_grades = DB::table('students')->whereIn('username', $students)
                ->select('username', 'name', 'study_group')
                ->join('registration', function ($join) use ($data, $semester, $year) {
                    $join->on('students.username', '=', 'registration.student_code')
                        ->where('registration.year', $year)->where('registration.semester', $semester)
                        ->where('course_code', $data['course_code'])->where('grade', 'P');
                })->get()->toArray();
            return response()->json([$status, $miss_grades], 200);
        }
    }

    public function configIndex()
    {
        $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        return view('control.config', compact('filter_data'));
    }

    public function seatingNumbersExcel(Request $request)
    {
        $semester = $this->getCurrentSemester();
        $year = $this->getCurrentYear();
        if ($semester == '') {
            return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
        }
        $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $data = $request->validate([
            'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
            'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
        ]);
        $department = DB::table('departments')->select('id','name')->where('id',$data['departments_id'])->pluck('name')[0];

        $students = Student::where($data)->select('seating_number', 'student_code', 'name')
            ->join('seating_numbers', 'seating_numbers.student_code', '=', 'username')
            ->where('year', $year)->orderBy('seating_number')->get()->toArray();
        $heading = [
            [
                'col' => 6,
                'text' => ['المعهد العالى للحاسب الالي و نظم المعلومات الفرقة الدراسية ' . $data['study_group']
                    . ' شعبة نظم المعلومات الادارية باللغة ' .
                    (($data['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'العربية' : '') .
                    (($data['specialization'] == 'سياحة') ? 'الانجليزية' : '')
                    . ' العام الدراسي ' . $this->getCurrentYear()],
            ]
        ];
        $headers = [
            [
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'رقم الجلوس'
                ],
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
            ]
        ];
        try {
            return Excel::download(new ReportsExport($heading, $headers, $students),
                'ارقام جلوس الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] .''.$department. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function reportIndex()
    {
        $filter_data = $this->getDistinctValues('students', ['specialization','departments_id']);
        $filter_data += $this->getDistinctValues('registration_years', ['study_group', 'year']);
        return view('control.reports', compact('filter_data'));
    }

    public function printStudentsGrade(Request $request)
    {
        $filter_data = $this->getDistinctValues('students', ['specialization','departments_id']);
        $filter_data += $this->getDistinctValues('registration_years', ['study_group', 'year']);
        $data = $request->validate([
            'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
            'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
            'year' => 'required|in:' . implode(',', $filter_data['year']),
            'count' => ['required', function ($attribute, $value, $fail) {
                $arr = explode('-', $value);
                if (count($arr) > 2)
                    $fail('خطأ فى البيانات');
                if (((int)$arr[0]) != $arr[0] or ((int)$arr[1]) != $arr[1])
                    $fail('خطأ فى البيانات');
                if ((int)$arr[0] < 1 or (int)$arr[1] < 1)
                    $fail('خطأ فى البيانات');
            }],
        ]);
        set_time_limit(0);
        $data['registration_years.study_group'] = $data['study_group'];
        $data['registration_years.year'] = $year = $data['year'];
        $count = $data['count'];
        unset($data['study_group'], $data['count'], $data['year']);
        $students = DB::table('registration_years')->select(['students.*', 'seating_number'])->where($data)
            ->join('students', 'registration_years.student_code', '=', 'students.username')
            ->join('seating_numbers', function ($join) use ($year) {
                $join->on('seating_numbers.student_code', '=', 'students.username')
                    ->where('seating_numbers.year', '=', $year);
            })->orderBy('seating_number')->get()->toArray();
        $pages = [];
        [$index, $iterate] = explode('-', $count);
        $start = ($index - 1) * 200;
        $end = $start + $iterate;
        for ($i = $start; $i < $end; $i++) {
            $student = (array)$students[$i];
            $student['study_group'] = $data['registration_years.study_group'];
            [$courses, $grades,] = $this->getStudentRegistrationStatus($student['username']);
            $student['certificate_obtained'] = ($student['certificate_obtained'] == 'لا يوجد شهاده') ? '' :
                $student['certificate_obtained'];
            $total_earned_hour = 0;
            $total_hour = 0;
            $student['study_group-2'] = str_replace(['الاولي', 'الثانية', 'الثالثة', 'الرابعة'],
                ['الاول', 'الثانى', 'الثالث', 'الرابع'], $student['study_group']);
            $student['lang'] = str_replace(['سياحة', 'ترميم الاثار و المقتنيات الفنية'],
                ['الانجليزية', 'العربية'], $student['specialization']);
            if (isset($grades[$year]) and isset($courses[$year])) {
                foreach ($grades[$year] as $grade) {
                    $total_hour += $grade['hours'];
                    $total_earned_hour += $grade['earned_hours'];
                }
            } else {
                $courses[$year] = [];
                $grades[$year] = [];
            }
            $note = DB::table('students_notes')->where('student_code', $student['username'])
                ->where('year', $year);
            $notes = $note->exists() ? $note->orderBy('semester')->get()->toArray() : null;
            $pages[] = compact('grades', 'year', 'student', 'total_hour', 'total_earned_hour',
                'notes', 'courses');
        }
        return view('control.grade_to_pdf_2', ['pages' => $pages]);
    }
     public function printStudentsGrade2(Request $request)
    {
        $filter_data = $this->getDistinctValues('students', ['specialization','departments_id']);
        $filter_data += $this->getDistinctValues('registration_years', ['study_group', 'year']);
        $data = $request->validate([
            'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
            'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
            'year' => 'required|in:' . implode(',', $filter_data['year']),
            'count' => ['required', function ($attribute, $value, $fail) {
                $arr = explode('-', $value);
                if (count($arr) > 2)
                    $fail('خطأ فى البيانات');
                if (((int)$arr[0]) != $arr[0] or ((int)$arr[1]) != $arr[1])
                    $fail('خطأ فى البيانات');
                if ((int)$arr[0] < 1 or (int)$arr[1] < 1)
                    $fail('خطأ فى البيانات');
            }],
        ]);
        set_time_limit(0);
        $data['registration_years.study_group'] = $data['study_group'];
        $data['registration_years.year'] = $year = $data['year'];
        $count = $data['count'];
        unset($data['study_group'], $data['count'], $data['year']);
        $students = DB::table('registration_years')->select(['students.*', 'seating_number'])->where($data)
            ->join('students', 'registration_years.student_code', '=', 'students.username')
            ->join('seating_numbers', function ($join) use ($year) {
                $join->on('seating_numbers.student_code', '=', 'students.username')
                    ->where('seating_numbers.year', '=', $year);
            })->orderBy('seating_number')->get()->toArray();
        $pages = [];
        [$index, $iterate] = explode('-', $count);
        $start = ($index - 1) * 200;
        $end = $start + $iterate;
        for ($i = $start; $i < $end; $i++) {
            $student = (array)$students[$i];
            $student['study_group'] = $data['registration_years.study_group'];
            [$courses, $grades,] = $this->getStudentRegistrationStatus($student['username']);
            $student['certificate_obtained'] = ($student['certificate_obtained'] == 'لا يوجد شهاده') ? '' :
                $student['certificate_obtained'];
            $total_earned_hour = 0;
            $total_hour = 0;
            $student['study_group-2'] = str_replace(['الاولي', 'الثانية', 'الثالثة', 'الرابعة'],
                ['الاول', 'الثانى', 'الثالث', 'الرابع'], $student['study_group']);
            $student['lang'] = str_replace(['سياحة', 'ترميم الاثار و المقتنيات الفنية'],
                ['الانجليزية', 'العربية'], $student['specialization']);
            if (isset($grades[$year]) and isset($courses[$year])) {
                foreach ($grades[$year] as $grade) {
                    $total_hour += $grade['hours'];
                    $total_earned_hour += $grade['earned_hours'];
                }
            } else {
                $courses[$year] = [];
                $grades[$year] = [];
            }
            $note = DB::table('students_notes')->where('student_code', $student['username'])
                ->where('year', $year);
            $notes = $note->exists() ? $note->orderBy('semester')->get()->toArray() : null;
            $pages[] = compact('grades', 'year', 'student', 'total_hour', 'total_earned_hour',
                'notes', 'courses');
        }
        return view('control.grade_to_pdf', ['pages' => $pages]);
    }

             // summer report
    public function printSummerGrade(Request $request)
    {
        $filter_data = $this->getDistinctValues('students', ['specialization','departments_id']);
        $filter_data += $this->getDistinctValues('registration_years', ['study_group', 'year']);
        $data = $request->validate([
            'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
            'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
            'year' => 'required|in:' . implode(',', $filter_data['year']),
            'count' => ['required', function ($attribute, $value, $fail) {
                $arr = explode('-', $value);
                if (count($arr) > 2)
                    $fail('خطأ فى البيانات');
                if (((int)$arr[0]) != $arr[0] or ((int)$arr[1]) != $arr[1])
                    $fail('خطأ فى البيانات');
                if ((int)$arr[0] < 1 or (int)$arr[1] < 1)
                    $fail('خطأ فى البيانات');
            }],
        ]);
        set_time_limit(0);
        $data['registration_years.study_group'] = $data['study_group'];
        $data['registration_years.year'] = $year = $data['year'];
        $count = $data['count'];
        unset($data['study_group'], $data['count'], $data['year']);
        $students = DB::table('registration_years')->select(['students.*', 'seating_number'])->where($data)
            ->join('students', 'registration_years.student_code', '=', 'students.username')
            ->join('seating_numbers', function ($join) use ($year) {
                $join->on('seating_numbers.student_code', '=', 'students.username')
                    ->where('seating_numbers.year', '=', $year);
            })
            ->orderBy('seating_number')->get()->toArray();
        $pages = [];
        [$index, $iterate] = explode('-', $count);
        $start = ($index - 1) * 200;
        $end = $start + $iterate;
        for ($i = $start; $i < $end; $i++) {
            $student = (array)$students[$i];
            $student['study_group'] = $data['registration_years.study_group'];
            if ( $this->getStudentRegistrationSummer($student['username'])===[])
            {
                continue;
            }
            [$courses, $grades,] = $this->getStudentRegistrationSummer($student['username']);
            $student['certificate_obtained'] = ($student['certificate_obtained'] == 'لا يوجد شهاده') ? '' :
                $student['certificate_obtained'];
            $total_earned_hour = 0;
            $total_hour = 0;
            $student['study_group-2'] = str_replace(['الاولي', 'الثانية', 'الثالثة', 'الرابعة'],
                ['الاول', 'الثانى', 'الثالث', 'الرابع'], $student['study_group']);
            $student['lang'] = str_replace(['سياحة', 'ترميم الاثار و المقتنيات الفنية'],
                ['الانجليزية', 'العربية'], $student['specialization']);
            if (isset($grades[$year]) and isset($courses[$year])) {
                foreach ($grades[$year] as $grade) {
                    $total_hour += $grade['hours'];
                    $total_earned_hour += $grade['earned_hours'];
                }
            } else {
                $courses[$year] = [];
                $grades[$year] =  [];
            }

            $note = DB::table('students_notes')->where('student_code', $student['username'])
                ->where('year', $year);
            $notes = $note->exists() ? $note->orderBy('semester')->get()->toArray() : null;
            $pages[] = compact('grades', 'year', 'student', 'total_hour', 'total_earned_hour',
                'notes', 'courses');
        }
        return view('control.grade_to_pdf', ['pages' => $pages]);
    }
    public function checkGradeNumber(Request $request)
    {
        if ($request->ajax()) {
            $filter_data = $this->getDistinctValues('students', ['specialization','departments_id']);
            $filter_data += $this->getDistinctValues('registration_years', ['study_group', 'year']);
            $validator = Validator::make($request->all(), [
                'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
                'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
                'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
                'year' => 'required|in:' . implode(',', $filter_data['year']),
            ]);
            if ($validator->fails()) {
                return Response()->json(['error' => 'بيانات غير صالحة'], 400);
            }
            $data = $validator->validate();
            $data['registration_years.study_group'] = $data['study_group'];
            $data['registration_years.year'] = $year = $data['year'];
            unset($data['study_group'], $data['year']);
            $count = DB::table('registration_years')->select('students.*')->where($data)
                ->join('students', 'registration_years.student_code', '=', 'students.username')
                ->join('seating_numbers', function ($join) use ($year) {
                    $join->on('seating_numbers.student_code', '=', 'students.username')
                        ->where('seating_numbers.year', '=', $year);
                })->orderBy('students.name')->count();
            $output = '';
            for ($i = 1; $i <= (int)($count / 200); $i++) {
                $output .= "<option>$i-200</option>";
            }
            $output .= "<option>$i-" . ($count % 200) . "</option>";
            return Response($output, 200);
        }
        abort(404);
    }

    public function editResultsIndex()
    {
        $courses = DB::table('registration')->select(['name', 'course_code', 'year', 'registration.semester'])
            ->distinct()->join('courses', 'registration.course_code', '=', 'courses.full_code')
            ->orderBy('year')->orderBy('semester')->get()->groupBy(['year', 'semester'])->toArray();
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        unset($courses[$year][$semester]);
        return view('control.edit_results', compact('courses'));
    }

    public function editResults(Request $request)
    {
        set_time_limit(0);
        $years = DB::table('registration')->distinct()->select('year')->pluck('year')->toArray();
        $semesters = DB::table('registration')->distinct()->select('semester')->pluck('semester')
            ->toArray();
        $validator = Validator::make($request->all(), [
            'semester' => 'required|in:' . implode(',', $semesters),
            'year' => 'required|in:' . implode(',', $years),
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }
        ['year' => $year, 'semester' => $semester] = $validator->validated();
        if ($year == $this->getCurrentYear() and $semester == $this->getCurrentSemester()) {
            return response()->json(['لا يمكن اختيار النرم الحالى'], 400);
        }
        $courses = DB::table('registration')->distinct()->select('course_code')
            ->where(compact('year', 'semester'))->pluck('course_code')->toArray();
        $validator = Validator::make($request->all(), [
            'course_code' => 'required|in:' . implode(',', $courses),
            'result' => 'required|file|mimes:csv,xls,xlsx',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }
        $data = $validator->validated();
        $results = Excel::toArray(null, $data['result'])[0];
        $validator = Validator::make(['result' => $results], [
            'result' => 'array|min:2',
            'result.*' => 'array|size:7',
            'result.0.0' => 'required|in:كود الطالب',
            'result.0.1' => 'required|in:رقم الجلوس',
            'result.0.2' => 'required|in:التحريري,التحريرى',
            'result.0.3' => 'required|in:اعمال السنه,اعمال السنة',
            'result.0.4' => 'required|in:المجموع',
            'result.0.5' => 'required|in:التقدير',
             'result.0.6' => 'required|in:ملاحظة,ملاحظه',
        ]);
        if ($validator->fails()) {
            return response()->json([$validator->errors()->all()], 400);
        }
        unset($results[0]);
        $students = DB::table('registration')->select('registration.student_code')
            ->where([['registration.course_code', '=', $data['course_code']], ['registration.year', '=', $year],
                ['registration.semester', '=', $semester]])
            ->join('registration_semester', function ($join) {
                $join->on('registration.student_code', '=', 'registration_semester.student_code')
                    ->on('registration.year', '=', 'registration_semester.year')
                    ->on('registration.semester', '=', 'registration_semester.semester');
            })->where('guidance', 1)->pluck('student_code')->toArray();
        [$grade, $degree] = $this->gradeToPoint();
        $validator = Validator::make(['result' => $results], [
            'result' => 'array|min:1',
            'result.*' => 'array|size:7',
            // 'result.*.0' => 'required|regex:/^[RT][0-9]{6}$/u|distinct|in:' . implode(',', $students),

            'result.*.1' => ['required', 'distinct', function ($attribute, $value, $fail) use ($year, $results) {
                $i = explode('.', $attribute)[1];
                $student_code = $results[$i][0];
                if (!$this->checkSeatingNumber($student_code, $value, $year)) {
                    $fail('رقم الجلوس غير متطابق مع كود الطالب فى السطر رقم ' . ($i + 1));
                }
            }],
            'result.*.2' => ['required_with:result.*.3,result.*.4', function ($attribute, $value, $fail) {
                $i = explode('.', $attribute)[1];
                if (is_numeric($value) and ($value > 50 or $value < 0)) {
                    return $fail('درجة التحريرى اكثر من 50 فى السطر رقم ' . ($i + 1));
                } elseif (is_string($value) and ($value != 'غائب' and $value != 'تأديب')) {
                    return $fail('يجب ان تكون القيمه التحريرى (غائب او تأديب) السطر رقم ' . ($i + 1));
                }
            }],
            'result.*.3' => ['required_with:result.*.2,result.*.4', function ($attribute, $value, $fail) {
                $i = explode('.', $attribute)[1];
                if (is_numeric($value) and ($value > 50 or $value < 0)) {
                    return $fail('درجة اعمال السنة اكثر من 50 فى السطر رقم ' . ($i + 1));
                } elseif (is_string($value) and ($value != 'غائب' and $value != 'تأديب')) {
                    return $fail('يجب ان تكون القيمه اعمال السنة (غائب او تأديب) السطر رقم ' . ($i + 1));
                }
            }],
            'result.*.4' => ['required_with:result.*.2,result.*.3', function ($attribute, $value, $fail) use ($results) {
                $i = explode('.', $attribute)[1];
                if (is_numeric($value) and ($value != ($results[$i][2] + $results[$i][3]))) {
                    return $fail('درجة المجموع لا تساوى اعمال السنة + التحريرى فى السطر رقم ' . ($i + 1));
                } elseif (is_string($value)) {
                    // if (!in_array($value, ['الغاء', 'غائب', 'تأديب'])) {
                    //     return $fail('يجب ان تكون القيمه المجموع تساوى اعمال السنة و التحريرى السطر رقم ' . ($i + 1));
                    // }
                    if (in_array($value, ['غائب', 'تأديب']) and ($results[$i][2] != $results[$i][3] or $value != $results[$i][2])) {
                        return $fail('يجب ان تكون القيمه المجموع تساوى اعمال السنة و التحريرى السطر رقم ' . ($i + 1));
                    }
                }
            }],
            'result.*.5' => ['required', function ($attribute, $value, $fail) use ($degree, $results) {
                $i = explode('.', $attribute)[1];
                if (is_null($results[$i][2]) and is_null($results[$i][3]) and is_null($results[$i][4])
                    and !in_array($value, ['IC', 'W', 'FX'])) {
                    return $fail('يجب ان تكون القيمه التقدير تساوى IC او W او FX فى السطر رقم ' . ($i + 1));
                }
                if (is_numeric($results[$i][4]) and ($value != $this->degreeToGrade($results[$i][4], $degree))) {
                    return $fail('التقدير خطأ فى السطر رقم ' . ($i + 1));
                 }
                //elseif (is_string($results[$i][4]) and (!in_array($results[$i][4], ['الغاء', 'غائب', 'تأديب'])
                //         or $value != 'F')) {
                //     return $fail('يجب ان تكون القيمه التقدير تساوى F السطر رقم ' . ($i + 1));
                // }
            }],
             'result.*.6' => 'nullable|string|max:255|not_regex:/[#;<>]/u',
        ]);
        if ($validator->fails()) {
            return response()->json([$validator->errors()->all()], 400);
        }
        $students = array_combine($students, array_fill(0, count($students), 0));
        $status = [];
        DB::beginTransaction();
        for ($i = 1; $i <= count($results); $i++) {
            try {
                DB::table('registration')->where([
                    ['student_code', '=', $results[$i][0]], ['course_code', '=', $data['course_code']],
                    ['year', '=', $year], ['semester', '=', $semester]])
                    ->update(['grade' => $results[$i][5], 'written' => $results[$i][2],
                        'yearly_performance_score' => $results[$i][3], 'note' => $results[$i][6]]);
                Student::find($results[$i][0])->update($this->calculateCGPAStudent($results[$i][0]));
                $students[$results[$i][0]] = 1;
            } catch (Exception $e) {
                $status['errors'][] = ['row' => ($i + 1), 'message' => 'خطأ في الإتصال برجاء إعادة المحاولة.'];
                break;
            }
        }
        $students = array_keys($students, 0);
        $miss_grades = [];
        DB::commit();
//        if (count($students) > 0) {
//            DB::rollBack();
//            $miss_grades = DB::table('students')->whereIn('username', $students)
//                ->select('username', 'name', 'study_group')
//                ->join('registration', function ($join) use ($data, $semester, $year) {
//                    $join->on('students.username', '=', 'registration.student_code')
//                        ->where('registration.year', $year)->where('registration.semester', $semester)
//                        ->where('course_code', $data['course_code']);
//                })->union(DB::table('deleted_students')->whereIn('username', $students)
//                    ->select('username', 'name', 'study_group')
//                    ->join('registration', function ($join) use ($data, $semester, $year) {
//                        $join->on('deleted_students.username', '=', 'registration.student_code')
//                            ->where('registration.year', $year)->where('registration.semester', $semester)
//                            ->where('course_code', $data['course_code']);
//                    }))->get()->toArray();
//        } else {
//            DB::commit();
//        }
        return response()->json([$status, $miss_grades], 200);
    }
}
