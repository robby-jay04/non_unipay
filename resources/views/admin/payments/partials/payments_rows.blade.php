
@forelse($payments as $payment)
<tr id="payment-row-{{ $payment->id }}">
    <td>{{ $payment->id }}</td>
    <td>{{ $payment->student->user->name }}</td>
    <td>₱{{ number_format($payment->total_amount, 2) }}</td>
    <td>
        <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'processing' ? 'info' : ($payment->status == 'pending' ? 'warning' : 'danger')) }}" 
              id="status-badge-{{ $payment->id }}">
            {{ ucfirst($payment->status) }}
        </span>
    </td>
    <td>{{ $payment->reference_no ?? 'N/A' }}</td>
    <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
    <td>
        <button class="btn btn-sm btn-info viewPaymentBtn" 
                data-id="{{ $payment->id }}" 
                data-bs-toggle="modal" 
                data-bs-target="#viewPaymentModal">
            <i class="fas fa-eye"></i> View
        </button>
        
        @if($payment->status === 'pending' || $payment->status === 'processing')
        <button class="btn btn-sm btn-success verifyPaymentBtn" 
                data-id="{{ $payment->id }}"
                id="verify-btn-{{ $payment->id }}">
            <i class="fas fa-check"></i> Verify
        </button>
        <button class="btn btn-sm btn-danger rejectPaymentBtn" 
                data-id="{{ $payment->id }}"
                id="reject-btn-{{ $payment->id }}">
            <i class="fas fa-times"></i> Reject
        </button>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-4">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <p class="text-muted">No payments found</p>
    </td>
</tr>
@endforelse