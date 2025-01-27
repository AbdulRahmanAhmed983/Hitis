<?php

namespace App\Http\Controllers;

use App\Http\Traits\StudentTrait;
use App\Models\Student;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Money2String\Arabic;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;
use Illuminate\Support\Facades\Redirect;
class FinanceController extends Controller
{
    use StudentTrait;

    public function paymentList(Request $request)
    {
        $filter_data = $this->getDistinctValues('students_payments', ['year', 'semester']);
        $other = $this->getDistinctValues('students_other_payments', ['year', 'semester']);
        $filter_data['year'] = array_unique(array_merge($filter_data['year'], $other['year']));
        $filter_data['semester'] = array_unique(array_merge($filter_data['semester'], $other['semester']));
        $filter_data += $this->getDistinctValues('students', ['study_group', 'student_classification', 'specialization','departments_id']);
        $departments = DB::table('departments')->select('id','name')->get();
        $filter_data['grade'] = ['ممتاز', 'جيد جدا', 'جيد', 'مقبول', 'ضعيف'];
        $filter_data['remaining_study'] = ['مسدد', 'غير مسدد'];
        $filter_data['remaining_other'] = ['مسدد', 'غير مسدد'];
        $filter_data['per_page'] = [25, 50, 100, 500, 1000, 'all'];
        $items_per_pages = empty($request->validate([
            'per_page' => 'nullable|in:' . implode(',', $filter_data['per_page']),
        ])) ? 50 : ($request->per_page == 'all' ? 99999999999 : $request->per_page);
        $data = array_filter($request->validate([
            'specialization' => 'nullable|in:' . implode(',', $filter_data['specialization']),
            'departments_id' => 'nullable|in:' . implode(',', $filter_data['departments_id']),
            'study_group' => 'nullable|in:' . implode(',', $filter_data['study_group']),
            'student_classification' => 'nullable|in:' . implode(',', $filter_data['student_classification']),
            'remaining_study' => 'nullable|in:' . implode(',', $filter_data['remaining_study']),
            'remaining_other' => 'nullable|in:' . implode(',', $filter_data['remaining_other']),
            'grade' => 'nullable|in:' . implode(',', $filter_data['grade']),
            'year' => 'nullable|in:' . implode(',', $filter_data['year']),
            'semester' => 'nullable|in:' . implode(',', $filter_data['semester']),
        ]), function ($value) {
            return ($value !== null && $value !== '');
        });
        $students_s = DB::table('students_payments')
            ->selectRaw('`student_code`,`year`,`semester`,`hours`, `ministerial_payment`,
            `payment` AS `study_payment`,`paid_payments` AS `study_paid`,0 AS `study_discount`,0 AS `other_payment`,
            0 AS `other_paid`,0 AS `other_discount`');
        $students_o = DB::table('students_other_payments')
            ->selectRaw('`student_code`,`year`,`semester`,0 AS `hours`,0 AS `ministerial_payment`,
            0 AS `study_payment`,0 AS `study_paid`,0 AS `study_discount`,`payment` AS `other_payment`,
            `paid_payments` AS `other_paid`,0 AS `other_discount`');
        $students_d = DB::table('students_discounts')
            ->selectRaw('`student_code`,`year`,`semester`,0 AS `hours`,0 AS `ministerial_payment`,
            0 AS `study_payment`,0 AS `study_paid`,
            IF(`type` = \'دراسية\',SUM(`amount`),0) AS `study_discount`,
            0 AS `other_payment`,0 AS `other_paid`,
            IF(`type` = \'اخرى\',SUM(`amount`),0) AS `other_discount`')
            ->groupBy('student_code', 'year', 'semester', 'type');
            $students_admin_expenses = DB::table('payments_administrative_expenses')
            ->selectRaw('`student_code`, `year`, `amount` AS `administrative_expenses`');
            $students_s->union($students_d)->union($students_o)->union($students_admin_expenses);
        $payments = DB::table('students')->joinSub($students_s, 'payments', function ($join) {
            $join->on('students.username', '=', 'payments.student_code');
        })->selectRaw('
            `students`.`name`,
            `student_code`,
            `students`.`student_classification`,
            `students`.`study_group`,
            `students`.`specialization`,
            `students`.`departments_id`,
            `students`.`studying_status`,
            `year`,
            `semester`,
            SUM(`hours`) AS `hours`,
            SUM(`ministerial_payment`) AS `ministerial_payment`,
            SUM(`s  tudy_payment`) AS `study_payment`,
            SUM(`study_paid`) AS `study_paid`,
            SUM(`study_discount`) AS `study_discount`,
            (SUM(`study_payment`) - SUM(`study_paid`) - SUM(`study_discount`)) AS `remaining_study`,
            SUM(`other_payment`) AS `other_payment`,
            SUM(`other_paid`) AS `other_paid`,
            SUM(`other_discount`) AS `other_discount`,
             (SUM(`other_payment`) - SUM(`other_paid`) - SUM(`other_discount`)) AS `remaining_other`,
         SUM(`administrative_expenses`) AS `administrative_expenses`')
            ->groupBy('student_code');
        if (!empty($data) or !is_null($request->search)) {
            $request->validate([
                'search' => 'nullable|string|not_regex:/[#;<>]/u',
            ]);
            $g_flag = false;
            if (isset($data['grade'])) {
                $data += $this->gradeToCgpa($data['grade']);
                $g_flag = $data['grade'];
                unset($data['grade']);
            }
            $s_flag = false;
            if (isset($data['remaining_study'])) {
                $s_flag = $data['remaining_study'];
                unset($data['remaining_study']);
            }
            $o_flag = false;
            if (isset($data['remaining_other'])) {
                $o_flag = $data['remaining_other'];
                unset($data['remaining_other']);
            }
            $payments->where($data)->whereRaw('CONCAT(`name`,"\0",`student_code`) LIKE ?',
                ['%' . $request->search . '%']);
            if ($s_flag) {
                if ($s_flag == 'غير مسدد') {
                    $payments->having('remaining_study', '>', '0')
                        ->having('study_payment', '>', 0);
                } else {
                    $payments->having('remaining_study', '=', '0')
                        ->having('study_payment', '>', 0);
                }
                $data['remaining_study'] = $s_flag;
            }
            if ($o_flag) {
                if ($o_flag == 'غير مسدد') {
                    $payments->having('remaining_other', '>', '0')
                        ->having('other_payment', '>', 0);
                } else {
                    $payments->having('remaining_other', '=', '0')
                        ->having('other_payment', '>', 0);
                }
                $data['remaining_other'] = $o_flag;
            }
            $students = $payments->paginate($items_per_pages);
            if ($g_flag) {
                $data['grade'] = $g_flag;
            }
            if ($s_flag) {
                $data['remaining_study'] = $s_flag;
            }
            if ($o_flag) {
                $data['remaining_other'] = $o_flag;
            }
            $students->appends(['search' => $request->search]);
            $students->appends($data);
        } else {
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $data = ['year' => $year, 'semester' => $semester];
            $students = $payments->where($data)->paginate($items_per_pages);
        }
        if ($items_per_pages == 99999999999) {
            $items_per_pages = 'all';
        }
        $request->validate([
            'page' => 'nullable|integer|between:1,' . $students->lastPage(),
        ]);
        $students->appends(['per_page' => $items_per_pages])->getCollection()->transform(function ($value) {
            $value->departments_id  = DB::table('departments')->select('id','name')->where('id', '=', $value->departments_id)->pluck('name')[0];
            $wallet = $this->getStudentWallet($value->student_code);
            $value->wallet = (empty($wallet)) ? 0 : $wallet->amount;
            return $value;
        });
        $keys = ['الاسم', 'code', 'تصنيف الطلاب', 'الفرقة الدراسية', 'التخصص', 'الشعبة','الحالة الدراسية', 'الساعات المسجلة',
            'المصاريف الوزارية', 'اجمالى المصاريف الدراسية', 'المدفوع', 'اجمالى الخصومات الدراسية',
            'المصاريف الادارية',
            'باقي المصاريف الدراسية', 'اجمالى المصاريف الاخرى', 'المدفوع', 'اجمالى الخصومات الاخرى',
            'باقي المصاريف الاخرى', 'مبلغ المحفظة الحالى'];
        return view('finance.show_finance')->with([
            'students' => $students,
            'keys' => $keys,
            'primaryKey' => 'student_code',
            'removed_keys' => ['year', 'semester'],
            'hidden_keys' => [],
            'search' => $request->search,
            'status' => [],
            'filter_data' => $filter_data,
            'items_per_pages' => $items_per_pages,
            'filter' => $data,
            'departments' => $departments,

        ]);
    }

    public function payTicketIndex()
    {
        $ministerial_receipt = DB::table('payment_tickets')->max('ministerial_receipt') + 1;
        $ministerial_receipt_e = $this->getData(['ministerial_receipt_end'])['ministerial_receipt_end'][0];
        $ministerial_receipt_s = $this->getData(['ministerial_receipt_start'])['ministerial_receipt_start'][0];
        $ministerial_receipt = ($ministerial_receipt > $ministerial_receipt_e) ? $ministerial_receipt_s :
            $ministerial_receipt;
        $daily_payments_datetime = DB::table('daily_payments_datetime')->where('end_date',NULL)->pluck('date')[0];

        return view('finance.pay_ticket', compact('ministerial_receipt','daily_payments_datetime'));
    }

    public function payTicket(Request $request)
    {
        if (!$this->canPay()) {
            return redirect()->back()->with('error', 'الخزينة مغلقة لا يمكن السداد الان')->withInput();
        }
        $type = $request->validate(['type' => 'required|in:دراسية,اخرى,محفظة'])['type'];
        $ministerial_receipt_s = $this->getData(['ministerial_receipt_start'])['ministerial_receipt_start'][0];
        $ministerial_receipt_e = $this->getData(['ministerial_receipt_end'])['ministerial_receipt_end'][0];
        $ministerial_receipt_n = DB::table('payment_tickets')->max('ministerial_receipt') + 1;
        $ministerial_receipt_n = ($ministerial_receipt_n > $ministerial_receipt_e) ? $ministerial_receipt_s :
            $ministerial_receipt_n;
        $rule = [
            'ticket_id' => ['required', 'string', 'size:19', 'exists:payment_tickets,ticket_id'
                , function ($attribute, $value, $fail) use ($type) {
                    if (!$this->ticketExists(substr($value, 12, 7), 0, $type, $value))
                        $fail('الحافظه غير صحيحه او تم ادخالها من قبل.');
                }
            ],
            'ministerial_receipt' => 'required|integer|digits_between:6,10|between:' . $ministerial_receipt_s . ','
                . $ministerial_receipt_e . '|unique:payment_tickets,ministerial_receipt|in:' . $ministerial_receipt_n,
            'payment_type' => 'required|in:كاش,كريدت',
            'visa_number' => ($request->payment_type == 'كريدت') ? 'required|digits:4' : 'nullable|regex:/^$/i',
        ];
        $data = $request->validate($rule);
        $username = substr($data['ticket_id'], 12, 7);
        $ticket = DB::table('payment_tickets')->where('ticket_id', $data['ticket_id']);
        if ($ticket->first()->amount < 0) {
            return redirect()->back()->with('error', 'قيمة الحافظه غير صحيحه.')->withInput();
        }
        $student = $this->getStudentInfo($username);
        if ($ticket->first()->amount == 0) {
            $data['ministerial_receipt'] = null;
        }
        if ($type == 'محفظة') {
            try {
                DB::transaction(function () use ($data, &$student, $username, $ticket) {
                    $ticket->update([
                        'ministerial_receipt' => $data['ministerial_receipt'],
                        'used' => 1,
                        'payment_type' => $data['payment_type'],
                        'visa_number' => (empty($data['visa_number']) ? null : $data['visa_number']),
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => Carbon::now(),
                    ]);
                    $wallet = DB::table('students_wallet')->where('student_code', $username);
                    if ($wallet->exists()) {
                        $wallet->increment('amount', $ticket->first()->amount);
                    } else {
                        $wallet->insert(['student_code' => $username, 'amount' => $ticket->first()->amount]);
                    }
                    DB::table('students_wallet_transaction')->insert([
                        'student_code' => $username,
                        'year' => $ticket->first()->year,
                        'semester' => $ticket->first()->semester,
                        'amount' => $ticket->first()->amount,
                        'date' => Carbon::now(),
                        'type' => 'ايداع',
                        'reason' => 'ايداع مبلغ مالي',
                    ]);
                    $ar = new Arabic();
                    $student['amount'] = $ar->money2str($ticket->first()->amount, 'EGP');
                    $student['semester'] = $ticket->first()->year . '-' . $ticket->first()->semester;
                    $student['payment_type'] = $data['payment_type'];
                    $student['visa_number'] = (empty($data['visa_number']) ? null : $data['visa_number']);
                    $student['ticket_id'] = $data['ticket_id'];
                    $student['ministerial_receipt'] = $data['ministerial_receipt'];
                    $student['date'] = date('d/m/Y');
                });
                $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
                $url = route('pay.ticket');
                session()->flash('success', 'تم تأكيد الدفع بنجاح');
                return view('finance.print_receipt', compact('student', 'url'));
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
            }
        } else {
            $year = $ticket->first()->year;
            $semester = $ticket->first()->semester;
            if ($type == 'دراسية') {
                $payment = DB::table('students_payments')->where('student_code', $username)
                    ->where('year', $year)->where('semester', $semester);
                // $payment = $this->getLastPayment($username);
                $discount = $this->getTotalStudyDiscount($username, $year, $semester);
            } else {
                // $payment = DB::table('students_other_payments')->where('student_code', $username)
                //     ->where('year', $year)->where('semester', $semester);
                $discount = $this->getTotalOtherDiscount($username, $year, $semester);
                $payment = $this->getLastOtherPayment($username);
                if (is_null($payment)) {
                    $payment = DB::table('students_other_payments')
                        ->where('student_code', $ticket->first()->student_code)
                        ->where('year', $ticket->first()->year)
                        ->where('semester', $ticket->first()->semester)
                        ->orderBy('id', 'desc')->first();
                }
                $payment = DB::table('students_other_payments')->where('id', $payment->id);
            }
             $paid_payments = $payment->first()->paid_payments + $ticket->first()->amount + $discount;
           // $paid_payments = $payment->first()->paid_payments  + $discount;
            if ($ticket->first()->amount > $payment->first()->payment) {
                return redirect()->back()->with('error', 'قيمة الحافظه اكثر من المطلوب برجاء التوجه لشئون الطلاب.')
                    ->withInput();
            }
            if ($ticket->first()->amount < $payment->first()->payment) {
                return redirect()->back()->with('error', 'قيمة الحافظه اقل من المطلوب برجاء التوجه لشئون الطلاب.')
                    ->withInput();
            }
            $student = $this->getStudentInfo($username);
            try {
                DB::transaction(function () use (
                    $type, $data, $paid_payments, $discount, $username, $semester, $year, $ticket,
                    $payment, &$student
                ) {
                    $ar = new Arabic();
                    $student['amount'] = $ar->money2str($ticket->first()->amount, 'EGP') ?: '0';
                    $student['semester'] = $ticket->first()->year . '-' . $ticket->first()->semester;
                    $ticket->update([
                        'ministerial_receipt' => $data['ministerial_receipt'],
                        'used' => 1,
                        'payment_type' => $data['payment_type'],
                        'visa_number' => (empty($data['visa_number']) ? null : $data['visa_number']),
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => Carbon::now(),
                    ]);
                    if ($paid_payments == $payment->first()->payment and $type == 'دراسية') {
                        $this->confirmStudentSemester($username, $year, $semester);
                    }
                    $payment->update([
                        'paid_payments' => $paid_payments - $discount,
                    ]);
                    $student['payment_type'] = $data['payment_type'];
                    $student['visa_number'] = (empty($data['visa_number']) ? null : $data['visa_number']);
                    $student['ticket_id'] = $data['ticket_id'];
                    $student['ministerial_receipt'] = $data['ministerial_receipt'];
                    $student['date'] = date('d/m/Y');
                });
                $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
                $url = route('pay.ticket');
                session()->flash('success', 'تم تأكيد الدفع بنجاح');
                return view('finance.print_receipt', compact('student', 'url'));
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
            }
        }
    }

    public function setDailyPaymentDateTime(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'btn' => 'required|in:open,close'
        ]);
        if ($data['btn'] == 'open') {
            $request->validate([
                'date' => 'unique:daily_payments_datetime,date'
            ]);
            if (DB::table('daily_payments_datetime')->whereNull('end_date')->exists()) {
                return redirect()->back()->withErrors(['date' => 'لا يمكن فتح ماليه هنالك مالية مفتوحة من قبل']);
            }
            DB::table('daily_payments_datetime')->insert([
                'date' => $data['date'],
                'start_date' => Carbon::now()
            ]);
            return redirect()->route('daily.payments')->with('success', 'تم فتح مالية يوم ' . $data['date']);
        } else {
            $last_date = DB::table('daily_payments_datetime')->max('start_date');
            $date = DB::table('daily_payments_datetime')->whereNull('end_date')
                ->where('start_date', $last_date)->where('date', $data['date']);
            if (!$date->exists()) {
                return redirect()->back()->withErrors(['date' => 'لا توجد اى يوميات مفتوحة']);
            }
            $date->update(['end_date' => Carbon::now()]);
            return redirect()->route('daily.payments')->with('success', 'تم غلق مالية يوم ' . $data['date']);
        }
    }

     public function dailyPaymentIndex(Request $request)
    {
        $days = DB::table('daily_payments_datetime')->distinct()->selectRaw('DATE(date) as days')
            ->orderBy('days', 'DESC')->pluck('days')->toArray();
        $rule = [
            'day' => 'nullable|in:' . implode(',', $days),
        ];
        $last_date = DB::table('daily_payments_datetime')->whereNull('end_date');
        if ($last_date->exists()) {
            $last_date = $last_date->first()->date;
        } else {
            $last_date = null;
        }
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return redirect()->route('daily.payments')->withErrors($validator->errors());
        } elseif (!is_null($request->day)) {
            $d = $request->day;
            $day = DB::table('daily_payments_datetime')->where('date', $d)
                ->orderByDesc('id')->get()->first();
            $tickets = $this->getTickets($day->start_date, $day->end_date);
            $ticketsAdministrative = $this->getTicketsAdministrative($day->start_date, $day->end_date);
            $tickets->map(function ($value) {
                $value->name = $this->getStudentInfo($value->student_code)['name'] ?? 'Unknown';
                return $value;
            });

            $ticketsAdministrative->map(function ($value) {
                $value->name = $this->getStudentInfo($value->student_code)['name'] ?? 'Unknown';
                return $value;
            });
            $cash = $credit = $study = $other = $wallet = $count = $discount_wallet_administrative =
            $insurance = $profile_expenses = $registration_fees = $renew_card_and_email = $card_and_email =
            $credit_administrative_military =  $cash_administrative_military =
            $military_expenses  = $cash_administrative = $credit_administrative =
            $discount_wallet_administrative_insurance = $discount_wallet_administrative_profile_expenses = $discount_wallet_administrative_registration_fees=
            $discount_wallet_administrative_card_and_email = $discount_wallet_administrative_renew_card_and_email = '';

            extract($this->getTicketsInfo($tickets));
            extract($this->getTicketsAdministrativeInfo($ticketsAdministrative));
            $tickets = $tickets->toArray();
            $ticketsAdministrative = $ticketsAdministrative->toArray();

            return view('finance.daily_payments', compact('tickets', 'days', 'd', 'cash',
                'credit', 'study', 'other', 'last_date', 'wallet','ticketsAdministrative',
                'cash_administrative','credit_administrative','insurance', 'profile_expenses',
                'registration_fees', 'card_and_email','renew_card_and_email','military_expenses',
            'credit_administrative_military','cash_administrative_military','discount_wallet_administrative',
            'discount_wallet_administrative_insurance','discount_wallet_administrative_profile_expenses','discount_wallet_administrative_registration_fees',
            'discount_wallet_administrative_card_and_email','discount_wallet_administrative_renew_card_and_email'));
        }
        return view('finance.daily_payments', compact('days', 'last_date'));
    }
    public function showTickets($student_code)
    {
        $rule = [
            'username' => 'required|exists:students,username',
        ];
        $validator = Validator::make(['username' => $student_code], $rule);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        $tickets = DB::table('payment_tickets')->where('student_code', $student_code)
            ->orderBy('date')->get()->all();
        $student = $this->getStudentInfo($student_code);
        return view('finance.show_tickets', compact('tickets', 'student'));
    }

    public function printReceipt($username, $ticket_id)
    {
        $rule = [
            'username' => 'required|exists:students,username|exists:payment_tickets,student_code',
            'ticket_id' => 'required|exists:payment_tickets,ticket_id'
        ];
        $validator = Validator::make(['username' => $username, 'ticket_id' => $ticket_id], $rule);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        if (!$this->ticketExists($username, 1, null, $ticket_id)) {
            return redirect()->back()->withErrors(['ticket_id' => 'الحافظة غير مسدده']);
        }
        $student = $this->getStudentInfo($username);
        $ticket = DB::table('payment_tickets')->where(['ticket_id' => $ticket_id, 'student_code' => $username])
            ->get()->first();
        $ar = new Arabic();
        $student['amount'] = $ar->money2str($ticket->amount, 'EGP') ?: '0';
        $student['semester'] = $ticket->year . '-' . $ticket->semester;
        $student['ticket_id'] = $ticket_id;
        $student['payment_type'] = $ticket->payment_type;
        $student['visa_number'] = $ticket->visa_number;
        $student['ministerial_receipt'] = $ticket->ministerial_receipt;
        $student['date'] = $ticket->date;
        $student['semester'] = str_replace('ترم صيفي', '(3)', $student['semester']);
        $url = route('registrations.payments');
        return view('finance.print_receipt', compact('student', 'url'));
    }

    public function studentStatus(Request $request)
    {
        $rules = [
            'username' => 'nullable|string|min:7|max:7|regex:/^[RT][0-9]{6}$/u|exists:users,username|exists:students,username',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('finance.student.status')->withErrors($validator)->withInput();
        } else {
            if (!is_null($request->username)) {
                [$payment, $semester_payment, $other_payment, $total_other_payment, $discount, $total_discount,
                    $year_semester] = $this->getStudentFinanceStatus($request->username);
                $student[] = $this->getStudentInfo($request->username)['name'];
                $student[] = $request->username;
                return view('finance.student_status', compact('payment', 'semester_payment',
                    'other_payment', 'total_other_payment', 'discount', 'total_discount', 'year_semester', 'student'));
            }
            return view('finance.student_status');
        }
    }

    public function studentAlertIndex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'nullable|regex:/^[RT][0-9]{6}$/u|exists:students,username'
        ]);
        if ($validator->fails()) {
            return redirect()->route('f.student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        if (!empty($data)) {
            $alerts = $this->getAlerts($data['username'], 'شئون المالية');
            return view('finance.student_alerts', compact('alerts'));
        }
        return view('finance.student_alerts');
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
            return redirect()->route('f.student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        try {
            $codes = explode(',', $data['usernames']);
            DB::transaction(function () use ($data, $codes) {
                foreach ($codes as $code) {
                    DB::table('students_alerts')->insert([
                        'student_code' => $code,
                        'category' => 'شئون المالية',
                        'reason' => $data['reason'],
                        'status' => $data['status'],
                        'created_by' => auth()->id(),
                    ]);
                }
            });
            return redirect()->route('f.student.alerts')->with('success', 'تم اضافة التنبيه بنجاح');
        } catch (Exception $ex) {
            return redirect()->route('f.student.alerts')->with('error', 'خطأ في الإتصال')->withInput();
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
            return redirect()->route('f.student.alerts')->with('error', "البيانات غير صحيحة");
        }
        $validator = Validator::make($request->all(), [
            'alert' => 'required|array|min:1',
            'alert.*' => ['required', 'in:1',
                function ($attribute, $value, $fail) use ($student_code) {
                    if (!DB::table('students_alerts')->where('id', explode('.', $attribute)[1])
                        ->where('student_code', $student_code)->where('category', 'شئون المالية')
                        ->exists())
                        $fail('البيانات غير صحيحة');
                }
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->route('f.student.alerts')->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        try {
            $num = 0;
            DB::transaction(function () use ($data, $student_code, &$num) {
                $num = DB::table('students_alerts')->where('student_code', $student_code)
                    ->where('category', 'شئون المالية')->whereIn('id', array_keys($data['alert']))
                    ->delete();
            });
            if ($num > 1)
                return redirect()->route('f.student.alerts')->with('success', 'تم حذف التنبيهات بنجاح');
            else
                return redirect()->route('f.student.alerts')->with('success', 'تم حذف التنبيه بنجاح');
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
        }
    }

    public function reportIndex()
    {
        return view('finance.reports');
    }

  public function dailyPaymentsReport($type, Request $request)
    {
        $validator = Validator::make(['type' => $type], [
            'type' => 'required|string|in:interval,daily',
        ]);
        if ($validator->fails()) {
            return redirect()->route('finance.reports')->withErrors($validator->errors())->withInput();
        }
        if ($type == 'daily') {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date_format:Y-m-d|before_or_equal:end_date',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date_format:Y-m-d\TH:i|before_or_equal:end_date',
                'end_date' => 'required|date_format:Y-m-d\TH:i|after_or_equal:start_date',
            ]);
        }
        if ($validator->fails()) {
            return redirect()->route('finance.reports')->withErrors($validator->errors())->withInput();
        }
        $data = $validator->validated();
        $validator = Validator::make($request->all(), [
            'action' => 'required|string|in:total,excel,excel_administrative,excel_extra_fees,days',
        ]);
        if ($validator->fails()) {
            return redirect()->route('finance.reports')->withErrors($validator->errors())->withInput();
        }
        $action = $validator->validated()['action'];
        if ($type == 'daily') {
            $dates = DB::table('daily_payments_datetime')->whereBetween('date', $data)
                ->orderBy('date')->get()->toArray();
            if (empty($dates)) {
                return redirect()->back()->withErrors('لا توجد بيانات فى هذا التاريخ')->withInput();
            }
            $start_date = $dates[0];
            $end_date = end($dates);
            $tickets = $this->getTickets($start_date->start_date, $end_date->end_date);
            $tickets_administrative = $this->getTicketsAdministrative($start_date->start_date, $end_date->end_date);
            $tickets_extra_fees = $this->getُExtaFeesPayments($start_date->start_date, $end_date->end_date);

        } else {
            $tickets = $this->getTickets($data['start_date'], $data['end_date']);
            $tickets_administrative = $this->getTicketsAdministrative($data['start_date'], $data['end_date']);
            $tickets_extra_fees = $this->getُExtaFeesPayments($data['start_date'], $data['end_date']);
        }
        if ($action == 'excel') {
            $tickets = $tickets->toArray();
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'اسم الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'الفرقة الدراسية'
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
                        'text' => 'الحالة الداراسية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'السنة الدارسية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'الترم الدراسي'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'قيمة السداد'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم الايصال'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم الحافظة'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'نوع المصاريف'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'طريقة الدفع'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم الفيزا'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'تاريخ السداد'
                    ],
                ],
            ];
            $export_data = [];
            for ($i = 0; $i < count($tickets); $i++) {
                $student = $this->getStudentInfo($tickets[$i]->student_code);
                if (isset($student['departments_id'])) {
                    $department = DB::table('departments')
                        ->select('id', 'name')
                        ->where('id', '=', $student['departments_id'])
                        ->pluck('name')[0];
                }
               if (isset($student['name']) || isset($student['username']) || isset($student['military_number']) || isset($student['national_id']) || isset($student['study_group']) ) {
                $export_data[$i][] = $student['name'];
                $export_data[$i][] = $student['username'];
                $export_data[$i][] = $student['study_group'];
                $export_data[$i][] = $student['specialization'];
                $export_data[$i][] = $department;
                $export_data[$i][] = $student['studying_status'];
                $export_data[$i][] = $tickets[$i]->year;
                $export_data[$i][] = $tickets[$i]->semester;
                $export_data[$i][] = $tickets[$i]->amount;
                $export_data[$i][] = $tickets[$i]->ministerial_receipt;
                $export_data[$i][] = $tickets[$i]->ticket_id;
                $export_data[$i][] = $tickets[$i]->type;
                $export_data[$i][] = $tickets[$i]->payment_type;
                $export_data[$i][] = $tickets[$i]->visa_number;
                $export_data[$i][] = $tickets[$i]->confirmed_at;
                }else {
                    $export_data[$i][] = 'Unknown Name';
                    $export_data[$i][] = 'Unknown code';
                    $export_data[$i][] = 'Unknown study_group';
                }
            }
            try {
                return Excel::download(new ReportsExport([], $headers, $export_data),
                    'سجل المالية من ' . $data['start_date'] . ' الى ' . $data['end_date'] . '.xlsx');
            } catch (Exception $e) {
                return redirect()->back()->withErrors('خطأ في الإتصال')->withInput();
            }
        }
        elseif($action == 'excel_administrative'){
            $tickets_administrative = $tickets_administrative->toArray();
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'اسم الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود الطالب'
                    ],
                     [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم بطاقة الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم تجنيد الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'الفرقة الدراسية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'السنة الدارسية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم االحافظة'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'التأمين'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'سحب الملف'
                    ],

                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رسوم قيد'
                    ],

                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'الكارنية والايميل'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'تجديد الكارنية والايميل'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'العسكرية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'المبلغ الاجمالي'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'طريقة الدفع'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم الفيزا'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'تاريخ السداد'
                    ],
                ],
            ];
            $export_data = [];
            for ($i = 0; $i < count($tickets_administrative); $i++) {
                $student = $this->getStudentInfo($tickets_administrative[$i]->student_code);
                if (isset($student['name']) || isset($student['username']) || isset($student['military_number']) || isset($student['national_id']) || isset($student['study_group']) ) {
                $export_data[$i][] = $student['name'];
                $export_data[$i][] = $student['username'];
                $export_data[$i][] = $student['national_id'];
                $export_data[$i][] = $student['military_number'];
                $export_data[$i][] = $student['study_group'];
                }else {
                    $export_data[$i][] = 'Unknown Name';
                    $export_data[$i][] = 'Unknown code';
                    $export_data[$i][] = 'Unknown study_group';
                }
                $export_data[$i][] = $tickets_administrative[$i]->year;
                $export_data[$i][] = $tickets_administrative[$i]->ticket_id;
                $export_data[$i][] = $tickets_administrative[$i]->insurance;
                $export_data[$i][] = $tickets_administrative[$i]->profile_expenses;
                $export_data[$i][] = $tickets_administrative[$i]->registration_fees;
                $export_data[$i][] = $tickets_administrative[$i]->card_and_email;
                $export_data[$i][] = $tickets_administrative[$i]->renew_card_and_email;
                $export_data[$i][] = $tickets_administrative[$i]->military_expenses;
                $export_data[$i][] = $tickets_administrative[$i]->amount;
                $export_data[$i][] = $tickets_administrative[$i]->payment_type;
                $export_data[$i][] = $tickets_administrative[$i]->visa_number;
                $export_data[$i][] = $tickets_administrative[$i]->confirmed_at;
            }
            try {
                return Excel::download(new ReportsExport([], $headers, $export_data),
                    'سجل الادارية من ' . $data['start_date'] . ' الى ' . $data['end_date'] . '.xlsx');
            } catch (Exception $e) {
                return redirect()->back()->withErrors('خطأ في الإتصال')->withInput();
            }
        }
        elseif($action == 'excel_extra_fees'){
            $tickets_extra_fees = $tickets_extra_fees->toArray();
            $headers = [
                [
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'اسم الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'كود الطالب'
                    ],
                     [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم بطاقة الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم تجنيد الطالب'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'الفرقة الدراسية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'السنة الدارسية'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم االحافظة'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'المبلغ الاجمالي'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'طريقة الدفع'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'رقم الفيزا'
                    ],
                    [
                        'col' => 1,
                        'row' => 1,
                        'text' => 'تاريخ السداد'
                    ],
                ],
            ];
            $export_data = [];
            for ($i = 0; $i < count($tickets_extra_fees); $i++) {
                $student = $this->getStudentInfo($tickets_extra_fees[$i]->student_code);
                if (isset($student['name']) || isset($student['username']) || isset($student['military_number']) || isset($student['national_id']) || isset($student['study_group']) ) {
                $export_data[$i][] = $student['name'];
                $export_data[$i][] = $student['username'];
                $export_data[$i][] = $student['national_id'];
                $export_data[$i][] = $student['military_number'];
                $export_data[$i][] = $student['study_group'];
                }else {
                    $export_data[$i][] = 'Unknown Name';
                    $export_data[$i][] = 'Unknown code';
                    $export_data[$i][] = 'Unknown study_group';
                }
                $export_data[$i][] = $tickets_extra_fees[$i]->year;
                $export_data[$i][] = $tickets_extra_fees[$i]->ticket_id;
                $export_data[$i][] = $tickets_extra_fees[$i]->amount;
                $export_data[$i][] = $tickets_extra_fees[$i]->payment_type;
                $export_data[$i][] = $tickets_extra_fees[$i]->visa_number;
                $export_data[$i][] = $tickets_extra_fees[$i]->confirmed_at;
            }
            try {
                return Excel::download(new ReportsExport([], $headers, $export_data),
                    'سجل الخدمات التعليمية من ' . $data['start_date'] . ' الى ' . $data['end_date'] . '.xlsx');
            } catch (Exception $e) {
                return redirect()->back()->withErrors('خطأ في الإتصال')->withInput();
            }
        }

        elseif ($action == 'total') {
            $dates = DB::table('daily_payments_datetime')->whereBetween('date', $data)
            ->orderBy('date')->get()->toArray();
        if (empty($dates)) {
            return redirect()->back()->withErrors('لا توجد بيانات فى هذا التاريخ')->withInput();
        }
            $start_date = $dates[0];
            $end_date = end($dates);
            $cash = $credit = $study = $other = $wallet = $count = $discount_wallet_administrative = $count_administrative =
            $insurance = $profile_expenses = $registration_fees = $renew_card_and_email = $card_and_email =
            $credit_administrative_military =  $cash_administrative_military =
            $military_expenses  = $cash_administrative = $credit_administrative =
            $discount_wallet_administrative_insurance = $discount_wallet_administrative_profile_expenses = $discount_wallet_administrative_registration_fees=
            $discount_wallet_administrative_card_and_email = $discount_wallet_administrative_renew_card_and_email = $count_extra_fees
            = $amount_total = $extra_fees_cash = $extra_fees_credit = '';
            extract($this->getTicketsInfo($tickets));
            extract($this->getTicketsAdministrativeInfo($tickets_administrative));
            extract($this->getExtraFeesInfo($tickets_extra_fees));
            return view('finance.reports', compact('start_date', 'end_date', 'cash', 'credit','count_administrative',
                'study', 'other', 'count', 'wallet',
                'cash_administrative','credit_administrative','insurance', 'profile_expenses',
                'registration_fees', 'card_and_email','renew_card_and_email','military_expenses',
            'credit_administrative_military','cash_administrative_military','discount_wallet_administrative',
            'discount_wallet_administrative_insurance','discount_wallet_administrative_profile_expenses','discount_wallet_administrative_registration_fees',
            'discount_wallet_administrative_card_and_email','discount_wallet_administrative_renew_card_and_email',
            'count_extra_fees','amount_total', 'extra_fees_cash','extra_fees_credit','extra_fees_discount'));
        } else {
            $end = Carbon::make($data['end_date']);
            $days = [];

            for ($start = Carbon::make($data['start_date']); $start->lessThanOrEqualTo($end); $start->addDay()) {
                $tickets_info = DB::table('daily_payments_datetime')->where('date', $start);
                if ($tickets_info->exists()) {
                    $days[$start->toDateString()] = $this->getTicketsInfo($this->getTickets(
                        $tickets_info->first()->start_date, $tickets_info->first()->end_date)) + $this->getTicketsAdministrativeInfo($this->getTicketsAdministrative(
                            $tickets_info->first()->start_date, $tickets_info->first()->end_date) +
                            $this->getExtraFeesInfo($this->getُExtaFeesPayments( $tickets_info->first()->start_date, $tickets_info->first()->end_date)));
                    } else {
                        $days[$start->toDateString()] = $this->getTicketsInfo(collect()) + $this->getTicketsAdministrativeInfo(collect()) + $this->getExtraFeesInfo(collect());
                    }

            }
            return view('finance.reports', ['days' => $days, 'start_date' => $data['start_date'],
                'end_date' => $data['end_date']]);
        }
    }

    public function discountIndex(Request $request)
    {
        $rule = [
            'username' => ['nullable', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if (!$this->discountExists($value))
                        $fail("الطالب $value ليس له خصومات مسجله.");
                }
            ],
        ];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {
            return redirect()->route('discount.index')->withErrors($validator->errors());
        } elseif (!is_null($request->username)) {
            $discounts = DB::table('students_discounts')->where('student_code', $request->username)
                ->orderBy('year')->orderBy('semester')->get()
                ->map(function ($value) {
                    if ($value->type == 'دراسية') {
                        $value->remove = (!$this->ticketSemesterExists($value->student_code, 1, 'دراسية',
                            $value->year, $value->semester));
                    } elseif ($value->type == 'محفظة') {
                        $wallet = $this->getStudentWallet($value->student_code);
                        $value->remove = (!empty($wallet) and $wallet->amount >= $value->amount);
                    } else {
                        $value->remove = false;
                    }
                    return $value;
                })->toArray();
            $student = $this->getStudentInfo($request->username);
            return view('finance.discount', compact('student', 'discounts'));
        }
        return view('finance.discount');
    }

    public function getUnpaidPayments(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'student_code' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
            ]);
            if ($validator->fails()) {
                return Response()->json(['error' => 'بيانات غير صالحة'], 400);
            }
            $student_code = $validator->validate()['student_code'];
            $payment = $this->getLastPayment($student_code);
            if (!is_null($payment) and $payment->payment > 0) {
                $output = [
                    'text' => $payment->semester . '-' . $payment->year . '  (' .
                        ($payment->payment - $payment->paid_payments) . ')',
                    'value' => $payment->semester . '-' . $payment->year
                ];
                return Response($output, 200);
            }
            return Response()->json(['error' => 'تم الانتهاء من المالية'], 400);
        }
        abort(404);
    }

 public function storeDiscount(Request $request)
    {
        $check = $request->validate(['file' => 'nullable|file|mimes:csv,xls,xlsx']);
        if (empty($check)) {
            $type = $request->validate(['type' => 'required|in: خدمات تعليمية,دراسية,محفظة,ادارية'])['type'];
            if ($type == 'دراسية') {
                [$semester, $year] = explode('-', $request->validate(['semester' => 'required|string'])['semester']);
                $student_code = $request->validate([
                    'username' => ['required', 'string', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                        function ($attribute, $value, $fail) use ($type, $semester, $year) {
                            if ($this->ticketSemesterExists($value, 1, $type, $year, $semester))
                                $fail('تم الإنتهاء من مالية الطالب ' . $value);
                            if (!$this->oldPaymentExists($value))
                                $fail('تم الإنتهاء من مالية الطالب ' . $value);
                        },
                    ],
                ])['username'];
                $amount = $request->validate([
                    'amount' => 'required|numeric|between:1,' .
                        ($this->getTotalStudyPay($student_code, $year, $semester) -
                            $this->getTotalStudyDiscount($student_code, $year, $semester))])['amount'];
                $reason = $request->validate(['reason' => 'required|string|max:255|regex:/^[^<>#;*]+$/u'])['reason'];
                $data = [
                    'student_code' => $student_code,
                    'year' => $year,
                    'semester' => $semester,
                    'type' => $type,
                    'amount' => $amount,
                    'reason' => $reason,
                    'created_by' => auth()->id(),
                    'created_at' => Carbon::now(),
                ];
                try {
                    DB::transaction(function () use ($data) {
                        DB::table('students_discounts')->insert($data);
                        unset($data['amount']);
                        unset($data['reason']);
                        $data['used'] = 0;
                        DB::table('payment_tickets')->where($data)->delete();
                        $payment = DB::table('students_payments')->where('student_code',
                            $data['student_code'])->where('year', $data['year'])
                            ->where('semester', $data['semester']);
                        if ($payment->first()->paid_payments > 0) {
                            DB::table('students_wallet_transaction')->insert([
                                'student_code' => $data['student_code'],
                                'year' => $data['year'],
                                'semester' => $data['semester'],
                                'amount' => $payment->first()->paid_payments,
                                'date' => Carbon::now(),
                                'type' => 'ايداع',
                                'reason' => 'استرجاع مصاريف دراسية من اضافة الخصومات',
                            ]);
                            DB::table('students_wallet')->where('student_code', $data['student_code'])
                                ->increment('amount', $payment->first()->paid_payments);
                            $payment->update(['paid_payments' => 0]);
                        }
                    });
                    return redirect()->back()->with(['success' => 'تم وضع الخصم بنجاح']);
                } catch (Exception $e) {
                    return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
                }
            }
             elseif($type == 'محفظة') {
                $student_code = $request->validate([
                    'username' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
                ])['username'];
                $amount = $request->validate([
                    'amount' => 'required|numeric|between:1,20000'])['amount'];
                $reason = $request->validate(['reason' => 'required|string|max:255|regex:/^[^<>#;*]+$/u'])['reason'];
                $data = [
                    'student_code' => $student_code,
                    'year' => $this->getCurrentYear(),
                    'semester' => $this->getCurrentSemester(),
                    'type' => $type,
                    'amount' => $amount,
                    'reason' => $reason,
                    'created_by' => auth()->id(),
                    'created_at' => Carbon::now(),
                ];
                try {
                    DB::transaction(function () use ($data) {
                        DB::table('students_discounts')->insert($data);
                        DB::table('students_wallet_transaction')->insert([
                            'student_code' => $data['student_code'],
                            'year' => $data['year'],
                            'semester' => $data['semester'],
                            'amount' => $data['amount'],
                            'date' => Carbon::now(),
                            'type' => 'ايداع',
                            'reason' => 'اضافة مصاريف من الخصومات',
                        ]);
                        if (!empty($this->getStudentWallet($data['student_code']))) {
                            DB::table('students_wallet')->where('student_code', $data['student_code'])
                                ->increment('amount', $data['amount']);
                        } else {
                            DB::table('students_wallet')->insert([
                                'student_code' => $data['student_code'],
                                'amount' => $data['amount'],
                            ]);
                        }
                    });
                    return redirect()->back()->with(['success' => 'تم وضع الخصم بنجاح']);
                } catch (Exception $e) {
                    return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
                }
            }
            elseif($type == 'ادارية'){
                $student_code = $request->validate([
                    'username' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
                ])['username'];
                $amount = $request->validate([
                    'amount' => 'required|numeric|between:1,20000'])['amount'];
                $reason = $request->validate(['reason' => 'required|string|max:255|regex:/^[^<>#;*]+$/u'])['reason'];
                $year = $this->getCurrentYear();
                // $get_amount = DB::table('payments_administrative_expenses')->where('student_code',$request->username)->where('year',$year)->pluck('amount')->toArray()[0];
                // if($request->amount != $get_amount){
                //     return redirect()->back()->with('error', 'هذا الطالب لدية محفظة بقيمة' . $get_amount)->withInput();
                // }
                $data = [
                    'student_code' => $student_code,
                    'year' => $this->getCurrentYear(),
                    'semester' => $this->getCurrentSemester(),
                    'type' => $type,
                    'amount' => $amount,
                    'reason' => $reason,
                    'created_by' => auth()->id(),
                    'created_at' => Carbon::now(),
                ];
                try {
                    DB::transaction(function () use ($data) {
                        DB::table('students_discounts')->insert($data);
                        DB::table('payments_administrative_expenses')
                        ->where('student_code',$data['student_code'])
                        ->where('amount',$data['amount'])
                        ->where('year',$data['year'])->update([
                            'payment_type' => 'Excep',
                            'used' => 1,
                            'confirmed_by' => auth()->id(),
                        ]);


                    });
                    return redirect()->back()->with(['success' => 'تم وضع الخصم بنجاح']);
                } catch (Exception $e) {
                    dd($e);
                    return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
                }
            }elseif($type == 'خدمات تعليمية'){
                $student_code = $request->validate([
                    'username' => 'required|string|regex:/^[RT][0-9]{6}$/u|exists:students,username',
                ])['username'];
                $reason = $request->validate(['reason' => 'required|string|max:255|regex:/^[^<>#;*]+$/u'])['reason'];
                $year = $this->getCurrentYear();
                // $get_amount = DB::table('payments_extra_fees')->where('student_code',$request->username)->where('year',$year)->pluck('amount')->toArray()[0];
                // if($request->amount != $get_amount){
                //     return redirect()->back()->with('error', 'هذا الطالب لدية محفظة بقيمة' . $get_amount)->withInput();
                // }
                $data = [
                    'student_code' => $student_code,
                    'year' => $this->getCurrentYear(),
                    'semester' => $this->getCurrentSemester(),
                    'type' => $type,
                    'amount' => $amount,
                    'reason' => $reason,
                    'created_by' => auth()->id(),
                    'created_at' => Carbon::now(),
                ];
                try {
                    DB::transaction(function () use ($data) {
                        DB::table('students_discounts')->insert($data);
                        DB::table('payments_extra_fees')->insert([
                            'student_code' => $data['student_code'],
                            'year' => $data['year'],
                            'amount' => $data['amount'],
                            'date' => Carbon::now(),
                            'payment_type' => 'Excep',
                            'used' => 1,
                            'created_by' => auth()->id(),
                        ]);
                    });
                    return redirect()->back()->with(['success' => 'تم وضع الخصم بنجاح']);
                } catch (Exception $e) {
                    return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
                }
            }

        }
        ################## EXCEL ###########################
        else {
            $discounts = Excel::toArray(null, $check['file'])[0];
            $validator = Validator::make(['discounts' => $discounts], [
                'discounts' => 'array|min:2',
                'discounts.*' => 'array|size:4',
                'discounts.*.*' => 'required',
                'discounts.0.0' => 'required|in:كود الطالب',
                'discounts.0.1' => 'required|in:نوع الخصم',
                'discounts.0.2' => 'required|in:قيمة الخصم,قيمه الخصم',
                'discounts.0.3' => 'required|in:سبب الخصم',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
            unset($discounts[0]);
            $year = $this->getCurrentYear();
            $semester = $this->getCurrentSemester();
            $validator = Validator::make(['discounts' => $discounts], [
                'discounts' => 'array|min:1',
                'discounts.*' => 'array|size:4',
                'discounts.*.0' => ['required', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                    function ($attribute, $value, $fail) use ($year, $discounts) {
                      $check_is_exist = DB::table('students_discounts')->where('student_code',$value)->where('year',$year)->whereIn('type',$discounts[1])->exists();
                      if($check_is_exist){
                           $fail('هناك خصم سابق للطالب ' . $value);
                      }
                    }],
                    'discounts.*.1' => ['required', 'in:خدمات تعليمية,خدمات تعليميه,ادارية,اداريه,دراسية,دراسيه,محفظه,محفظة',
                    function ($attribute, $value, $fail) use ($semester, $year, $discounts) {
                        $i = explode('.', $attribute)[1];
                        if (in_array($value, ['دراسية', 'دراسيه'])) {
                            if ($this->ticketSemesterExists($discounts[$i][0], 1, 'دراسية', $year, $semester))
                                $fail('تم الإنتهاء من مالية الطالب ' . $discounts[$i][0]);
                            if (!$this->oldPaymentExists($discounts[$i][0]))
                                $fail('تم الإنتهاء من مالية الطالب ' . $discounts[$i][0]);
                        }
                    }],
                'discounts.*.2' => ['required', 'numeric', 'min:1',
                    function ($attribute, $value, $fail) use ($semester, $year, $discounts) {
                        $i = explode('.', $attribute)[1];
                        $pay = ($this->getTotalStudyPay($discounts[$i][0], $year, $semester) -
                            $this->getTotalStudyDiscount($discounts[$i][0], $year, $semester));
                        if (in_array($discounts[$i][1], ['دراسية', 'دراسيه']) and
                            $value > $pay)
                            $fail('يجب ان يكون الخصم اصغر من او يساوى ' . $pay . ' فى السطر ' . ($i + 1));
                            // $get_amount = DB::table('payments_administrative_expenses')->where('student_code',$discounts[$i][0])->where('year',$year)->pluck('amount')->toArray()[0];
                            //  $get_amount_fees = DB::table('payments_extra_fees')->where('student_code',$discounts[$i][0])->where('year',$year)->pluck('amount')->toArray()[0];
                            // if ($value != $get_amount && ($discounts[$i][1] == 'ادارية' || $discounts[$i][1] == 'اداريه')) {
                            //     $fail( 'هذا الطالب ' . $discounts[$i][0] . ' لديه محفظة بقيمة ' . $get_amount);
                            // }
                            // elseif($value != $get_amount_fees && ($discounts[$i][1] == 'خدمات تعليمية' || $discounts[$i][1] == 'خدمات تعليميه')){

                            // }
                    }],
                'discounts.*.3' => 'required|string|max:255|regex:/^[^<>#;*]+$/u',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
            try {
                DB::transaction(function () use ($discounts, $year, $semester) {
                    foreach ($discounts as $discount) {

                        $data = [
                            'student_code' => $discount[0],
                            'year' => $year,
                            'semester' => $semester,
                            'type' => ($discount[1] == 'دراسيه') ? 'دراسية' :
                             (
                        ($discount[1] == 'محفظه') ? 'محفظة' :
                        (
                            ($discount[1] == 'اداريه') ? 'ادارية' :
                            (
                                ($discount[1] == 'خدمات تعليميه') ? 'خدمات تعليمية' : $discount[1]
                            )
                        )
                            ),
                            'amount' => $discount[2],
                            'reason' => $discount[3],
                            'created_by' => auth()->id(),
                            'created_at' => Carbon::now(),
                        ];

                        DB::table('students_discounts')->insert($data);

                        if ($data['type'] == 'دراسية') {
                            unset($data['amount']);
                            unset($data['reason']);
                            $data['used'] = 0;
                            DB::table('payment_tickets')->where($data)->delete();
                            $payment = DB::table('students_payments')->where('student_code',
                                $data['student_code'])->where('year', $data['year'])
                                ->where('semester', $data['semester']);
                            if ($payment->first()->paid_payments > 0) {
                                DB::table('students_wallet_transaction')->insert([
                                    'student_code' => $data['student_code'],
                                    'year' => $data['year'],
                                    'semester' => $data['semester'],
                                    'amount' => $payment->first()->paid_payments,
                                    'date' => Carbon::now(),
                                    'type' => 'ايداع',
                                    'reason' => 'استرجاع مصاريف دراسية من اضافة الخصومات',
                                ]);
                                DB::table('students_wallet')->where('student_code', $data['student_code'])
                                    ->increment('amount', $payment->first()->paid_payments);
                                $payment->update(['paid_payments' => 0]);
                            }
                        } elseif($data['type'] == 'محفظة') {
                            DB::table('students_wallet_transaction')->insert([
                                'student_code' => $data['student_code'],
                                'year' => $data['year'],
                                'semester' => $data['semester'],
                                'amount' => $data['amount'],
                                'date' => Carbon::now(),
                                'type' => 'ايداع',
                                'reason' => 'اضافة مصاريف من الخصومات',
                            ]);
                            if (!empty($this->getStudentWallet($data['student_code']))) {
                                DB::table('students_wallet')->where('student_code', $data['student_code'])
                                    ->increment('amount', $data['amount']);
                            } else {
                                DB::table('students_wallet')->insert([
                                    'student_code' => $data['student_code'],
                                    'amount' => $data['amount'],
                                ]);
                            }
                        }else if ($data['type'] == 'ادارية') {

                             DB::table('payments_administrative_expenses')
                        ->where('student_code',$data['student_code'])
                        ->where('amount',$data['amount'])
                        ->where('year',$data['year'])->update([
                            'payment_type' => 'Excep',
                            'used' => 1,
                            'confirmed_by' => auth()->id(),
                        ]);
                        }else if ($data['type'] == 'خدمات تعليمية') {
                            DB::table('payments_extra_fees')
                            ->where('student_code',$data['student_code'])
                            ->where('amount',$data['amount'])
                            ->where('year',$data['year'])->update([
                                'payment_type' => 'Excep',
                                'used' => 1,
                                'confirmed_by' => auth()->id(),
                            ]);
                        }
                    }
                });
                return redirect()->back()->with(['success' => 'تم وضع الخصم الملف بنجاح']);
            } catch (\Exception $e) {
                dd($e);
                return redirect()->back()->with('error', 'خطأ في الإتصال')->withInput();
            }
        }
    }

    public function deleteDiscount($student_code, $id)
    {
        $rule = [
            'username' => ['required', 'regex:/^[RT][0-9]{6}$/u', 'exists:students,username',
                function ($attribute, $value, $fail) {
                    if (!$this->discountExists($value, 'محفظة') and !$this->discountExists($value, 'دراسية'))
                        $fail("الطالب $value ليس له خصومات مسجله.");
                }
            ],
            'id' => 'required|integer|exists:students_discounts,id',
        ];
        $validator = Validator::make(['username' => $student_code, 'id' => $id], $rule);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        $discount = DB::table('students_discounts')->where('id', $id)
            ->where('student_code', $student_code);
        if ($discount->exists()) {
            $discount = $discount->first();
            if ($discount->type == 'دراسية') {
                if (!$this->ticketSemesterExists($discount->student_code, 1, 'دراسية',
                    $discount->year, $discount->semester)) {
                    try {
                        DB::transaction(function () use ($discount, $student_code, $id) {
                            DB::table('students_discounts')->where('id', $id)->delete();
                            DB::table('payment_tickets')->where('student_code', $student_code)
                                ->where('type', $discount->type)->where('year', $discount->year)
                                ->where('semester', $discount->semester)->where('used', 0)->delete();
                            $payment = DB::table('students_payments')->where('student_code', $student_code)
                                ->where('year', $discount->year)->where('semester', $discount->semester);
                            if ($payment->first()->paid_payments > 0) {
                                DB::table('students_wallet_transaction')->insert([
                                    'student_code' => $student_code,
                                    'year' => $discount->year,
                                    'semester' => $discount->semester,
                                    'amount' => $payment->first()->paid_payments,
                                    'date' => Carbon::now(),
                                    'type' => 'ايداع',
                                    'reason' => 'استرجاع مصاريف دراسية من حذف الخصومات',
                                ]);
                                DB::table('students_wallet')->where('student_code', $student_code)
                                    ->increment('amount', $payment->first()->paid_payments);
                                $payment->update(['paid_payments' => 0]);
                            }
                        });
                        return redirect()->route('discount.index')->with('success', 'تم حذف الخصم بنجاح');
                    } catch (Exception $e) {
                        return redirect()->back()->with('error', 'خطأ في الإتصال');
                    }
                }
            } else {
                $wallet = $this->getStudentWallet($student_code);
                if (!empty($wallet)) {
                    if ($wallet->amount >= $discount->amount) {
                        try {
                            DB::transaction(function () use ($discount, $student_code, $id) {
                                DB::table('students_discounts')->where('id', $id)->delete();
                                DB::table('students_wallet_transaction')->insert([
                                    'student_code' => $student_code,
                                    'year' => $discount->year,
                                    'semester' => $discount->semester,
                                    'amount' => $discount->amount,
                                    'date' => Carbon::now(),
                                    'type' => 'سحب',
                                    'reason' => 'سحب مصاريف من حذف الخصومات',
                                ]);
                                DB::table('students_wallet')->where('student_code', $student_code)
                                    ->decrement('amount', $discount->amount);
                            });
                            return redirect()->route('discount.index')->with('success', 'تم حذف الخصم بنجاح');
                        } catch (Exception $e) {
                            return redirect()->back()->with('error', 'خطأ في الإتصال');
                        }
                    } else {
                        return redirect()->back()->withErrors(['wallet' => 'لا يوجد مبلغ كافي فى المحفظة لالغاء الخصم']);
                    }
                }
            }
        }
        return redirect()->back()->withErrors(['id' => 'خطأ فى البيانات']);
    }
     public function createpayAdministrativeExpenses(){
        $daily_payments_datetime = DB::table('daily_payments_datetime')->where('end_date',NULL)->pluck('date')[0];
        return view('finance.administrative_expenses',compact('daily_payments_datetime'));
    }
     public function getTotalValue($ticket_id){
        $username = substr($ticket_id, 12, 7);
        $student = $this->getStudentInfo($username);
        $total = $this->getStudentAdministrativeExpenses($username)->amount;
         return $total;
    }

      public function payAdministrativeExpenses(Request $request){
            if (!$this->canPay()) {
                return redirect()->back()->with('error', 'الخزينة مغلقة لا يمكن السداد الان')->withInput();
            }


        $rule = [
                   'ticket_id' => ['required', 'string', 'between:11,20'
                , function ($attribute, $value, $fail) {
                   $check_used  = DB::table('payments_administrative_expenses')->where('ticket_id', $value)
                    ->where('used', 1)->exists();
                    if (!$value || $check_used)
                        $fail('الحافظه غير صحيحه او تم ادخالها من قبل.');
                }
            ],
            'payment_type' => 'required|in:كاش,كريدت',
            'visa_number' => ($request->payment_type == 'كريدت') ? 'required|digits:4' : 'nullable|regex:/^$/i',
            'confirmed_at' => 'date',
        ];

        $data = $request->validate($rule);
        $username = substr($data['ticket_id'], 12, 7);
        $student = $this->getStudentInfo($username);
        if($student['study_group'] == 'الثالثة' and $student['gender']){
                DB::table('students')->where('username',$student['username'])->update([
                    'military_education' => 'مؤهل'
                    ]);
            }
        if ($request->filled('ticket_id')) {
            $total = $this->getStudentAdministrativeExpenses($username)->amount;
        }
        $ticket = DB::table('payments_administrative_expenses')->where('ticket_id', $data['ticket_id']);
        if ($ticket->first()->amount < 0) {
            return redirect()->back()->with('error', 'قيمة الحافظه غير صحيحه.')->withInput();
        }else{
            DB::table('payments_administrative_expenses')->where('ticket_id', $data['ticket_id'])->update([
                        'used' => 1,
                        'payment_type' => $data['payment_type'],
                        'visa_number' => (empty($data['visa_number']) ? null : $data['visa_number']),
                        'confirmed_by' => auth()->id(),
                        'confirmed_at' => Carbon::now(),

            ]);
        }
        $date = Carbon::now();
        $url = route('create.pay.administrative.expenses');
        session()->flash('success', 'تم تأكيد الدفع بنجاح');
                return view('finance.print_receipt_expenses', compact('student', 'url','data','date','total'));
    }
     public function payExtraFees(Request $request){
        if (!$this->canPay()) {
            return redirect()->back()->with('error', 'الخزينة مغلقة لا يمكن السداد الان')->withInput();
        }
         $rule = [
            'ticket_id' => ['required', 'string', 'between:11,20'
            , function ($attribute, $value, $fail) {
               $check_used  = DB::table('payments_extra_fees')->where('ticket_id', $value)
                ->where('used', 1)->exists();
                if (!$value || $check_used)
                    $fail('الحافظه غير صحيحه او تم ادخالها من قبل.');
            }
        ],
        'payment_type' => 'required|in:كاش,كريدت',
        'visa_number' => ($request->payment_type == 'كريدت') ? 'required|digits:4' : 'nullable|regex:/^$/i',
        'confirmed_at' => 'date',
    ];
    $data = $request->validate($rule);
    $username = substr($data['ticket_id'], 12, 7);
    $student = $this->getStudentInfo($username);
    $ticket_fees = DB::table('payments_extra_fees')->where('ticket_id', $data['ticket_id']);
    if ($ticket_fees->first()->amount < 0) {
        return redirect()->back()->with('error', 'قيمة الحافظه غير صحيحه.')->withInput();
    }else{
        DB::table('payments_extra_fees')->where('ticket_id', $data['ticket_id'])->update([
                    'used' => 1,
                    'payment_type' => $data['payment_type'],
                    'visa_number' => (empty($data['visa_number']) ? null : $data['visa_number']),
                    'confirmed_by' => auth()->id(),
                    'confirmed_at' => Carbon::now(),

        ]);
    }
    $date = Carbon::now();
    $url = route('create.pay.administrative.expenses');
    session()->flash('success', 'تم تأكيد الدفع بنجاح');
            return view('finance.print_receipt_extra_fees', compact('student', 'url','data','date','ticket_fees'));
}
        public function AdministrativeExpensesDiscount(){
            return view('finance.discount_administrative_expenses');
        }
        public function payAdministrativeExpensesDiscount(Request $request)
    {
                if (!$this->canPay()) {
                    return redirect()->back()->with('error', 'الخزينة مغلقة لا يمكن السداد الان')->withInput();
                }

        $rules = [
           'ticket_id' => ['required', 'string', 'between:11,20'],
            'confirmed_at' => 'date',
        ];
        $data = $request->validate($rules);
        $username = substr($data['ticket_id'], 12, 7);
        $student = $this->getStudentInfo($username);
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        if ($request->filled('ticket_id')) {
            $total = $this->getStudentAdministrativeExpenses($username)->amount;
        }
        $ticket = DB::table('payments_administrative_expenses')->where('ticket_id', $data['ticket_id']);
        $wallet = $this->getStudentWallet($username)->amount;
        if ($total > $wallet) {
            return redirect()->back()->with('error', 'قيمة المحفظة أقل من المطلوب.')->withInput();
        }

        else {
           try {
            DB::transaction(function () use ($data) {
                $payment = DB::table('payments_administrative_expenses')
            ->where('ticket_id', $data['ticket_id'])
            ->first();

        if ($payment && $payment->used == 1) {
            throw new \Exception('تم دفع الطالب من قبل');
        }

        DB::table('payments_administrative_expenses')
            ->where('ticket_id', $data['ticket_id'])
            ->update([
                'used' => 1,
                'payment_type' => 'خصم',
                'visa_number' => empty($data['visa_number']) ? null : $data['visa_number'],
                'confirmed_by' => 'Wallet_admin',
                'confirmed_at' => Carbon::now(),
                     ]);
            });
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }
                DB::table('students_wallet')->where('student_code', $username)->update([
                'amount' => $wallet - $total,
            ]);
            DB::table('students_wallet_transaction')->insert([
                'student_code' => $username,
                'year' => $year,
                'semester' => $semester,
                'amount' => $total,
                'date' => Carbon::now(),
                'type' => 'سحب',
                'reason' => 'سحب مصاريف ادارية',
            ]);
        }

        $date = Carbon::now();
        $url = route('dministrative.expenses.discount');
        session()->flash('success', 'تم تأكيد الدفع بنجاح');
        return view('finance.print_receipt_expenses', compact('student', 'url','data','date','total'));
        }
             public function editTypePayment(Request $request, $ticket_id){

        $payments_data = DB::table('payment_tickets')->where('ticket_id', $ticket_id)->first();
        return view('finance.update_payment_type', compact('payments_data'));
         }

       public function updateTypePayment(Request $request , $ticket_id){

        $rules = [
           'ticket_id' => ['required', 'string', 'exists:payment_tickets,ticket_id'],
            'payment_type' => 'in:كاش,كريدت',
            'visa_number' => ($request->payment_type == 'كريدت') ? 'required|digits:4' : 'nullable|regex:/^$/i',
        ];
        $data = $request->validate($rules);
        try{
        DB::table('payment_tickets')
            ->where('ticket_id', $data['ticket_id'])
            ->update([
                'payment_type' => $data['payment_type'],
                'visa_number' => empty($data['visa_number']) ? null : $data['visa_number'],
                     ]);
                     return redirect()->back()->with(['success' => 'تم التحديث بنجاح']);

             }catch(Exception $e){
                dd($e);
                return redirect()->back()->with('error', 'خطأ في التعديل');
             }



       }
}
