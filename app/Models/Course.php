<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'department',
    ];

    // ── Relationships ──────────────────────────────────────────────

    /**
     * A course has many students.
     * Assumes students.course_id FK (adjust if you store course as a string column).
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}