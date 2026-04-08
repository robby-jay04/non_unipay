@forelse($payments as $payment)
<tr id="payment-row-{{ $payment->id }}" class="payment-row">
<td class="px-4 py-3 fw-semibold payment-id">
    #{{ $payment->id }}
</td>
    <td class="py-3">
        <div class="d-flex align-items-center gap-2">
            <div class="student-avatar rounded-circle d-flex align-items-center justify-content-center"
                 style="width: 38px; height: 38px; background: rgba(15,60,145,0.1); font-weight: 600; color: #0f3c91;">
                {{ strtoupper(substr($payment->student->user->name, 0, 1)) }}
            </div>
            <div>
                <span class="fw-medium d-block">{{ $payment->student->user->name }}</span>
                <small class="text-muted">{{ $payment->student->student_no }}</small>
            </div>
        </div>
    </td>
    <td class="py-3 fw-semibold" style="color: #0f3c91;">₱{{ number_format($payment->total_amount, 2) }}</td>
    <td class="py-3">
        @if($payment->status == 'paid')
            <span class="badge-paid" id="status-badge-{{ $payment->id }}">
                <i class="fas fa-check-circle"></i> Paid
            </span>
        @elseif($payment->status == 'processing')
            <span class="badge-processing" id="status-badge-{{ $payment->id }}">
                <i class="fas fa-sync-alt"></i> Processing
            </span>
        @elseif($payment->status == 'pending')
            <span class="badge-pending" id="status-badge-{{ $payment->id }}">
                <i class="fas fa-clock"></i> Pending
            </span>
        @else
            <span class="badge-failed" id="status-badge-{{ $payment->id }}">
                <i class="fas fa-times-circle"></i> Failed
            </span>
        @endif
    </td>
    <td class="py-3">
        <span class="text-muted small">{{ $payment->reference_no ?? '—' }}</span>
    </td>
    <td class="py-3">
        <span class="text-muted small">{{ $payment->created_at->format('M d, Y h:i A') }}</span>
    </td>
    <td class="py-3 pe-4">
        <div class="d-flex gap-2">
            <button class="btn-action viewPaymentBtn" title="View details"
                    data-id="{{ $payment->id }}"
                    data-bs-toggle="modal"
                    data-bs-target="#viewPaymentModal">
                <i class="fas fa-eye"></i>
            </button>
            @if($payment->status === 'pending' || $payment->status === 'processing')
                <button class="btn-action verifyPaymentBtn" title="Verify payment"
                        data-id="{{ $payment->id }}"
                        id="verify-btn-{{ $payment->id }}">
                    <i class="fas fa-check"></i>
                </button>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-inbox fa-4x" style="color: #d1d5db;"></i>
            <h6 class="fw-semibold mt-3" style="color: #1e293b;">No payments found</h6>
            <p class="text-muted small">When students make payments, they'll appear here.</p>
        </div>
    </td>
</tr>
@endforelse

