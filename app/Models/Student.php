<?php

namespace App\Models;

use App\Traits\Auditable;   // ✅ Add this
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Payment;

class Student extends Model
{
    use HasFactory, Auditable;   // ✅ Add Auditable trait

    protected string $auditModule = 'Student';   // ✅ Define module name for logs

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
        'clearance_status', // ✅ added
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

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }
}