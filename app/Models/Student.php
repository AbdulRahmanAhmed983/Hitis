<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'name', 'national_id', 'issuer_national_number', 'certificate_obtained', 'certificate_obtained_date','status_graduated',
        'nationality', 'birth_date', 'birth_province', 'birth_country', 'gender', 'religion', 'address',
        'landline_phone', 'mobile', 'father_profession', 'Parents_phone1', 'Parents_phone2', 'student_classification',
        'classification_notes', 'study_group','departments_id', 'specialization', 'academic_advisor', 'studying_status',
        'immigrant_student', 'email', 'notes', 'username', 'password', 'military_number', 'recruitment_area',
        'recruitment_notes', 'photo', 'updated_by', 'certificate_degree', 'certificate_degree_percentage',
        'certificate_degree_total', 'english_degree', 'military_education', 'total_hours', 'earned_hours', 'cgpa',
        'registration_date', 'certificate_seating_number', 'created_by', 'apply_classification',
        'apply_classification_notes', 'enlistment_status', 'position_of_recruitment', 'decision_number', 'decision_date',
        'expiry_date'
    ];

    public function getBirthDateAttribute($value)
    {
        return date('d/m/Y', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y h:i:sa', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d/m/Y h:i:sa', strtotime($value));
    }
}
