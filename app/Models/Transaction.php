<?php

namespace App\Models;

use App\Traits\Auditable;   // ✅ Add this
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Payment;

class Transaction extends Model
{
    use HasFactory, Auditable;   // ✅ Add Auditable trait

    protected string $auditModule = 'Transaction';   // ✅ Define module name for logs

    protected $fillable = [
        'payment_id',
        'transaction_id',
        'amount',
        'status',
        'payment_method',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}