<div class="row g-3">

    {{-- Student Info --}}
    <div class="col-12">
        <div class="p-3 rounded-3 breakdown-section">
            <h6 class="fw-bold mb-3 breakdown-title">
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
        <div class="p-3 rounded-3 breakdown-section">
            <h6 class="fw-bold mb-3 breakdown-title">
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
                            <span class="badge-paid"><i class="fas fa-check-circle"></i> Paid</span>
                        @elseif($payment->status === 'processing')
                            <span class="badge-processing"><i class="fas fa-sync-alt"></i> Processing</span>
                        @elseif($payment->status === 'pending')
                            <span class="badge-pending"><i class="fas fa-clock"></i> Pending</span>
                        @else
                            <span class="badge-failed"><i class="fas fa-times-circle"></i> Failed</span>
                        @endif
                    </p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Semester</small>
                    <p class="fw-medium mb-1">{{ $payment->semester ? $payment->semester->name : 'N/A' }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">School Year</small>
                    <p class="fw-medium mb-1">{{ $payment->schoolYear ? $payment->schoolYear->name : 'N/A' }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Exam Period</small>
                    <p class="fw-medium mb-1">{{ $payment->examPeriod ? $payment->examPeriod->name : 'Not set' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Fees Breakdown --}}
    @if($payment->fees && $payment->fees->count())
    <div class="col-12">
        <div class="p-3 rounded-3 breakdown-section">
            <h6 class="fw-bold mb-3 breakdown-title">
                <i class="fas fa-list me-2"></i> Fees Breakdown
            </h6>
            <div class="table-responsive">
                <table class="table table-sm mb-0 breakdown-table">
                    <thead>
                        <tr>
                            <th class="border-0">Fee Name</th>
                            <th class="border-0">Type</th>
                            <th class="border-0 text-end">Amount</th>
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
                            <td colspan="2" class="fw-bold border-0">Total</td>
                            <td class="fw-bold border-0 text-end">₱{{ number_format($payment->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Proof of Payment --}}
    @if($payment->proof_of_payment)
    <div class="col-12">
        <div class="p-3 rounded-3 breakdown-section">
            <h6 class="fw-bold mb-3 breakdown-title">
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

<style>
    /* Dark mode compatible styles for the breakdown partial */
    .breakdown-section {
        background: rgba(15, 60, 145, 0.05);
        transition: background 0.3s ease;
    }
    body.dark .breakdown-section {
        background: rgba(59, 130, 246, 0.08);
    }

    .breakdown-title {
        color: #0f3c91;
    }
    body.dark .breakdown-title {
        color: #60a5fa;
    }

    .breakdown-table,
    .breakdown-table tbody,
    .breakdown-table tr,
    .breakdown-table td,
    .breakdown-table th {
        background-color: transparent !important;
        color: var(--text-primary);
    }
    .breakdown-table td, .breakdown-table th {
        border-color: var(--border-color) !important;
        color: var(--text-secondary);
    }
    .breakdown-table tfoot td {
        color: var(--text-primary);
    }
    .breakdown-table tfoot td:first-child {
        color: #0f3c91;
    }
    body.dark .breakdown-table tfoot td:first-child {
        color: #60a5fa;
    }

    /* Reuse existing badge styles (already dark‑friendly) */
    .badge-paid, .badge-pending, .badge-processing, .badge-failed {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.85rem;
    }
    .badge-paid       { background: rgba(76,175,80,0.15); color: #2e7d32; }
    .badge-pending    { background: rgba(244,180,20,0.15); color: #b26a00; }
    .badge-processing { background: rgba(13,110,253,0.15); color: #0a58ca; }
    .badge-failed     { background: rgba(220,53,69,0.15); color: #a71d2a; }
    body.dark .badge-paid       { background: rgba(76,175,80,0.25); color: #81c784; }
    body.dark .badge-pending    { background: rgba(244,180,20,0.25); color: #ffd54f; }
    body.dark .badge-processing { background: rgba(59,130,246,0.25); color: #93c5fd; }
    body.dark .badge-failed     { background: rgba(220,53,69,0.25); color: #ef9a9a; }

    .text-muted {
        color: var(--text-muted) !important;
    }
    .fw-medium {
        color: var(--text-primary);
    }
</style>