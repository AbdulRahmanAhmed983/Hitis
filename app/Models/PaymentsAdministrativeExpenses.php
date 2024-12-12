<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentsAdministrativeExpenses extends Model
{
    use HasFactory;
    protected $table = 'payments_administrative_expenses';
    protected $primaryKey = 'student_code';
    public $incrementing = false;
    protected $fillable = ['ticket_id','student_code'];
}
