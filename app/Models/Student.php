<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_no',
        'course',
        'year_level',
        'contact',
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

    public function getClearanceStatusAttribute()
{
    $requiredAmount = 58000; // full payment requirement

    $totalPaid = $this->payments()
        ->where('status', 'paid')
        ->sum('total_amount');

    return $totalPaid >= $requiredAmount ? 'cleared' : 'not cleared';
}
}
