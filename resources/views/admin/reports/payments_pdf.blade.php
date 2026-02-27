<!DOCTYPE html>
<html>
<head>
    <title>Payment Report</title>
    <style>
        body { font-family: DejaVu Sans; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h2>Payment Report</h2>
    <p>Date Generated: {{ now()->format('M d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                <td>{{ $payment->student->user->name }}</td>
                <td>₱{{ number_format($payment->total_amount, 2) }}</td>
                <td>{{ ucfirst($payment->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>