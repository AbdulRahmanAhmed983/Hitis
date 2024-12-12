<?php

namespace App\Http\Controllers;

use App\Http\Traits\DataTrait;
use App\Http\Traits\StudentTrait;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use App\Exports\ReportsExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ReportsController extends Controller
{
    use DataTrait, StudentTrait;

    public function reportsIndex()
    {
        $year = $this->getCurrentYear();
        $filter_data_course = $this->getDistinctValues('registration', ['course_code', 'year']);
        $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $filter_data = array_merge($filter_data,
            $this->getDistinctValues('students_semesters', ['year', 'semester'], false));
        return view('reports.reports', compact('filter_data','filter_data_course','year'));
    }

    public function enlistmentReport(Request $request)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        if ($semester == '') {
            return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
        }
        $filter_data = $this->getDistinctValues('students', ['study_group']);
        $data = $request->validate([
            'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
            'enlistment_status' => 'required|in:اعفاء مؤقت,له حق التأجيل',
        ]);
        $data['nationality'] = 'مصري';
        $data['gender'] = 'ذكر';
        $students = Student::where($data)->select(['name', 'username', 'national_id', 'issuer_national_number',
            'recruitment_area', 'position_of_recruitment', 'decision_number', 'recruitment_notes', 'military_number'])
            ->selectRaw('DAY(birth_date) as day,MONTH(birth_date) as month,YEAR(birth_date) as year,
            DAY(decision_date) as decision_day,MONTH(decision_date) as decision_month,YEAR(decision_date) as decision_year,
            DAY(expiry_date) as expiry_day,MONTH(expiry_date) as expiry_month,YEAR(expiry_date) as expiry_year,
            DATE_ADD(birth_date, INTERVAL 28 YEAR) as date_reached')
            ->orderBy('name')->orderBy('username')->get()->toArray();
        if ($data['enlistment_status'] == 'له حق التأجيل') {
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'م'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'رقم الجلوس'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'الاسم'
                    ],
                    [
                        'col' => 3,
                        'row' => 1,
                        'text' => 'تاريخ الميلاد'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'رقم البطاقة'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'جهة الاصدار'
                    ],
                    [
                        'col' => 3,
                        'row' => 1,
                        'text' => 'رقم البطاقة العسكرية'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'منطقة التجنيد'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'تاريخ تصدير'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'موقف الطالب من التجنيد'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'تاريخ بلوغ سن 28'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'تاريخ الفصل او التخرج'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'ملاحظات'
                    ],
                ],
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'يوم'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'شهر'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'سنة'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'سنة الميلاد'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود المركز او القسم'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم المسلسل'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'النموذج للاتصال العسكرى'
                    ],
                ]
            ];
        } else {
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'م'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'رقم الجلوس'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'الاسم'
                    ],
                    [
                        'col' => 3,
                        'row' => 1,
                        'text' => 'تاريخ الميلاد'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'رقم البطاقة'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'جهة الاصدار'
                    ],
                    [
                        'col' => 3,
                        'row' => 1,
                        'text' => 'رقم البطاقة العسكرية'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'موقف الطالب من التجنيد'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'رقم القرار'
                    ],
                    [
                        'col' => 3,
                        'row' => 2,
                        'text' => 'تاريخ القرار'
                    ],
                    [
                        'col' => 3,
                        'row' => 2,
                        'text' => 'تاريخ انتهاء شهادة الاعفاء المؤقتة'
                    ],
                    [
                        'col' => 1,
                        'row' => 2,
                        'text' => 'ملاحظات'
                    ],
                ],
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'يوم'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'شهر'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'سنة'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'سنة الميلاد'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود المركز او القسم'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم المسلسل'
                    ],
                ]
            ];
        }
        $export_data = [];
        for ($i = 0; $i < count($students); $i++) {
            $export_data[$i][] = $i + 1;
            $seating_number = $this->getSeatingNumber($students[$i]['username'], $year);
            $export_data[$i][] = (empty($seating_number)) ? '' : $seating_number->seating_number;
            $export_data[$i][] = $students[$i]['name'];
            $export_data[$i][] = $students[$i]['day'];
            $export_data[$i][] = $students[$i]['month'];
            $export_data[$i][] = $students[$i]['year'];
            $export_data[$i][] = $students[$i]['national_id'];
            $export_data[$i][] = $students[$i]['issuer_national_number'];
            $export_data[$i] = array_merge($export_data[$i], array_reverse(
                explode('/', $students[$i]['military_number'])));
            if ($data['enlistment_status'] == 'له حق التأجيل') {
                $export_data[$i][] = $students[$i]['recruitment_area'];
                $export_data[$i][] = '';
            }
            $export_data[$i][] = $students[$i]['position_of_recruitment'];
            if ($data['enlistment_status'] == 'له حق التأجيل') {
                $export_data[$i][] = $students[$i]['date_reached'];
                $export_data[$i][] = '';
            }
            if ($data['enlistment_status'] == 'اعفاء مؤقت') {
                $export_data[$i][] = $students[$i]['decision_number'];
                $export_data[$i][] = $students[$i]['decision_day'];
                $export_data[$i][] = $students[$i]['decision_month'];
                $export_data[$i][] = $students[$i]['decision_year'];
                $export_data[$i][] = $students[$i]['expiry_day'];
                $export_data[$i][] = $students[$i]['expiry_month'];
                $export_data[$i][] = $students[$i]['expiry_year'];
            }
            $export_data[$i][] = $students[$i]['recruitment_notes'];
        }
        $heading = [
            [
                'col' => 3,
                'text' => ['وزارة التعليم العالى', 'المعهد العالى للحاسب اللآلى و نظم المعلومات', 'ابو قير-الاسكندرية']
            ], [
                'col' => 6,
                'text' => $data['enlistment_status'] == 'له حق التأجيل' ?
                    ['الفرقة ' . $data['study_group'] . ' شعبة نظم المعلومات الإدارية العام الجامعى '
                        . $year] : ($data['enlistment_status'] == 'اعفاء مؤقت' ?
                        ['سجل (الاعفاء المؤقته) بالفرقه ' . $data['study_group'] .
                            ' شعبة نظم المعلومات الإدارية العام الجامعى ' . $year] : [])
                ,
            ], [
                'col' => 2,
                'text' => ['منطقة تجنيد الاسكندرية', 'مكتب الاتصال العسكرى'],
            ]
        ];
        try {
            return Excel::download(new ReportsExport($heading, $headers, $export_data),
                'سجل التجنيد الفرقة ' . $data['study_group'] . '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function studyReport(Request $request)
    {
        $year = $this->getCurrentYear();
        $previous_year = $this->getPreviousYear();
        $semester = $this->getCurrentSemester();
        if ($semester == '') {
            return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
        }
        $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $data = $request->validate([
            'study_group' => 'required|in:' . implode(',', $filter_data['study_group']),
            'specialization' => 'required|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'required|in:' . implode(',', $filter_data['departments_id']),
        ]);

        $students = Student::where($data)->select(['username', 'name', 'registration_date', 'gender', 'nationality','studying_status',
            'religion', 'birth_province', 'certificate_obtained', 'certificate_obtained_date','student_classification', 'apply_classification','address', 'mobile',
            'father_profession', 'recruitment_notes', 'certificate_seating_number', 'certificate_degree',
            'english_degree', 'cgpa', 'notes'])
            ->selectRaw('DAY(birth_date) as day,MONTH(birth_date) as month,YEAR(birth_date) as year')
            ->orderBy('name')->orderBy('username')->get()->toArray();
        $headers = [
            [
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'رقم الجلوس'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'كود الطالب'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الاسم'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'تاريخ قيد الطالب بالمعهد'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'عدد سنين القيد'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'النوع'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الجنسية'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الديانة'
                ],
                [
                    'col' => 3,
                    'row' => 1,
                    'text' => 'تاريخ الميلاد'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'جهة الميلاد'
                ],
                 [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'تصنيف الطالب'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'حالة الطالب'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الشهادة الحاصل عليها'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'رقم جلوس الشهادة'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'مجموع الشهادة'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'درجة السياحة'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'تاريخ الشهادة'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'رقم جلوس الطالب العام السابق'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'المعدل التراكمي'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'نتيجة الطالب العام السابق'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'عنوان الطالب'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'رقم الهاتف'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'مهنة ولى الامر'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'موقف الطالب من التجنيد'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'ملاحظات'
                ],
            ],
            [
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'يوم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'شهر'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'سنة'
                ],
            ]
        ];
        $department = DB::table('departments')->select('id','name')->where('id',$data['departments_id'])->pluck('name')[0];

        $export_data = [];

        for ($i = 0; $i < count($students); $i++) {
            $seating_number = $this->getSeatingNumber($students[$i]['username'], $year);
            $export_data[$i][] = (empty($seating_number)) ? '' : $seating_number->seating_number;
            $export_data[$i][] = $students[$i]['username'];
            $export_data[$i][] = $students[$i]['name'];
            $export_data[$i][] = $students[$i]['registration_date'];
            $export_data[$i][] = explode('/', $year)[1] - $students[$i]['registration_date'];
            $export_data[$i][] = $students[$i]['gender'];
            $export_data[$i][] = $students[$i]['nationality'];
            $export_data[$i][] = $students[$i]['religion'];
            $export_data[$i][] = $students[$i]['day'];
            $export_data[$i][] = $students[$i]['month'];
            $export_data[$i][] = $students[$i]['year'];
            $export_data[$i][] = $students[$i]['birth_province'];
            $export_data[$i][] = $students[$i]['studying_status'];
             $export_data[$i][] = $student['student_classification'];
             $export_data[$i][] = $student['apply_classification'];
            $export_data[$i][] = $students[$i]['certificate_obtained'];
            $export_data[$i][] = $students[$i]['certificate_seating_number'];
            $export_data[$i][] = $students[$i]['certificate_degree'];
            $export_data[$i][] = $students[$i]['english_degree'];
            $export_data[$i][] = $students[$i]['certificate_obtained_date'];
            $seating_number = $this->getSeatingNumber($students[$i]['username'], $previous_year);
            $export_data[$i][] = (empty($seating_number)) ? '' : $seating_number->seating_number;
            $export_data[$i][] = $students[$i]['cgpa'];
            $export_data[$i][] = $this->getYearGrade($students[$i]['username'], $previous_year);
            $export_data[$i][] = $students[$i]['address'];
            $export_data[$i][] = $students[$i]['mobile'];
            $export_data[$i][] = $students[$i]['father_profession'];
            $export_data[$i][] = $students[$i]['recruitment_notes'];
            $export_data[$i][] = $students[$i]['notes'];
        }
        $heading = [
            [
                'col' => 5,
                'text' => ['المعهد العالى للحاسب الالي و نظم المعلومات الفرقة الدراسية ' . $data['study_group']
                    . ' شعبة نظم المعلومات الادارية باللغة ' .
                    (($data['specialization'] == 'ترميم الاثار و المقتنيات الفنية') ? 'العربية' : '') .
                    (($data['specialization'] == 'سياحة') ? 'الانجليزية' : '')
                    . ' العام الدراسي ' . $this->getCurrentYear()],
            ]
        ];
        try {
            return Excel::download(new ReportsExport($heading, $headers, $export_data),
                'سجل الدراسي الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] .''.$department. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function financeReport(Request $request)
    {
        set_time_limit(0);
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
        $students = Student::where($data)->select(['name', 'username', 'specialization','departments_id', 'study_group',
            'studying_status', 'student_classification'])
            ->orderBy('name')->orderBy('username')->get()->toArray();
        $headers = [
            [
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'كود الطالب'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'رقم الجلوس'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الاسم'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'السنة الدراسية'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الفرقة'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'التخصص'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الشعبة'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'الحالة الدراسية'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'التصنيف'
                ],
                [
                    'col' => 4,
                    'row' => 1,
                    'text' => 'المصروفات الدراسية'
                ],
                [
                    'col' => 4,
                    'row' => 1,
                    'text' => 'المصروفات الاخرى'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'خصومات المحفظة'
                ],
                [
                    'col' => 1,
                    'row' => 2,
                    'text' => 'باقي المحفظة'
                ],
            ],
            [
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي المستحق'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي المدفوعات'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي الخصومات'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي المتبقي'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي المستحق'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي المدفوعات'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي الخصومات'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'اجمالي المتبقي'
                ],
            ]
        ];
        $export_data = [];

        for ($i = 0; $i < count($students); $i++) {
            if (isset($students[$i]['departments_id'])) {
                $department = DB::table('departments')
                    ->select('id', 'name')
                    ->where('id', '=',$students[$i]['departments_id'])
                    ->pluck('name')[0];
                }
            $export_data[$i][] = $students[$i]['username'];
            $seating_number = $this->getSeatingNumber($students[$i]['username'], $year);
            $export_data[$i][] = (empty($seating_number)) ? '' : $seating_number->seating_number;
            $export_data[$i][] = $students[$i]['name'];
            $export_data[$i][] = $year;
            $export_data[$i][] = $students[$i]['study_group'];
            $export_data[$i][] = $students[$i]['specialization'];
            $export_data[$i][] = $department;
            $export_data[$i][] = $students[$i]['studying_status'];
            $export_data[$i][] = $students[$i]['student_classification'];
            $export_data[$i][] = $this->getTotalStudyPay($students[$i]['username'], $year);
            $export_data[$i][] = $this->getTotalStudyPaid($students[$i]['username'], $year);
            $export_data[$i][] = $this->getTotalStudyDiscount($students[$i]['username'], $year);
            $value1 = floatval($export_data[$i][8]);
            $value2 = floatval($export_data[$i][9]);
            $value3 = floatval($export_data[$i][10]);
            $export_data[$i][] = $value1 - $value2 - $value3;
           // $export_data[$i][] = $export_data[$i][8] - $export_data[$i][9] - $export_data[$i][10];
            $export_data[$i][] = $this->getTotalOtherPay($students[$i]['username'], $year);
            $export_data[$i][] = $this->getTotalOtherPaid($students[$i]['username'], $year);
            $export_data[$i][] = $this->getTotalOtherDiscount($students[$i]['username'], $year);
            $export_data[$i][] = $export_data[$i][12] - $export_data[$i][13] - $export_data[$i][14];
            $export_data[$i][] = $this->getTotalWalletDiscount($students[$i]['username'], $year);
            $wallet = $this->getStudentWallet($students[$i]['username']);
            $export_data[$i][] = (empty($wallet) ? 0 : $wallet->amount);
        }
        try {
            return Excel::download(new ReportsExport([], $headers, $export_data),
                'سجل المالية الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] .''.$department. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function seatingNumberReport(Request $request)
    {
        set_time_limit(0);
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
        $department = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('id')[0];
        $department_name = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('name')[0];
        $type = $data['specialization'] == 'ترميم الاثار و المقتنيات الفنية' ? 'R' : 'T';
        $courses = DB::table('courses')->whereIn('semester', $sem)->where('type', $type)->where('departments_id',$department)
            ->where('is_selected', 1)->get()->toArray();
        $registrations = DB::table('registration')->select(['student_code', 'course_code', 'students.name', 'students.studying_status',
            'students.study_group'])->where('semester', $semester)->where('year', $year)
            ->whereIn('course_code', array_column($courses, 'full_code'))
            ->join('students', 'registration.student_code', '=', 'students.username')
            ->where('students.student_classification','=','مقيد')
            ->orderBy('students.name')->get()->groupBy('student_code')->toArray();
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
                    'text' => 'رقم الجلوس'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الاسم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الفرقة'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'حالة الطالب'
                ],
            ],
        ];
        foreach ($courses as $course) {
            $headers[0][] = [
                'col' => 1,
                'row' => 1,
                'text' => $course->name . ' (' . $course->full_code . ')'
            ];
        }
        $export_data = [];
        $i = 0;
        foreach ($registrations as $registration) {
            $export_data[$i][] = $registration[0]->student_code;
            $seating_number = $this->getSeatingNumber($registration[0]->student_code, $year);
            $export_data[$i][] = (empty($seating_number)) ? '' : $seating_number->seating_number;
            $export_data[$i][] = $registration[0]->name;
            $export_data[$i][] = $registration[0]->study_group;
            $export_data[$i][] = $registration[0]->studying_status;
            for ($j = 5; $j < count($headers[0]); $j++) {
                $flag = false;
                foreach ($registration as $value) {
                    if (str_contains($headers[0][$j]['text'], $value->course_code)) {
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    $export_data[$i][] = '✓';
                } else {
                    $export_data[$i][] = '';
                }
            }
            $i++;
        }
        $export_data = collect($export_data)->sortBy([['1', 'asc'], ['2', 'asc']])->toArray();
        try {
            return Excel::download(new ReportsExport([], $headers, $export_data),
                'كشف ارقام الجلوس الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] .''.$department_name. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function studentWarningReport(Request $request)
    {
        set_time_limit(0);
        $filter_data = $this->getDistinctValues('students', ['study_group', 'specialization','departments_id']);
        $filter_data = array_merge($filter_data,
            $this->getDistinctValues('students_semesters', ['year', 'semester'], false));
        $data = $request->validate([
            'study_group' => 'nullable|in:' . implode(',', $filter_data['study_group']),
            'specialization' => 'nullable|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'nullable|in:' . implode(',', $filter_data['departments_id']),
            'year' => 'required|in:' . implode(',', $filter_data['year']),
            'semester' => 'required|in:' . implode(',', $filter_data['semester']),
        ]);
        $students = DB::table('students_semesters')->select(['students_semesters.student_code', 'name',
            'students_current_warning.warning as current_warning', 'seating_number', 'students.specialization','students.departments_id',
            'students.study_group'])
            ->where('students_semesters.year', $data['year'])
            ->where('students_semesters.warning', 1)
            ->join('registration_years', function ($join) use ($data) {
                $join->on('registration_years.student_code', '=', 'students_semesters.student_code')
                    ->on('registration_years.year', '=', 'students_semesters.year');
                if (!empty($data['study_group'])) {
                    $join->where('registration_years.study_group', $data['study_group']);
                }
            })->join('seating_numbers', function ($join) {
                $join->on('seating_numbers.student_code', '=', 'students_semesters.student_code')
                    ->on('seating_numbers.year', '=', 'students_semesters.year');
            })->join('students', function ($join) use ($data) {
                $join->on('students.username', '=', 'students_semesters.student_code');
                if (!empty($data['specialization'])) {
                    $join->where('students.specialization', $data['specialization'])->where('departments_id', $data['departments_id']);
                }
            })->join('students_current_warning', 'students_current_warning.student_code', '=',
                'students_semesters.student_code')->orderBy('students.specialization')->orderBy('students.departments_id')
            ->orderBy('students.study_group')->get()->transform(function ($value) {
                return (array)$value;
            })->toArray();
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
                    'text' => 'الاسم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'التخصص'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الشعبة'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الفرقة'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'عدد الانذارات الحاليه'
                ],
            ],
        ];
        $department = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('name')[0];
        $export_data = [];
        $i = 0;
        foreach ($students as $student) {
            $export_data[$i][] = $student['seating_number'];
            $export_data[$i][] = $student['student_code'];
            $export_data[$i][] = $student['name'];
            $export_data[$i][] = $student['specialization'];
            $export_data[$i][] = $department;
            $export_data[$i][] = $student['study_group'];
            $export_data[$i][] = $student['current_warning'];
            $i++;
        }
        try {
            return Excel::download(new ReportsExport([], $headers, $export_data),
                'كشف الطلاب الحاصلين على انذارات ' . $data['study_group'] . ' ' . $data['specialization']
                . ' عام ' . str_replace('/', '-', $data['year']) . ' ' . $data['semester'] .''.$department. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function payingStudentsSubjectReport(Request $request)
    {
        set_time_limit(0);
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
        $students = Student::where($data)
            ->join('registration_semester', 'students.username', '=', 'registration_semester.student_code')
            ->where(['payment' => 1, 'registration_semester.year' => $year,
                'registration_semester.semester' => $semester])

            ->join('payment_tickets', function ($join) {
                $join->on('payment_tickets.student_code', '=', 'students.username')
                    ->on('payment_tickets.year', 'registration_semester.year')
                    ->on('payment_tickets.semester', 'registration_semester.semester')
                    ->where('type', 'محفظة');
            })->orderBy('confirmed_at')->get()->toArray();
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
        $department = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('id')[0];
        $department_name = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('name')[0];
        $type = $data['specialization'] == 'ترميم الاثار و المقتنيات الفنية' ? 'R' : 'T';
        $courses = DB::table('courses')->whereIn('semester', $sem)->where('type', $type)->where('departments_id',$department)
            ->where('is_selected', 1)->get()->toArray();
        $headers  = [
            [
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'رقم مسلسل'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'كود الطالب'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الاسم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'سكشن رقم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الحالة الدراسية'
                ],
            ],
        ];
        foreach ($courses as $course) {
            $headers[0][] = [
                'col' => 1,
                'row' => 1,
                'text' => $course->name . ' (' . $course->full_code . ')'
            ];
        }
        if ($data['study_group'] != 'الاولي') {
            $headers[0][] = [
                'col' => 1,
                'row' => 1,
                'text' => 'اسم مادة التخلف 1'
            ];
            $headers[0][] = [
                'col' => 1,
                'row' => 1,
                'text' => 'اسم مادة التخلف 2'
            ];
        }
        $export_data = [];
        $i = 0;
        foreach ($students as $student) {
            $export_data[$i][] = $i + 1;
            $export_data[$i][] = $student['username'];
            $export_data[$i][] = $student['name'];
            $section = $this->getStudentSectionNumber($student['username'], $year, $semester);
            $export_data[$i][] = (is_null($section)) ? '' : $section->section_number;
            $export_data[$i][] = $student['studying_status'];
            $student_courses = collect($this->getRegisteredCourses($student['username'], $year, $semester))
                ->map(function ($value) {
                    $value['used'] = false;
                    return $value;
                })->toArray();
            for ($j = 5; $j < (5 + count($courses)); $j++) {
                $flag = false;
                foreach ($student_courses as &$value) {
                    if (str_contains($headers[0][$j]['text'], $value['full_code'])) {
                        $value['used'] = $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    $export_data[$i][] = '✓';
                } else {
                    $export_data[$i][] = '';
                }
            }
            $remaining_courses = collect($student_courses)->where('used', false)->values()->toArray();
            $export_data[$i][] = isset($remaining_courses[0]) ? $remaining_courses[0]['name'] . ' (' .
                $remaining_courses[0]['full_code'] . ')' : '';
            $export_data[$i][] = isset($remaining_courses[1]) ? $remaining_courses[1]['name'] . ' (' .
                $remaining_courses[1]['full_code'] . ')' : '';
            $i++;
        }
        try {
            return Excel::download(new ReportsExport([], $headers, $export_data),
                'كشف الطلاب المسددين الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] .''.$department_name. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function registeredStudentsSubjectReport(Request $request)
    {
        set_time_limit(0);
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
        $students = Student::where($data)
            ->join('registration_semester', 'students.username', '=', 'registration_semester.student_code')
            ->where(['registration_semester.year' => $year, 'registration_semester.semester' => $semester])
            ->orderBy('name')->get()->toArray();
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
        $department = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('id')[0];
        $department_name = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('name')[0];
        $type = $data['specialization'] == 'ترميم الاثار و المقتنيات الفنية' ? 'R' : 'T';
        $courses = DB::table('courses')->whereIn('semester', $sem)->where('type', $type)->where('departments_id', $department)
            ->where('is_selected', 1)->get()->toArray();
        $headers = [
            [
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'رقم مسلسل'
                ],
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
                    'text' => 'الاسم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'سكشن رقم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الحالة الدراسية'
                ],
            ],
        ];
        foreach ($courses as $course) {
            $headers[0][] = [
                'col' => 1,
                'row' => 1,
                'text' => $course->name . ' (' . $course->full_code . ')'
            ];
        }
        if ($data['study_group'] != 'الاولي') {
            $headers[0][] = [
                'col' => 1,
                'row' => 1,
                'text' => 'اسم مادة التخلف 1'
            ];
            $headers[0][] = [
                'col' => 1,
                'row' => 1,
                'text' => 'اسم مادة التخلف 2'
            ];
        }
        $export_data = [];
        $i = 0;
        foreach ($students as $student) {
            $export_data[$i][] = $i + 1;
            $seating_number = $this->getSeatingNumber($student['username'], $year);
            $export_data[$i][] = (empty($seating_number)) ? '' : $seating_number->seating_number;
            $export_data[$i][] = $student['username'];
            $export_data[$i][] = $student['name'];
            $section = $this->getStudentSectionNumber($student['username'], $year, $semester);
            $export_data[$i][] = (is_null($section)) ? '' : $section->section_number;
            $export_data[$i][] = $student['studying_status'];
            $student_courses = collect($this->getRegisteredCourses($student['username'], $year, $semester))
                ->map(function ($value) {
                    $value['used'] = false;
                    return $value;
                })->toArray();
            for ($j = 6; $j < (6 + count($courses)); $j++) {
                $flag = false;
                foreach ($student_courses as &$value) {
                    if (str_contains($headers[0][$j]['text'], $value['full_code'])) {
                        $value['used'] = $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    $export_data[$i][] = '✓';
                } else {
                    $export_data[$i][] = '';
                }
            }
            $remaining_courses = collect($student_courses)->where('used', false)->values()->toArray();
            $export_data[$i][] = isset($remaining_courses[0]) ? $remaining_courses[0]['name'] . ' (' .
                $remaining_courses[0]['full_code'] . ')' : '';
            $export_data[$i][] = isset($remaining_courses[1]) ? $remaining_courses[1]['name'] . ' (' .
                $remaining_courses[1]['full_code'] . ')' : '';
            $i++;
        }
        try {
            return Excel::download(new ReportsExport([], $headers, $export_data),
                'كشف الطلاب المسجلين الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] .''.$department_name. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

    public function unregisteredStudentsSubjectReport(Request $request)
    {
        set_time_limit(0);
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
        $students = Student::where($data)
            ->leftJoin('registration_semester', function ($join) use ($year, $semester) {
                $join->on('students.username', '=', 'registration_semester.student_code')
                    ->where(compact('year', 'semester'));
            })
            ->where(['registration_semester.year' => null, 'registration_semester.semester' => null])
            ->orderBy('name')->get()->toArray();
            $department = DB::table('departments')->where('id', '=', $data['departments_id'])->pluck('name')[0];
        $headers = [
            [
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'رقم مسلسل'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'كود الطالب'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الاسم'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'الحالة الدراسية'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'التصنيف'
                ],
                 [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'رقم الموبايل'
                ],
                [
                    'col' => 1,
                    'row' => 1,
                    'text' => 'العنوان'
                ],
            ],
        ];
        $export_data = [];
        $i = 0;
        foreach ($students as $student) {
            $export_data[$i][] = $i + 1;
            $export_data[$i][] = $student['username'];
            $export_data[$i][] = $student['name'];
            $export_data[$i][] = $student['studying_status'];
            $export_data[$i][] = $student['student_classification'];
            $export_data[$i][] = $student['mobile'];
            $export_data[$i][] = $student['address'];
            $i++;
        }
        try {
            return Excel::download(new ReportsExport([], $headers, $export_data),
                'كشف الطلاب الغير مسجلين الفرقة ' . $data['study_group'] . ' ' . $data['specialization'] .''.$department. '.xlsx');
        } catch (Exception $e) {
            return redirect()->back()->withErrors('خطأ في الإتصال');
        }
    }

         public function getRegistration()
        {
            $semester = $this->getCurrentSemester();
            $year = $this->getCurrentYear();
            if ($semester == '') {
                return redirect()->back()->withErrors(['year' => 'لم يتم تفعيل اي ترم بعد']);
            }
            $filter_data_course = $this->getDistinctValues('registration', ['course_code', 'year']);
            return view('reports.reports' , compact('filter_data_course'));
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
