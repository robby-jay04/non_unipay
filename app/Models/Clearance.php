<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Clearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'status', // 'cleared', 'pending'
        'exam_period',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeCleared($query)
    {
        return $query->where('status', 'cleared');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
