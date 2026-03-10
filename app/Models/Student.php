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
    // Get current semester and school year by is_current flag
    $currentSemester = \App\Models\Semester::where('is_current', true)->first();
    $currentSchoolYear = \App\Models\SchoolYear::where('is_current', true)->first();

    // If no active semester/school year is set, always return pending
    if (!$currentSemester || !$currentSchoolYear) {
        return [
            'status'    => 'pending',
            'total_paid' => 0,
            'required'  => 0,
            'remaining' => 0,
        ];
    }

    // Query fees using IDs, not string fields
    $requiredAmount = \App\Models\Fee::where('school_year_id', $currentSchoolYear->id)
        ->where('semester_id', $currentSemester->id)
        ->sum('amount');

    // If no fees exist yet for this semester, never show as cleared
    if ($requiredAmount <= 0) {
        return [
            'status'    => 'pending',
            'total_paid' => 0,
            'required'  => 0,
            'remaining' => 0,
        ];
    }

    // Get payments for fees in the current period only
    $fees = \App\Models\Fee::where('school_year_id', $currentSchoolYear->id)
        ->where('semester_id', $currentSemester->id)
        ->get();

    $totalPaid = 0;
    foreach ($fees as $fee) {
        $totalPaid += $fee->payments()
            ->where('student_id', $this->id)
            ->where('status', 'paid')
            ->sum('payments.total_amount');
    }

    return [
        'status'    => $totalPaid >= $requiredAmount ? 'cleared' : 'pending',
        'total_paid' => $totalPaid,
        'required'  => $requiredAmount,
        'remaining' => max($requiredAmount - $totalPaid, 0),
    ];
}

public function getClearanceStatusAttribute()
{
    return $this->getClearanceStatus()['status'];
}
}
