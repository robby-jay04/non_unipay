<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = ['school_year_id', 'name', 'is_current'];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }
   public function examPeriods()
{
    return $this->hasMany(ExamPeriod::class);
}
}