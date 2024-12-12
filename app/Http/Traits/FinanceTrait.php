<?php

namespace App\Http\Traits;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

trait FinanceTrait
{
    use DataTrait, MoodleTrait;

    public function oldPaymentExists($username, bool $other_payment = false): bool
    {
        if ($other_payment) {
            return $this->getTotalPayment($username, 'اخرى') > 0;
        }
        return $this->getTotalPayment($username, 'دراسية') > 0;
    }

    public function otherPaymentExists($username, $year = null, $semester = null): bool
    {
        if (is_null($year)) {
            $year = $this->getCurrentYear();
        }
        if (is_null($semester)) {
            $semester = $this->getCurrentSemester();
        }
        return DB::table('students_other_payments')->where('student_code', '=', $username)
            ->where('year', $year)->where('semester', $semester)->exists();
    }

    public function discountExists($username, string $type = null, $year = null, $semester = null): bool
    {
        $data = ['student_code' => $username];
        if (!is_null($year)) {
            $data['year'] = $year;
        }
        if (!is_null($semester)) {
            $data['semester'] = $semester;
        }
        if (!is_null($type)) {
            $data['type'] = $type;
        }
        return DB::table('students_discounts')->where($data)->exists();
    }

    public function getTotalPayment($username, $mode = 'all'): float
    {
        $s_p = 0;
        $o_p = 0;
        if ($mode == 'all' or $mode == 'دراسية') {
            $study_payment = DB::table('students_payments')->where('student_code', $username)->get()
                ->groupBy(['year', 'semester'])->toArray();
            $study_discount = DB::table('students_discounts')->where('student_code', $username)
                ->where('type', 'دراسية')->get()->groupBy(['year', 'semester'])->toArray();
            foreach ($study_payment as $year => $value) {
                foreach ($value as $semester => $val) {
                    $dis = 0;
                    if (!empty($study_discount[$year]) and !empty($study_discount[$year][$semester])) {
                        foreach ($study_discount[$year][$semester] as $am) {
                            $dis += $am->amount;
                        }
                    }
                    $s_p += $val[0]->payment - $val[0]->paid_payments - $dis;
                }
            }
        }
        if ($mode == 'all' or $mode == 'اخرى') {
            $other_payment = DB::table('students_other_payments')->where('student_code', $username)->get()
                ->groupBy(['year', 'semester'])->toArray();
            $other_discount = DB::table('students_discounts')->where('student_code', $username)
                ->where('type', 'اخرى')->get()->groupBy(['year', 'semester'])->toArray();
            foreach ($other_payment as $year => $value) {
                foreach ($value as $semester => $val) {
                    $dis = 0;
                    foreach ($val as $v) {
                        $o_p += $v->payment - $v->paid_payments;
                    }
                    if (!empty($other_discount[$year]) and !empty($other_discount[$year][$semester])) {
                        foreach ($other_discount[$year][$semester] as $am) {
                            $dis += $am->amount;
                        }
                    }
                    $o_p += -$dis;
                }
            }
        }
        return $s_p + $o_p;
    }

    public function getTotalStudyPay($username, $year, $semester = null)
    {
        if (!$semester) {
            return DB::table('students_payments')->where('student_code', $username)
                ->where('year', $year)->groupBy('student_code')->sum('payment');
        }
        return DB::table('students_payments')->where('student_code', $username)
            ->where('year', $year)->where('semester', $semester)->groupBy('student_code')
            ->sum('payment');
    }

    public function getTotalStudyPaid($username, $year, $semester = null)
    {
        if (!$semester) {
            return DB::table('students_payments')->where('student_code', $username)
                ->where('year', $year)->groupBy('student_code')->sum('paid_payments');
        }
        return DB::table('students_payments')->where('student_code', $username)
            ->where('year', $year)->where('semester', $semester)->groupBy('student_code')
            ->sum('paid_payments');
    }

    public function getTotalStudyDiscount($username, $year, $semester = null)
    {
        if (!$semester) {
            return DB::table('students_discounts')->where('student_code', $username)
                ->where('year', $year)->where('type', 'دراسية')
                ->groupBy('student_code')->sum('amount');
        }
        return DB::table('students_discounts')->where('student_code', $username)
            ->where('year', $year)->where('semester', $semester)
            ->where('type', 'دراسية')->groupBy('student_code')->sum('amount');
    }

    public function getTotalOtherPay($username, $year, $semester = null)
    {
        if (!$semester) {
            return DB::table('students_other_payments')->where('student_code', $username)
                ->where('year', $year)->groupBy('student_code')->sum('payment');
        }
        return DB::table('students_other_payments')->where('student_code', $username)
            ->where('year', $year)->where('semester', $semester)->groupBy('student_code')
            ->sum('payment');
    }

    public function getTotalOtherPaid($username, $year, $semester = null)
    {
        if (!$semester) {
            return DB::table('students_other_payments')->where('student_code', $username)
                ->where('year', $year)->groupBy('student_code')->sum('paid_payments');
        }
        return DB::table('students_other_payments')->where('student_code', $username)
            ->where('year', $year)->where('semester', $semester)->groupBy('student_code')
            ->sum('paid_payments');
    }

    public function getTotalOtherDiscount($username, $year, $semester = null)
    {
        if (!$semester) {
            return DB::table('students_discounts')->where('student_code', $username)
                ->where('year', $year)->where('type', 'اخرى')
                ->groupBy('student_code')->sum('amount');
        }
        return DB::table('students_discounts')->where('student_code', $username)
            ->where('year', $year)->where('semester', $semester)
            ->where('type', 'اخرى')->groupBy('student_code')->sum('amount');
    }

    public function getTotalWalletDiscount($username, $year, $semester = null)
    {
        if (!$semester) {
            return DB::table('students_discounts')->where('student_code', $username)
                ->where('year', $year)->where('type', 'محفظة')
                ->groupBy('student_code')->sum('amount');
        }
        return DB::table('students_discounts')->where('student_code', $username)
            ->where('year', $year)->where('semester', $semester)
            ->where('type', 'محفظة')->groupBy('student_code')->sum('amount');
    }

    public function getLastPayment($username)
    {
        $payments = DB::table('students_payments')->where('student_code', $username)
            ->get()->groupBy(['year', 'semester'])->toArray();
        foreach ($payments as $year => $pay) {
            foreach ($pay as $semester => $value) {
                if (!$this->ticketSemesterExists($username, 1, 'دراسية', $year, $semester)) {
                    $discount = $this->getTotalStudyDiscount($username, $year, $semester);
                    $payment = $value[0];
                    $payment->payment -= $discount;
                    return $payment;
                }
            }
        }
        return null;
    }

    public function getLastOtherPayment($username)
    {
        $payments = DB::table('students_other_payments')->where('student_code', $username)
            ->whereRaw('payment - paid_payments > 0')->get()->groupBy(['year', 'semester'])->toArray();
        foreach ($payments as $year => $pay) {
            foreach ($pay as $semester => $value) {
                $discount = $this->getTotalOtherDiscount($username, $year, $semester);
                if ($value[0]->payment > ($value[0]->paid_payments + $discount)) {
                    $payment = $value[0];
                    $payment->payment -= $discount;
                    return $payment;
                }
            }
        }
        return null;
    }

    public function checkPayment($username, $year = null, $semester = null): ?int
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
        return empty($registration_status) ? null : $registration_status[0]->payment;
    }

    public function ticketExists($username, $used = 0, $type = null, $ticket_id = null): bool
    {
        if ($type) {
            if ($ticket_id) {
                return DB::table('payment_tickets')->where('student_code', $username)
                    ->where('type', $type)->where('ticket_id', $ticket_id)
                    ->where('used', $used)->exists();
            }
            return DB::table('payment_tickets')->where('student_code', $username)
                ->where('type', $type)->where('used', $used)->exists();
        } else {
            if ($ticket_id) {
                return DB::table('payment_tickets')->where('student_code', $username)
                    ->where('ticket_id', $ticket_id)->where('used', $used)->exists();
            }
            return DB::table('payment_tickets')->where('student_code', $username)
                ->where('used', $used)->exists();
        }
    }

    public function ticketSemesterExists($username, $used = 0, $type = null, $year = null, $semester = null): bool
    {
        $year = $year ?: $this->getCurrentYear();
        $semester = $semester ?: $this->getCurrentSemester();
        if ($type) {
            return DB::table('payment_tickets')->where('student_code', $username)
                ->where('year', $year)->where('semester', $semester)
                ->where('type', $type)->where('used', $used)->exists();
        } else {
            return DB::table('payment_tickets')->where('student_code', $username)
                ->where('year', $year)->where('semester', $semester)
                ->where('used', $used)->exists();
        }
    }

    public function getHourPayment(string $specialization, string $study_group, string $studying_status): float
    {
        $semester = $this->getCurrentSemester();
        $type = ($specialization == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
        if ($semester == 'ترم أول' or $semester == 'ترم ثاني') {
            if ($studying_status == 'باقي') {
                switch ($study_group) {
                    case 'الاولي':
                        return DB::table('hour_payment_' . $type)->find(3)->first;
                    case 'الثانية':
                        return DB::table('hour_payment_' . $type)->find(3)->second;
                    case 'الثالثة':
                        return DB::table('hour_payment_' . $type)->find(3)->third;
                    case 'الرابعة':
                        return DB::table('hour_payment_' . $type)->find(3)->fourth;
                }
            } else {
                switch ($study_group) {
                    case 'الاولي':
                        return DB::table('hour_payment_' . $type)->find(1)->first;
                    case 'الثانية':
                        return DB::table('hour_payment_' . $type)->find(1)->second;
                    case 'الثالثة':
                        return DB::table('hour_payment_' . $type)->find(1)->third;
                    case 'الرابعة':
                        return DB::table('hour_payment_' . $type)->find(1)->fourth;
                }
            }
        } else if ($semester == 'ترم صيفي') {
            return DB::table('hour_payment_' . $type)->find(1)->summer;
        }
        abort(500);
    }

    public function getMinisterialPayment(string $specialization, string $study_group, string $studying_status): float
    {
        $semester = $this->getCurrentSemester();
        $type = ($specialization == 'ترميم الاثار و المقتنيات الفنية') ? 'arabic' : 'english';
        if ($semester == 'ترم أول' or $semester == 'ترم ثاني') {
            if ($studying_status == 'باقي') {
                switch ($study_group) {
                    case 'الاولي':
                        return DB::table('hour_payment_' . $type)->find(4)->first;
                    case 'الثانية':
                        return DB::table('hour_payment_' . $type)->find(4)->second;
                    case 'الثالثة':
                        return DB::table('hour_payment_' . $type)->find(4)->third;
                    case 'الرابعة':
                        return DB::table('hour_payment_' . $type)->find(4)->fourth;
                }
            } else {
                switch ($study_group) {
                    case 'الاولي':
                        return DB::table('hour_payment_' . $type)->find(2)->first;
                    case 'الثانية':
                        return DB::table('hour_payment_' . $type)->find(2)->second;
                    case 'الثالثة':
                        return DB::table('hour_payment_' . $type)->find(2)->third;
                    case 'الرابعة':
                        return DB::table('hour_payment_' . $type)->find(2)->fourth;
                }
            }
        } else if ($semester == 'ترم صيفي') {
            return DB::table('hour_payment_' . $type)->find(2)->summer;
        }
        abort(500);
    }

    public function canPay(): bool
    {
        return DB::table('daily_payments_datetime')->whereNull('end_date')->exists();
    }

    public function getTickets($start_date, $end_date = null): \Illuminate\Support\Collection
    {
        if ($end_date) {
            $tickets = DB::table('payment_tickets')->where('used', 1)
                ->whereBetween('confirmed_at', [$start_date, $end_date])
                ->orderBy('confirmed_at')->get();
        } else {
            $tickets = DB::table('payment_tickets')->where('used', 1)
                ->where('confirmed_at', '>=', $start_date)
                ->orderBy('confirmed_at')->get();
        }
        return $tickets;
    }
    public function getTicketsAdministrative($start_date, $end_date = null): \Illuminate\Support\Collection
    {
        if ($end_date) {

            $tickets_administrative = DB::table('payments_administrative_expenses')->where('used', 1)->whereNotIn('confirmed_by',['Wallet_admin'])
                ->whereBetween('confirmed_at', [$start_date, $end_date])
                ->orderBy('confirmed_at')->get();
        } else {

            $tickets_administrative = DB::table('payments_administrative_expenses')->where('used', 1)->whereNotIn('confirmed_by',['Wallet_admin'])
                ->where('confirmed_at', '>=', $start_date)
                ->orderBy('confirmed_at')->get();
        }
        return $tickets_administrative;
    }


    public function getTicketsInfo($tickets): array
    {
        $cash = $tickets->where('payment_type', 'كاش')->sum('amount');
        $credit = $tickets->where('payment_type', 'كريدت')->sum('amount');
        $study = $tickets->where('type', 'دراسية')->sum('amount');
        $other = $tickets->where('type', 'اخرى')->sum('amount');
        $wallet = $tickets->where('type', 'محفظة')->sum('amount');
        $count = $tickets->count();
        return compact('cash', 'credit', 'study', 'other', 'wallet', 'count');
    }


    public function getTicketsAdministrativeInfo($ticketsAdministrative): array
    {
        $collection = collect($ticketsAdministrative);
        $count_administrative = $ticketsAdministrative->count();
        $insurance = $collection->pluck('insurance')->sum();
        $profile_expenses = $collection->pluck('profile_expenses')->sum();
        $registration_fees = $collection->pluck('registration_fees')->sum();
        $card_and_email = $collection->pluck('card_and_email')->sum();
        $renew_card_and_email = $collection->pluck('renew_card_and_email')->sum();
        $military_expenses = $collection->pluck('military_expenses')->sum();
        $amount_total = $collection->pluck('amount')->sum();
        $count_administrative = $collection->count();
        $cash_administrative = $collection->where('payment_type', 'كاش')->sum('amount');
        $credit_administrative = $collection->where('payment_type', 'كريدت')->sum('amount');
        $discount_wallet_administrative = $collection->where('payment_type', 'خصم')->sum('amount');
        $discount_wallet_administrative_insurance = $collection->where('payment_type', 'خصم')->pluck('insurance')->sum();
        $discount_wallet_administrative_profile_expenses = $collection->where('payment_type', 'خصم')->pluck('profile_expenses')->sum();
        $discount_wallet_administrative_registration_fees = $collection->where('payment_type', 'خصم')->pluck('registration_fees')->sum();
        $discount_wallet_administrative_card_and_email = $collection->where('payment_type', 'خصم')->pluck('card_and_email')->sum();
        $discount_wallet_administrative_renew_card_and_email = $collection->where('payment_type', 'خصم')->pluck('renew_card_and_email')->sum();

        $credit_administrative_military = $collection->where('payment_type', 'كريدت')->pluck('military_expenses')->sum();
        $cash_administrative_military  = $collection->where('payment_type', 'كاش')->pluck('military_expenses')->sum();

        return compact(
            'cash_administrative','credit_administrative','count_administrative','insurance', 'profile_expenses',
            'registration_fees', 'card_and_email','renew_card_and_email','military_expenses','amount_total',
            'credit_administrative_military','cash_administrative_military','discount_wallet_administrative',
            'discount_wallet_administrative_insurance','discount_wallet_administrative_profile_expenses','discount_wallet_administrative_registration_fees',
            'discount_wallet_administrative_card_and_email','discount_wallet_administrative_renew_card_and_email'
        );
    }

    public function getYearSemester($payments, $other_payment): array
    {
        $final = [];
        $years = array_keys($payments);
        foreach ($years as $year) {
            $final[$year] = array_keys($payments[$year]);
        }
        $years = array_keys($other_payment);
        foreach ($years as $year) {
            if (!empty($final[$year])) {
                foreach (array_keys($other_payment[$year]) as $array_key) {
                    if (!in_array($array_key, $final[$year])) {
                        $final[$year][] = $array_key;
                        sort($final[$year]);
                    }
                }
            } else {
                $final[$year] = array_keys($other_payment[$year]);
            }
        }
        asort($final);
        ksort($final);
        return $final;
    }

    public function semesterFinanceCompilation($payments, $username, $dis = false): array
    {
        $pay = [];
        if ($dis) {
            foreach ($payments as $year => $payment) {
                foreach ($payment as $semester => $value) {
                    foreach ($value as $type => $item) {
                        if ($type == 'دراسية') {
                            $pay[$year][$semester][$type]['amount'] =
                                $this->getTotalStudyDiscount($username, $year, $semester);
                        } else {
                            $pay[$year][$semester][$type]['amount'] =
                                $this->getTotalOtherDiscount($username, $year, $semester);
                        }
                    }
                }
            }
        } else {
            foreach ($payments as $year => $payment) {
                foreach ($payment as $semester => $value) {
                    $pay[$year][$semester]['payment'] = $this->getTotalOtherPay($username, $year, $semester);
                    $pay[$year][$semester]['paid_payments'] = $this->getTotalOtherPaid($username, $year, $semester);
                }
            }
        }
        return $pay;
    }

    public function getStudentFinanceStatus($username): array
    {
        $payment = DB::table('payment_tickets')->where('student_code', $username)
            ->select()->orderBy('date')->get()->groupBy(['year', 'semester'])->toArray();
        $semester_payment = DB::table('students_payments')->where('student_code',
            $username)->select()->get()->groupBy(['year', 'semester'])->toArray();
        $other_payment = DB::table('students_other_payments')->where('student_code',
            $username)->select()->get()->groupBy(['year', 'semester'])->toArray();
        $total_other_payment = $this->semesterFinanceCompilation($other_payment, $username);
        $discount = DB::table('students_discounts')->where('student_code',
            $username)->select()->get()->groupBy(['year', 'semester', 'type'])->toArray();
        $total_discount = $this->semesterFinanceCompilation($discount, $username, true);
        $year_semester = $this->getYearSemester($semester_payment, $other_payment);
        return [$payment, $semester_payment, $other_payment, $total_other_payment, $discount,
            $total_discount, $year_semester];
    }

   public function confirmStudentSemester($username, $year, $semester)
    {
        DB::table('registration_semester')->where([
            ['student_code', '=', $username],
            ['year', '=', $year],
            ['semester', '=', $semester],
        ])->update(['payment' => 1]);
        if ($year == $this->getCurrentYear() and $semester == $this->getCurrentSemester()) {
                $response = $this->uploadMoodleStudent($username);
              //   $response_book = $this->uploadMoodleBookStudent($username);
           if ($response == 'error') {
           //  if ($response == 'error' || $response_book == 'error') {
                abort(500);
            }
        }
        $this->addStudentToSection($username, $year, $semester);
    }
    public function addStudentToSection($username, $year, $semester)
    {
        $student = Student::find($username)->getOriginal();
        $number = $this->getSectionNumbers($student['specialization'], $student['study_group']);
        $data = [
            'student_code' => $username,
            'year' => $year,
            'semester' => $semester,
            'study_group' => $student['study_group'],
            'specialization' => $student['specialization']
        ];
        if ($student['studying_status'] == 'مستجد') {
            $section = DB::table('section_number')->distinct()->where(['year' => $year, 'semester' => $semester,
                'study_group' => $student['study_group'], 'specialization' => $student['specialization'],
                ['section_number', '!=', 'سكشن الباقون']])
                ->selectRaw('section_number,count(*) as count')
                ->groupBy(['study_group', 'specialization', 'section_number'])
                ->get()->sortBy('section_number')->last();
            if (is_null($section)) {
                $data['section_number'] = 1;
            } else {
                if ($section->count >= $number) {
                    $data['section_number'] = ($section->section_number + 1);
                } else {
                    $data['section_number'] = $section->section_number;
                }
            }
        } else {
            $data['section_number'] = 'سكشن الباقون';
        }
        DB::table('section_number')->insert($data);
    }

    public function getStudentWallet($username)
    {
        return DB::table('students_wallet')->where('student_code', $username)->first();
    }
      public function getStudentAdministrativeExpenses($username)
    {
        $year = $this->getCurrentYear();
        return DB::table('payments_administrative_expenses')->where('student_code', $username)->where('year', $year)->first();
    }
        public function CheckNewStudentPayment($specialization,$studying_status,$wallet,$level,$payment_required,$semester){
        if ($studying_status == 'مستجد' and $semester == 'ترم أول')
        {
            switch ($specialization)
            {
                case 'ترميم الاثار و المقتنيات الفنية':{
                $total_payment['arabic'] = (array)DB::table('hour_payment_arabic')->where('id', 6)->first();
                if ($wallet < $total_payment['arabic'][$level])
                {
                    return false;
                }
                else
                {
                    return true;
                }
                }
                break;
                case 'سياحة':{
                    $total_payment['english'] = (array)DB::table('hour_payment_english')->where('id', 6)->first();
                    if ($wallet  < $total_payment['english'] [$level])
                    {
                        return false;
                    }
                    else{
                        return true;
                    }
                }
                break;
            }

        }

        else
        {
            if ($wallet < $payment_required)
            {
                return false;
            }
            else{
                return true;
            }
        }
    }
     public function getExtrFees(){
        return DB::table('extra_fees')->get()->toArray();
    }
    public function checkActiveFees(){
        $activeFees = DB::table('extra_fees')->get('active')->toArray();
        return $activeFees;
    }
    public function getDetailsFeesActive(){
        return DB::table('extra_fees')->where('active',1)->get()->toArray();
    }
      public function getStudentExtraFees($username)
    {
        $year =$this->getCurrentYear();
        return DB::table('payments_extra_fees')->where('year',$year)->where('student_code', $username)->first();
    }
      public function checkPayFees($username)
    {
        $getDetailsFeesActive = $this->getDetailsFeesActive();
        $name_fees = array_map(function ($getDetailsFeesActive){
                    return $getDetailsFeesActive->name_fees;
        },$getDetailsFeesActive);
        $year =$this->getCurrentYear();
        return DB::table('payments_extra_fees')->where('student_code', $username)->whereIn('type',$name_fees)->where('year',$year)
        ->where('used',1)->exists();
    }
    public function getُExtaFeesPayments($start_date, $end_date = null): \Illuminate\Support\Collection
    {
        if ($end_date) {
            $tickets_fees = DB::table('payments_extra_fees')->where('used', 1)->whereNotIn('confirmed_by',['Wallet_admin'])
                ->whereBetween('confirmed_at', [$start_date, $end_date])
                ->orderBy('confirmed_at')->get();
        } else {
            $tickets_fees = DB::table('payments_extra_fees')->where('used', 1)->whereNotIn('confirmed_by',['Wallet_admin'])
                ->where('confirmed_at', '>=', $start_date)
                ->orderBy('confirmed_at')->get();
        }
        return $tickets_fees;
    }
    public function getExtraFeesInfo($getُExtaFeesPayments):array{
        $collection = collect($getُExtaFeesPayments);
        $count_extra_fees = $collection->count();
        $amount_total = $collection->pluck('amount')->sum();
        $extra_fees_cash = $collection->where('payment_type', 'كاش')->sum('amount');
        $extra_fees_credit = $collection->where('payment_type', 'كريدت')->sum('amount');
        $extra_fees_discount = $collection->where('payment_type', 'خصم')->sum('amount');
        return compact('count_extra_fees','amount_total', 'extra_fees_cash','extra_fees_credit',
        'extra_fees_discount');
    }

}
