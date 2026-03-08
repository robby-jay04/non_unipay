@forelse($payments as $payment)
<tr id="payment-row-{{ $payment->id }}">
    <td class="px-4 py-3">{{ $payment->id }}</td>
    <td class="py-3">{{ $payment->student->user->name }}</td>
    <td class="py-3">₱{{ number_format($payment->total_amount, 2) }}</td>
    <td class="py-3">
        @if($payment->status == 'paid')
            <span class="badge-paid" id="status-badge-{{ $payment->id }}">Paid</span>
        @elseif($payment->status == 'processing')
            <span class="badge-pending" id="status-badge-{{ $payment->id }}">Processing</span>
        @elseif($payment->status == 'pending')
            <span class="badge-pending" id="status-badge-{{ $payment->id }}">Pending</span>
        @else
            <span class="badge-failed" id="status-badge-{{ $payment->id }}">Failed</span>
        @endif
    </td>
    <td class="py-3">{{ $payment->reference_no ?? 'N/A' }}</td>
    <td class="py-3">{{ $payment->created_at->format('M d, Y h:i A') }}</td>
    <td class="py-3 pe-4">
        <!-- View Button -->
        <button class="btn btn-sm rounded-pill px-3 viewPaymentBtn"
                style="background: rgba(15, 60, 145, 0.1); color: #0f3c91; border: none;"
                data-id="{{ $payment->id }}"
                data-bs-toggle="modal"
                data-bs-target="#viewPaymentModal">
            <i class="fas fa-eye me-1"></i> View
        </button>

        @if($payment->status === 'pending' || $payment->status === 'processing')
            <!-- Verify Button -->
            <button class="btn btn-sm rounded-pill px-3 verifyPaymentBtn"
                    style="background: rgba(40, 167, 69, 0.1); color: #28a745; border: none;"
                    data-id="{{ $payment->id }}"
                    id="verify-btn-{{ $payment->id }}">
                <i class="fas fa-check me-1"></i> Verify
            </button>

            <!-- Reject Button -->
            <button class="btn btn-sm rounded-pill px-3 rejectPaymentBtn"
                    style="background: rgba(220, 53, 69, 0.1); color: #dc3545; border: none;"
                    data-id="{{ $payment->id }}"
                    id="reject-btn-{{ $payment->id }}">
                <i class="fas fa-times me-1"></i> Reject
            </button>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-4 text-muted">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <p class="mb-0">No payments found</p>
    </td>
</tr>
@endforelse