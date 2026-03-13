<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'semester_id',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    /**
     * Get the semester that owns the exam period.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Scope a query to only include the current exam period.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}