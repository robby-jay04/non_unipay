<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PaymentExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Payment Report';
    }

   public function collection()
{
    $query = Payment::with('student.user', 'transaction')
        ->whereNotNull('payment_date')
        ->orderBy('payment_date', 'asc');

    if (!empty($this->filters['status'])) {
        $query->where('status', $this->filters['status']);
    }

    if (!empty($this->filters['from_date'])) {
        $query->whereDate('payment_date', '>=', $this->filters['from_date']);
    }

    if (!empty($this->filters['to_date'])) {
        $query->whereDate('payment_date', '<=', $this->filters['to_date']);
    }

    return $query->get();
}
    public function headings(): array
    {
        return [
            'Payment ID',
            'Student Name',
            'Student No',
            'Amount (₱)',
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
            $payment->student->user->name    ?? 'N/A',
            $payment->student->student_no    ?? 'N/A',
            number_format($payment->total_amount, 2),
            ucfirst($payment->payment_method ?? 'N/A'),
            ucfirst($payment->status),
            $payment->reference_no           ?? 'N/A',
            $payment->payment_date
                ? $payment->payment_date->format('Y-m-d H:i:s')
                : 'N/A',
            $payment->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Payment ID
            'B' => 28,  // Student Name
            'C' => 16,  // Student No
            'D' => 15,  // Amount
            'E' => 18,  // Payment Method
            'F' => 12,  // Status
            'G' => 24,  // Reference No
            'H' => 22,  // Payment Date
            'I' => 22,  // Created At
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header row styling
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F3C91'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Data rows — zebra striping
        for ($row = 2; $row <= $lastRow; $row++) {
            $color = ($row % 2 === 0) ? 'F0F4FF' : 'FFFFFF';
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $color],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // Status column — color code Paid / Failed
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = strtolower($sheet->getCell("F{$row}")->getValue());
            if ($status === 'paid') {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => ['color' => ['rgb' => '2E7D32'], 'bold' => true],
                ]);
            } elseif ($status === 'failed') {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => ['color' => ['rgb' => 'C62828'], 'bold' => true],
                ]);
            } elseif ($status === 'pending') {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => ['color' => ['rgb' => 'E65100'], 'bold' => true],
                ]);
            }
        }

        // Border around entire table
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Freeze the header row
        $sheet->freezePane('A2');

        // Center-align ID, Amount, Method, Status columns
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("E2:F{$lastRow}")->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Row height for header
        $sheet->getRowDimension(1)->setRowHeight(24);

        return [];
    }
}