<div>
    <p><strong>Payment ID:</strong> {{ $payment->id }}</p>
    <p><strong>Student:</strong> {{ $payment->student->user->name }}</p>
    <p><strong>Amount:</strong> ₱{{ number_format($payment->total_amount, 2) }}</p>
    <p><strong>Status:</strong> 
        <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
            {{ ucfirst($payment->status) }}
        </span>
    </p>
    <p><strong>Reference No:</strong> {{ $payment->reference_no }}</p>
    <p><strong>Date:</strong> {{ $payment->created_at->format('M d, Y') }}</p>
    <p><strong>Details:</strong> {{ $payment->details ?? 'N/A' }}</p>
</div>
