<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Payment;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_no',
        'course',
        'year_level',
        'contact',
        'semester',
    'school_year',
        'is_confirmed',
        'profile_picture',
        'last_profile_update',

        'last_picture_update',
    ];
    protected $casts = [
    'is_confirmed' => 'boolean',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function clearance()
    {
        return $this->hasOne(Clearance::class);
    }

    public function hasPaidFees()
    {
        return $this->payments()
            ->where('status', 'paid')
            ->exists();
    }

public function getClearanceStatus()
{
    $currentSemester = \App\Models\Semester::where('is_current', true)->first();
    $currentSchoolYear = \App\Models\SchoolYear::where('is_current', true)->first();

    if (!$currentSemester || !$currentSchoolYear) {
        return [
            'status'     => 'pending',
            'total_paid' => 0,
            'required'   => 0,
            'remaining'  => 0,
        ];
    }

    // Get only fees applicable to this student's course OR all courses (NULL)
    $applicableFees = \App\Models\Fee::where('school_year_id', $currentSchoolYear->id)
        ->where('semester_id', $currentSemester->id)
        ->where(function ($query) {
            $query->where('course', $this->course)
                  ->orWhereNull('course');
        })
        ->get();

    $requiredAmount = $applicableFees->sum('amount');

    if ($requiredAmount <= 0) {
        return [
            'status'     => 'pending',
            'total_paid' => 0,
            'required'   => 0,
            'remaining'  => 0,
        ];
    }

    $totalPaid = 0;
    foreach ($applicableFees as $fee) {
        $totalPaid += $fee->payments()
            ->where('student_id', $this->id)
            ->where('status', 'paid')
            ->sum('payments.total_amount');
    }

    $remaining = max($requiredAmount - $totalPaid, 0);

    return [
        'status'     => $totalPaid >= $requiredAmount ? 'cleared' : 'pending',
        'total_paid' => $totalPaid,
        'required'   => $requiredAmount,
        'remaining'  => $remaining,
    ];
}

public function getClearanceStatusAttribute()
{
    return $this->getClearanceStatus()['status'];
}

public function notifications()
{
    return $this->hasMany(\App\Models\Notification::class);
}
}
