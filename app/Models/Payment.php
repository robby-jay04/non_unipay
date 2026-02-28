<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log; // ✅ Add this import

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'total_amount',
        'status',
        'payment_method',
        'reference_no',
        'paymongo_source_id',
        'paymongo_payment_intent_id',
        'payment_date',
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
}