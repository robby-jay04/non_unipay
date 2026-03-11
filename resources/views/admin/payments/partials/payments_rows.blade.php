@forelse($payments as $payment)
<tr id="payment-row-{{ $payment->id }}" class="payment-row">
    <td class="px-4 py-3 fw-semibold" style="color: #1e293b;">#{{ $payment->id }}</td>
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
                <i class="fas fa-check-circle me-1"></i> Paid
            </span>
        @elseif($payment->status == 'processing')
            <span class="badge-processing" id="status-badge-{{ $payment->id }}">
                <i class="fas fa-sync-alt me-1"></i> Processing
            </span>
        @elseif($payment->status == 'pending')
            <span class="badge-pending" id="status-badge-{{ $payment->id }}">
                <i class="fas fa-clock me-1"></i> Pending
            </span>
        @else
            <span class="badge-failed" id="status-badge-{{ $payment->id }}">
                <i class="fas fa-times-circle me-1"></i> Failed
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
            <!-- View Button -->
            <button class="btn-action viewPaymentBtn" title="View details"
                    data-id="{{ $payment->id }}"
                    data-bs-toggle="modal"
                    data-bs-target="#viewPaymentModal">
                <i class="fas fa-eye"></i>
            </button>

            @if($payment->status === 'pending' || $payment->status === 'processing')
                <!-- Verify Button -->
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
            <p class="text-muted small">When students make payments, they’ll appear here.</p>
        </div>
    </td>
</tr>
@endforelse
@push('styles')
<style>
/* Payment rows */
.payment-row {
    transition: all 0.2s ease;
}
.payment-row:hover {
    background-color: rgba(15, 60, 145, 0.02) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.02);
}

/* Student avatar */
.student-avatar {
    transition: all 0.2s;
}
.payment-row:hover .student-avatar {
    background: rgba(15,60,145,0.15) !important;
    transform: scale(1.02);
}

/* Action buttons */
.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    cursor: pointer;
    background: transparent;
    color: #64748b;
}
.btn-action:hover {
    background: rgba(15,60,145,0.1);
    color: #0f3c91;
    transform: scale(1.1);
}
.btn-action.verifyPaymentBtn:hover {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}
.btn-action.rejectPaymentBtn:hover {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

/* Empty state */
.empty-state {
    padding: 2rem;
}
.empty-state i {
    opacity: 0.7;
}
.empty-state h6 {
    font-size: 1.1rem;
}
.empty-state p {
    font-size: 0.9rem;
    max-width: 300px;
    margin: 0 auto;
}

/* Refined badges */
.badge-paid {
    background: rgba(76, 175, 80, 0.15);
    color: #2e7d32;
    font-weight: 600;
    padding: 0.45rem 1rem;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    font-size: 0.85rem;
}
.badge-pending {
    background: rgba(244, 180, 20, 0.15);
    color: #b26a00;
    font-weight: 600;
    padding: 0.45rem 1rem;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    font-size: 0.85rem;
}
.badge-processing {
    background: rgba(13, 110, 253, 0.15);
    color: #0a58ca;
    font-weight: 600;
    padding: 0.45rem 1rem;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    font-size: 0.85rem;
}
.badge-failed {
    background: rgba(220, 53, 69, 0.15);
    color: #a71d2a;
    font-weight: 600;
    padding: 0.45rem 1rem;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    font-size: 0.85rem;
}
</style>
@endpush