<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use Auditable;  // HasFactory is not used here, but can be added if needed

    protected string $auditModule = 'SchoolYear';

    protected $fillable = ['name', 'is_current'];

    public static function current()
    {
        return self::where('is_current', true)->first();
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }
}