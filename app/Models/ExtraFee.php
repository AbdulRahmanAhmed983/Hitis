<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraFee extends Model
{
    use HasFactory;

    public $table = 'extra_fees';
    protected $failable = ['id','name_fees','amount','active'];

}
