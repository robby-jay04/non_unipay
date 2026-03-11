<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'type',
        'semester',
        'school_year',
        'semester_id',
        'school_year_id',
        'course',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function scopeCurrentSchoolYear($query)
    {
        $current = SchoolYear::current();
        if (!$current) {
            return $query->whereNull('school_year');
        }
        return $query->where('school_year', $current->name);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCourse($query, $course)
    {
        return $query->where('course', $course);
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'fee_payment')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}