<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        //'password' => 'hashed',
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function isAdmin(): bool
{
    return in_array($this->role, ['admin', 'superadmin']);
}

    public function isStudent()
    {
        return $this->role === 'student';
    }
    // app/Models/User.php
public function notifications()
{
    return $this->hasMany(\App\Models\Notification::class);
}
}
