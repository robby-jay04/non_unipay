<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
        }

        /* ── Header ── */
        .header {
            background: #0f3c91;
            color: white;
            padding: 20px 30px;
            margin-bottom: 20px;
        }
        .header-top {
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 12px;
            margin-bottom: 12px;
            display: table;
            width: 100%;
        }
        .header-top-left  { display: table-cell; vertical-align: middle; }
        .header-top-right { display: table-cell; vertical-align: middle; text-align: right; }
        .header-bottom { display: table; width: 100%; }
        .header-bottom-left  { display: table-cell; vertical-align: bottom; }
        .header-bottom-right { display: table-cell; vertical-align: bottom; text-align: right; font-size: 9px; color: rgba(255,255,255,0.8); }
        .header-bottom-right span { display: block; margin-bottom: 2px; }
        .header h1 { font-size: 20px; font-weight: bold; letter-spacing: 1px; }
        .header .subtitle { font-size: 11px; color: rgba(255,255,255,0.75); margin-top: 2px; }
        .org-name { font-size: 13px; font-weight: bold; letter-spacing: 2px; }
        .org-sub  { font-size: 9px; color: rgba(255,255,255,0.7); margin-top: 2px; }
        .ref      { font-size: 9px; color: rgba(255,255,255,0.7); margin-top: 2px; }

        /* ── Summary Cards ── */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: #f4f7fe;
            border: 1px solid #dce3f5;
            border-radius: 6px;
            padding: 12px 16px;
            text-align: center;
            vertical-align: top;
            width: 25%;
        }
        .card-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-bottom: 4px; }
        .card-value { font-size: 16px; font-weight: bold; color: #0f3c91; }
        .card-value.green  { color: #16a34a; }
        .card-value.orange { color: #d97706; }
        .card-value.red    { color: #dc2626; }

        /* ── Date Group Header ── */
        .date-group-header {
            background: #e8eef9;
            border-left: 4px solid #0f3c91;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: bold;
            color: #0f3c91;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0;
        }
        .date-group-subtotal {
            background: #f0f4ff;
            font-size: 9.5px;
            color: #374151;
            padding: 4px 10px;
            text-align: right;
            border-bottom: 1px solid #dce3f5;
            margin-bottom: 12px;
        }

        /* ── Section Title ── */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f3c91;
            border-bottom: 2px solid #0f3c91;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        /* ── Table ── */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .main-table thead tr { background: #0f3c91; color: white; }
        .main-table thead th {
            padding: 9px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }
        .main-table tbody tr:nth-child(even) { background: #f4f7fe; }
        .main-table tbody tr:nth-child(odd)  { background: #ffffff; }
        .main-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .main-table tbody tr:last-child td { border-bottom: 1px solid #dce3f5; }

        /* ── Day Subtotal Row ── */
        .day-subtotal-row td {
            background: #e8eef9 !important;
            color: #0f3c91 !important;
            font-weight: bold;
            font-size: 10px;
            padding: 6px 10px;
            border-top: 1px solid #dce3f5 !important;
            border-bottom: 2px solid #0f3c91 !important;
        }

        /* ── Status Pills ── */
        .status-pill {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-approved { background: #dcfce7; color: #15803d; }
        .status-pending  { background: #fef9c3; color: #92400e; }
        .status-rejected { background: #fee2e2; color: #b91c1c; }
        .status-failed   { background: #fee2e2; color: #b91c1c; }

        /* ── Grand Total Row ── */
        .total-row td {
            background: #0f3c91 !important;
            color: white !important;
            font-weight: bold;
            font-size: 11px;
            padding: 9px 10px;
            border: none !important;
        }

        /* ── Signature ── */
        .signature-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .signature-table td { width: 33%; text-align: center; padding: 0 10px; vertical-align: bottom; }
        .sig-line { border-top: 1px solid #374151; margin-top: 30px; padding-top: 4px; font-size: 10px; font-weight: bold; }
        .sig-role { font-size: 9px; color: #6b7280; }

        /* ── Footer ── */
        .footer {
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
        .footer strong { color: #0f3c91; }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="header-top">
            <div class="header-top-left">
                <div class="org-name">UNIPAY</div>
                <div class="org-sub">Payment Management System</div>
            </div>
            <div class="header-top-right">
                <div style="font-size:9px; color:rgba(255,255,255,0.7);">OFFICIAL DOCUMENT</div>
                <div class="ref">Ref #: RPT-{{ now()->format('Ymd-His') }}</div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="header-bottom-left">
                <h1>Payment Report</h1>
                <div class="subtitle">Summary of student payment transactions</div>
            </div>
            <div class="header-bottom-right">
                <span>Generated: {{ now()->format('F d, Y') }}</span>
                <span>Time: {{ now()->format('h:i A') }}</span>
                <span>By: {{ auth()->user()->name ?? 'Administrator' }}</span>
            </div>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    @php
        $total    = $payments->sum('total_amount');
        $approved = $payments->whereIn('status', ['approved', 'paid'])->sum('total_amount');
        $pending  = $payments->where('status', 'pending')->count();
        $failed   = $payments->whereIn('status', ['failed', 'rejected'])->count();

        // Group by payment_date day, fallback to created_at
        $groupedPayments = $payments->groupBy(function ($payment) {
            $date = $payment->payment_date ?? $payment->created_at;
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        })->sortKeys();
    @endphp

    <table class="summary-table">
        <tr>
            <td class="summary-card">
                <div class="card-label">Total Transactions</div>
                <div class="card-value">{{ $payments->count() }}</div>
            </td>
            <td class="summary-card">
                <div class="card-label">Total Amount</div>
                <div class="card-value">₱{{ number_format($total, 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="card-label">Total Paid</div>
                <div class="card-value green">₱{{ number_format($approved, 2) }}</div>
            </td>
            <td class="summary-card">
                <div class="card-label">Pending / Failed</div>
                <div class="card-value orange">{{ $pending + $failed }}</div>
            </td>
        </tr>
    </table>

    {{-- ── TRANSACTIONS GROUPED BY DAY ── --}}
    <div class="section-title">Transaction Details — Sorted by Payment Date</div>

    @php $grandTotal = 0; $rowCounter = 0; @endphp

    @foreach($groupedPayments as $date => $dayPayments)
        @php
            $dayTotal      = $dayPayments->sum('total_amount');
            $grandTotal   += $dayTotal;
            $formattedDate = \Carbon\Carbon::parse($date)->format('F d, Y - l');
        @endphp

        {{-- Day header --}}
        <div class="date-group-header">
            📅 {{ $formattedDate }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            {{ $dayPayments->count() }} transaction(s)
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th style="width:24%;">Student Name</th>
                    <th style="width:13%;">Student No.</th>
                    <th style="width:13%;">Course</th>
                    <th style="width:8%; text-align:center;">Year</th>
                    <th style="width:12%; text-align:right;">Amount</th>
                    <th style="width:10%; text-align:center;">Status</th>
                    <th style="width:16%;">Payment Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dayPayments as $payment)
                    @php
                        $rowCounter++;
                        $student     = $payment->student ?? null;
                        $name        = optional($student?->user)->name ?? 'N/A';
                        $stuNo       = $student->student_no  ?? '—';
                        $course      = $student->course      ?? '—';
                        $year        = $student->year_level  ?? '—';
                        $statusClass = match(strtolower($payment->status)) {
                            'approved', 'paid' => 'status-approved',
                            'pending'          => 'status-pending',
                            'rejected',
                            'failed'           => 'status-failed',
                            default            => 'status-pending',
                        };
                    @endphp
                    <tr>
                        <td style="text-align:center; color:#6b7280;">{{ $rowCounter }}</td>
                        <td><strong>{{ $name }}</strong></td>
                        <td>{{ $stuNo }}</td>
                        <td>{{ $course }}</td>
                        <td style="text-align:center;">{{ $year }}</td>
                        <td style="text-align:right; font-weight:bold;">₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td style="text-align:center;">
                            <span class="status-pill {{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                        </td>
                        <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                    </tr>
                @endforeach

                {{-- Day subtotal row --}}
                <tr class="day-subtotal-row">
                    <td colspan="5" style="text-align:right;">
                        Daily Total ({{ $dayPayments->count() }} transaction(s))
                    </td>
                    <td style="text-align:right;">₱{{ number_format($dayTotal, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tbody>
        </table>

        <br>
    @endforeach

    {{-- ── GRAND TOTAL ── --}}
    <table class="main-table">
        <tbody>
            <tr class="total-row">
                <td style="width:4%;"></td>
                <td colspan="4" style="text-align:right;">GRAND TOTAL — {{ $payments->count() }} Transactions</td>
                <td style="text-align:right;">₱{{ number_format($grandTotal, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    {{-- ── SIGNATURE ── --}}
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-line">Prepared by</div>
                <div class="sig-role">{{ auth()->user()->name ?? 'Administrator' }}</div>
            </td>
            <td>
                <div class="sig-line">Checked by</div>
                <div class="sig-role">Authorized Personnel</div>
            </td>
            <td>
                <div class="sig-line">Approved by</div>
                <div class="sig-role">School Administrator</div>
            </td>
        </tr>
    </table>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        This document is system-generated by <strong>UniPay</strong> &mdash; {{ now()->format('F d, Y \a\t h:i A') }}.
        This report is for official use only.
    </div>

</body>
</html>