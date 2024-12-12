<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartID extends Model
{
    use HasFactory;
    protected $table = 'smart_id';
    protected $fillable = ['student_code','card_code','created_at','updated_at'];
    public $timestamps = true;
    protected $primaryKey = 'student_code';
    public $incrementing = false;
}
