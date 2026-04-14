<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    use HasFactory, Auditable;

    protected string $auditModule = 'Payment';

    protected $fillable = [
        'student_id',
        'total_amount',
        'status',
        'payment_method',
        'reference_no',
        'paymongo_source_id',
        'paymongo_payment_intent_id',
        'payment_date',
        'semester_id',
        'school_year_id',
        'exam_period_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($payment) {
            Log::info('Payment created', [
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
                'amount' => $payment->total_amount,
            ]);
        });

        static::updated(function ($payment) {
            if ($payment->isDirty('status') && $payment->status === 'paid') {
                Log::info('Payment completed', [
                    'payment_id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'reference_no' => $payment->reference_no,
                ]);
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function fees()
    {
        return $this->belongsToMany(Fee::class, 'fee_payment')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    public function examPeriod()
    {
        return $this->belongsTo(ExamPeriod::class);
    }
}