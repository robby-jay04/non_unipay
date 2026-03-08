<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_id',
        'status'
    ];
}
