<div class="row g-3">

    {{-- Student Info --}}
    <div class="col-12">
        <div class="p-3 rounded-3" style="background: rgba(15, 60, 145, 0.05);">
            <h6 class="fw-bold mb-3" style="color: #0f3c91;">
                <i class="fas fa-user-graduate me-2"></i> Student Information
            </h6>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Name</small>
                    <p class="fw-medium mb-1">{{ $payment->student->user->name }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Student No</small>
                    <p class="fw-medium mb-1">{{ $payment->student->student_no }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Course</small>
                    <p class="fw-medium mb-0">{{ $payment->student->course }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Year Level</small>
                    <p class="fw-medium mb-0">{{ $payment->student->year_level }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Info --}}
    <div class="col-12">
        <div class="p-3 rounded-3" style="background: rgba(15, 60, 145, 0.05);">
            <h6 class="fw-bold mb-3" style="color: #0f3c91;">
                <i class="fas fa-receipt me-2"></i> Payment Information
            </h6>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Reference No</small>
                    <p class="fw-medium mb-1">{{ $payment->reference_no ?? 'N/A' }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Amount</small>
                    <p class="fw-medium mb-1">₱{{ number_format($payment->total_amount, 2) }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Method</small>
                    <p class="fw-medium mb-1">{{ ucfirst($payment->payment_method ?? 'N/A') }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Date</small>
                    <p class="fw-medium mb-1">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Status</small>
                    <p class="mb-0">
                        @if($payment->status === 'paid')
                            <span class="badge-paid">
                                <i class="fas fa-check-circle"></i> Paid
                            </span>
                        @elseif($payment->status === 'processing')
                            <span class="badge-processing">
                                <i class="fas fa-sync-alt"></i> Processing
                            </span>
                        @elseif($payment->status === 'pending')
                            <span class="badge-pending">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                        @else
                            <span class="badge-failed">
                                <i class="fas fa-times-circle"></i> Failed
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Fees Breakdown --}}
    @if($payment->fees && $payment->fees->count())
    <div class="col-12">
        <div class="p-3 rounded-3" style="background: rgba(15, 60, 145, 0.05);">
            <h6 class="fw-bold mb-3" style="color: #0f3c91;">
                <i class="fas fa-list me-2"></i> Fees Breakdown
            </h6>
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th class="border-0" style="color: #475569;">Fee Name</th>
                        <th class="border-0" style="color: #475569;">Type</th>
                        <th class="border-0 text-end" style="color: #475569;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payment->fees as $fee)
                    <tr>
                        <td class="border-0">{{ $fee->name }}</td>
                        <td class="border-0">{{ ucfirst($fee->type) }}</td>
                        <td class="border-0 text-end">₱{{ number_format($fee->pivot->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="fw-bold border-0" style="color: #0f3c91;">Total</td>
                        <td class="fw-bold border-0 text-end" style="color: #0f3c91;">
                            ₱{{ number_format($payment->total_amount, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    {{-- Proof of Payment --}}
    @if($payment->proof_of_payment)
    <div class="col-12">
        <div class="p-3 rounded-3" style="background: rgba(15, 60, 145, 0.05);">
            <h6 class="fw-bold mb-3" style="color: #0f3c91;">
                <i class="fas fa-image me-2"></i> Proof of Payment
            </h6>
            <img src="{{ asset('storage/' . $payment->proof_of_payment) }}"
                 alt="Proof of Payment"
                 class="img-fluid rounded-3"
                 style="max-height: 300px; object-fit: contain;">
        </div>
    </div>
    @endif

</div>