<?php

namespace App\Http\Middleware;

use App\Http\Traits\StudentTrait;
use App\Http\Traits\DataTrait;
use App\Http\Traits\FinanceTrait;
use Illuminate\Support\Facades\DB;


use Closure;
use Illuminate\Http\Request;

class StudentChecks
{
    use StudentTrait,DataTrait,FinanceTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $year = $this->getCurrentYear();
        $semester = $this->getCurrentSemester();
        $check_payment_isTrue= DB::table('registration_semester')->where(['student_code' => auth()->id(), 'year' => $year,
        'semester' => $semester])->pluck('payment')->first();
        $student_info = $this->getStudentInfo(auth()->id());
        $studentWallet = $this->getStudentWallet(auth()->id());
        $getDetailsFeesActive = $this->getDetailsFeesActive();
        $name_fees = array_map(function ($getDetailsFeesActive){
                    return $getDetailsFeesActive->name_fees . " ". $getDetailsFeesActive->amount;
        },$getDetailsFeesActive);
        $check_payFees  = $this->checkPayFees(auth()->id());
        dd($this->getFeesNotPid(auth()->id()));

        foreach ($getDetailsFeesActive as $get_details_fee){
            $check_active_fees = $get_details_fee->active;
            $get_name_fees = $get_details_fee->name_fees;
                if($check_active_fees){
                    if($check_payment_isTrue  == 0 or !isset($check_payment_isTrue)){
                        if(!$check_payFees){
                            return redirect()->route('dashboard')->withErrors([
                                'alert' => 'برجاء استكمال '.implode(' و', $name_fees)
                                ]);
                        }
                    }
                }
        }

        if ($studentWallet) {
            $wallet = $studentWallet->amount;
         }else{
              return redirect()->route('dashboard')->withErrors([
                'alert' => 'هذا الطالب ليس له رصيد في المحفظة' ]);
         }
        if ($student_info['specialization'] == 'ترميم الاثار و المقتنيات الفنية') {
                $payments = (array)DB::table('hour_payment_arabic')->where('id', 6)->first();
            }
            else {
                $payments = (array)DB::table('hour_payment_english')->where('id', 6)->first();
            }
            $payment = 0;
            switch ($student_info['study_group']) {
                case 'الاولي':
                    $payment_total = $payments['first'];
                    break;
                case 'الثانية':
                    $payment_total = $payments['second'];
                    break;
                case 'الثالثة':
                    $payment_total = $payments['third'];
                    break;
                case 'الرابعة':
                    $payment_total = $payments['fourth'];
                    break;
            }

          //  dd($payments ,$payment_total);

             if($student_info['studying_status'] == 'باقي'){
                    $check_guidance= DB::table('registration_semester')->where(['student_code' => auth()->id(), 'year' => $year, 'semester' => $semester])->pluck('guidance');
                    $check_payment_remaining= DB::table('registration_semester')->where(['student_code' => auth()->id(), 'year' => $year, 'semester' => $semester])->pluck('payment');
                 if (isset($check_guidance[0]) && isset($check_payment_remaining[0]) && $check_guidance[0] == 1 && $check_payment_remaining[0] == 1) {
                        return $next($request);
                } else {
                    return redirect()->route('dashboard')->withErrors([
                        'alert' => 'برجاء الاتصال بالمرشد الأكاديمي',
                    ]);
                }
            }

          $checkPayment = DB::table('payment_tickets')->where(['student_code' => auth()->id(), 'year' => $year, 'semester' => $semester, 'type' => 'محفظة'])->pluck('used');
          $get_pay = $this->getTotalStudyPaid(auth()->id(),$year);
          $sum_paid = $get_pay + $wallet;

          if (isset($wallet) and isset($get_pay)) {
            $required_remaining = $payment_total - $sum_paid;
        } else {
              return redirect()->route('dashboard')->withErrors([
                    'alert' => 'برجاء التوجه لشئون الطلاب لسداد القسط ' ]);
            }
        $paymentAdministrativeExpenses = $this->checkTotalAdministrativeExpenses(auth()->id());
        $payment_used = $this->checkUsedAdministrativeExpenses(auth()->id());
        if (!isset($checkPayment[0]) || isset($wallet) || isset($check_payment_isTrue)){
         if ( ($sum_paid < $payment_total) and $check_payment_isTrue  == 0  ) {
            return redirect()->route('dashboard')->withErrors([
            'alert' => 'برجاء اكمال المستحقات الماليه التى تقدر ب ' . $required_remaining]);
         }

         elseif(empty($paymentAdministrativeExpenses[0]) || !isset($paymentAdministrativeExpenses[0]) || $payment_used == 0){
            return redirect()->route('dashboard')->withErrors([
                'alert' => 'برجاء اكمال المستحقات الماليه الادارية ']);
            }

        }else{
             return $next($request);

        }

        $alerts = $this->getAlerts(auth()->id());
        if (in_array('danger', array_column(json_decode(json_encode($alerts), true), 'status'))) {
            return redirect()->route('dashboard')->withErrors(['alert' => 'يرجى مراجعة التنبيهات الحمراء للمتابعة']);
        }
        return $next($request);
    }
}
