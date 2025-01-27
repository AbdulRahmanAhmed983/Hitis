<?php

namespace App\Models;  ;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registeration extends Model
{
    use HasFactory;

    protected $table = 'registration';
    // protected $primaryKey = 'student_code';
    public $incrementing = false;
    protected $fillable = ['student_code','course_code','year','semester','yearly_performance_score','written',
    'grade','note'];
}
