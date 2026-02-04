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
        'type', // 'tuition', 'miscellaneous', 'exam'
        'semester',
        'school_year',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function scopeCurrentSchoolYear($query)
    {
        return $query->where('school_year', config('app.current_school_year'));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
