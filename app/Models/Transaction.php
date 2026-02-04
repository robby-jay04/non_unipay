<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'reference_no',
        'gateway_response',
        'status', // 'initiated', 'completed', 'failed'
    ];

    protected $casts = [
        'gateway_response' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
