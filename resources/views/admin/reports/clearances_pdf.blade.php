<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Clearance Report</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', sans-serif;
            margin: 1.5cm;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        /* Header area */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0f3c91;
            padding-bottom: 15px;
        }

        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 24px;
            font-weight: bold;
            color: #0f3c91;
            margin-bottom: 5px;
        }

        .report-subtitle {
            font-size: 12px;
            color: #555;
        }

        /* Info section */
        .info-section {
            background: #f8fafc;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            border-left: 4px solid #0f3c91;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #0f3c91;
        }

        .info-value {
            color: #555;
        }

       

        /* Table styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 10px;
        }

        .data-table th {
            background: #0f3c91;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #1e4a7a;
        }

        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .badge-cleared {
            color: #2e7d32;
            font-weight: 600;
            display: inline-block;
            padding: 2px 8px;
            background: #e8f5e9;
            border-radius: 20px;
            font-size: 9px;
        }

        /* Footer */
        .footer {
            margin-top: 35px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }

        .page-number:after {
            content: counter(page);
        }

        @page {
            margin: 1.5cm;
            @bottom-center {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 9px;
                color: #94a3b8;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        @if(file_exists(public_path('assets/logo.png')))
            <img src="{{ public_path('assets/logo.png') }}" class="logo" alt="Logo">
        @endif
        <div class="report-title">Student Clearance Report</div>
        <div class="report-subtitle">Official record of cleared students</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Generated on:</span>
            <span class="info-value">{{ $generated_at }}</span>
        </div>
        @if($currentSemester)
        <div class="info-row">
            <span class="info-label">Current Period:</span>
            <span class="info-value">{{ $currentSemester->name }} – {{ $currentSemester->schoolYear->name ?? 'N/A' }}</span>
        </div>
        @endif
        @if($search)
        <div class="info-row">
            <span class="info-label">Search filter:</span>
            <span class="info-value">"{{ $search }}"</span>
        </div>
        @endif
    </div>

   

    <table class="data-table">
        <thead>
            <tr>
                <th>Student No.</th>
                <th>Student Name</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Semester</th>
                <th>School Year</th>
                <th>Status</th>
                <th>Last Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clearances as $student)
            <tr>
                <td>{{ $student->student_no ?? '—' }}</td>
                <td>{{ $student->user->name }}</td>
                <td>{{ $student->course ?? '—' }}</td>
                <td>{{ $student->year_level ? 'Year ' . $student->year_level : '—' }}</td>
                <td>{{ $currentSemester->name ?? '—' }}</td>
                <td>{{ $currentSemester->schoolYear->name ?? '—' }}</td>
                <td><span class="badge-cleared">Cleared</span></td>
                <td>
                    @php
                        $lastPayment = $student->payments()
                                    ->where('status', 'paid')
                                    ->latest('payment_date')
                                    ->first();
                    @endphp
                    @if($lastPayment && $lastPayment->payment_date)
                        {{ \Carbon\Carbon::parse($lastPayment->payment_date)->format('M d, Y') }}
                    @else
                        {{ $student->updated_at->format('M d, Y') }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px;">No cleared students found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        This is a system-generated report. For inquiries, please contact the Registrar's Office.
        <div class="page-number"></div>
    </div>
</body>
</html>