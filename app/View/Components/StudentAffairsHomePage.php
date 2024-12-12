<?php

namespace App\View\Components;

use App\Models\Student;
use Illuminate\View\Component;

class StudentAffairsHomePage extends Component
{
    public $student_age_28;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->student_age_28 = $this->getMilitaryAge28();
    }

    public function getMilitaryAge28(): array
    {
        return Student::select(['name', 'username', 'national_id', 'birth_date', 'enlistment_status',
            'position_of_recruitment', 'expiry_date', 'recruitment_notes'])
            ->where('nationality', 'مصري')->where('gender', 'ذكر')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('enlistment_status', 'له حق التأجيل')
                        ->whereRaw('DATEDIFF(DATE_ADD(birth_date, INTERVAL 28 YEAR),?) <= ?', [date('Y-m-d'), 10]);
//                        ->whereRaw('DATEDIFF(DATE_ADD(birth_date, INTERVAL 28 YEAR),?) >= 0', [date('Y-m-d')]);
                })->orWhere(function ($q) {
                    $q->where('enlistment_status', 'اعفاء مؤقت')
                        ->whereRaw('DATEDIFF(expiry_date,?) <= ?', [date('Y-m-d'), 10]);
//                            ->whereRaw('DATEDIFF(expiry_date,?) >= 0', [date('Y-m-d')]);
                });
            })->orderBy('expiry_date')
            ->get()->groupBy('enlistment_status')->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.student-affairs-home-page');
    }
}
