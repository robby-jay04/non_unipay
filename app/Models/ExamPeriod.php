<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPeriod extends Model
{
    use HasFactory, Auditable;

    protected string $auditModule = 'ExamPeriod';

    protected $fillable = [
        'name',
        'semester_id',
        'is_current',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}