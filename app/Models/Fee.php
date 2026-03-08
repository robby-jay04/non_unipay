<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SchoolYear;
class Fee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'type', // 'tuition', 'miscellaneous', 'exam'
        'semester',
        'school_year',
        'semester_id',
        'school_year_id',
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
public function payments()
{
    return $this->belongsToMany(Payment::class, 'fee_payment')
                ->withPivot('amount')
                ->withTimestamps();
}
}
