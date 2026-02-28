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
    $requiredAmount = \App\Models\Fee::where('school_year', $this->school_year)
        ->where('semester', $this->semester)
        ->sum('amount');

    $totalPaid = $this->payments()
        ->where('status', 'paid')
        ->sum('total_amount');

    return [
        'status' => $totalPaid >= $requiredAmount ? 'cleared' : 'pending',
        'total_paid' => $totalPaid,
        'required' => $requiredAmount,
        'remaining' => max($requiredAmount - $totalPaid, 0),
    ];
}

public function getClearanceStatusAttribute()
{
    return $this->getClearanceStatus()['status'];
}
}
