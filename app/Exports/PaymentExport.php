<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Payment::with('student.user', 'transaction');

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['from_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['from_date']);
        }

        if (isset($this->filters['to_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['to_date']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Payment ID',
            'Student Name',
            'Student No',
            'Amount',
                'Payment Method',
            'Status',
            'Reference No',
            'Payment Date',
            'Created At',
        ];
    }

   public function map($payment): array
{
    return [
        $payment->id,
        $payment->student->user->name ?? 'N/A',
        $payment->student->student_no ?? 'N/A',
        $payment->total_amount,
        $payment->payment_method ?? 'N/A', 
        ucfirst($payment->status),// Use transaction reference if available, otherwise fallback to payment referenc
        $payment->reference_no ?? 'N/A',        
        $payment->payment_date ? $payment->payment_date->format('Y-m-d H:i:s') : 'N/A',
        $payment->created_at->format('Y-m-d H:i:s'),
    ];
}
}