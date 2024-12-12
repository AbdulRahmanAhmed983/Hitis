<?php

namespace App\View\Components;

use App\Http\Traits\StudentTrait;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class StudentHomePage extends Component
{
    use StudentTrait;

    public $student;
    public $payment;
    public $guidance;
    public $alerts;
    public $old_payment;
    public $pay;
    public $administrativeExpenses;
    public $wallet;
    public $section;
    public $exam_table;
    public $exam_place;
    public $seat_number;
    public $current_warning;
    public $warning_threshold;
    public $advisor;
    public $payment_used;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->student = $this->getStudentInfo(auth()->id());
        $this->guidance = $this->checkGuidance(auth()->id());
        $this->payment = $this->checkPayment(auth()->id());
        $this->alerts = $this->getAlerts(auth()->id());
        $this->old_payment = ($this->oldPaymentExists(auth()->id()) or $this->ticketExists(auth()->id()) or
        $this->oldPaymentExists(auth()->id(), true) or $this->checkTotalPayment(auth()->id())[0]
         or $this->checkTotalAdministrativeExpenses(auth()->id())[0]);
        $this->pay = $this->getTotalPayment(auth()->id()) + $this->checkTotalPayment(auth()->id())[1];
        $this->administrativeExpenses = $this->checkTotalAdministrativeExpenses(auth()->id())[0];
        //$amount = $this->checkTotalAdministrativeExpenses(auth()->id());
        //dd($amount);
        $this->payment_used = $this->checkUsedAdministrativeExpenses(auth()->id());

        $this->wallet = $this->getStudentWallet(auth()->id());
        $this->section = $this->getStudentSectionNumber(auth()->id(), $this->getCurrentYear(),
        $this->getCurrentSemester());
        $this->exam_table = $this->getStudentExamTable(auth()->id());
        $this->exam_place = $this->getStudentExamPlace(auth()->id());
        $this->seat_number = $this->getSeatingNumber(auth()->id());
        $this->current_warning = $this->getCurrentWarning(auth()->id());
        $this->warning_threshold = $this->getData(['warning_threshold'])['warning_threshold'][0];
        $this->advisor = $this->getAcademicAdvisor($this->student['academic_advisor']);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.student-home-page');
    }
}
