<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'created_by',
        'title',
        'body',
        'priority',
        'audience',
        'audience_value',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Filter published announcements visible to a given student.
     * Matches 'all', or exact course/year_level value.
     */
    public function scopeVisibleTo($query, Student $student)
    {
        return $query->where('is_published', true)
            ->where(function ($q) use ($student) {
                $q->where('audience', 'all')
                  ->orWhere(function ($q2) use ($student) {
                      $q2->where('audience', 'course')
                         ->where('audience_value', $student->course);
                  })
                  ->orWhere(function ($q2) use ($student) {
                      $q2->where('audience', 'year_level')
                         ->where('audience_value', (string) $student->year_level);
                  });
            });
    }
}