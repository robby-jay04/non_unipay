@extends('admin.layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Reports & Analytics</h2>
</div>

<!-- Report Cards -->
<div class="row g-4 mb-5">
    <!-- Payment Reports Card -->
    <div class="col-md-6">
        <div class="card report-card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="report-icon rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 52px; height: 52px; background: rgba(15, 60, 145, 0.1);">
                        <i class="fas fa-file-invoice-dollar" style="color: #0f3c91; font-size: 26px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #0f3c91;">Payment Reports</h5>
                        <small class="text-muted">PDF & Excel exports</small>
                    </div>
                </div>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">
                    Generate comprehensive payment reports with detailed transaction history.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.reports.pdf') }}" class="btn-export btn-pdf rounded-pill px-4 py-2">
                        <i class="fas fa-file-pdf me-2"></i> Export PDF
                    </a>
                    <a href="{{ route('admin.reports.excel') }}" class="btn-export btn-excel rounded-pill px-4 py-2">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clearance Reports Card -->
    <div class="col-md-6">
        <div class="card report-card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="report-icon rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 52px; height: 52px; background: rgba(244, 180, 20, 0.1);">
                        <i class="fas fa-clipboard-check" style="color: rgb(244, 180, 20); font-size: 26px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" style="color: #0f3c91;">Clearance Reports</h5>
                        <small class="text-muted">Student clearance status</small>
                    </div>
                </div>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">
                    View and export detailed student clearance status for exam eligibility.
                </p>
                <a href="{{ route('admin.reports.clearances') }}" class="btn-view rounded-pill px-4 py-2">
                    <i class="fas fa-eye me-2"></i> View Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">
            <i class="fas fa-history me-2" style="font-size: 1.1rem;"></i>Recent Transactions
        </h5>
        <span class="text-muted small">Last 10 payments</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Date</th>
                        <th class="py-3">Student</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3 pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr class="transaction-row">
                        <td class="px-4 py-3">
                            <span class="fw-medium" style="color: #1e293b;">{{ $payment->created_at->format('M d, Y') }}</span>
                            <small class="d-block text-muted">{{ $payment->created_at->format('h:i A') }}</small>
                        </td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="student-avatar rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 38px; height: 38px; background: rgba(15,60,145,0.1); font-weight: 600; color: #0f3c91;">
                                    {{ strtoupper(substr($payment->student->user->name, 0, 1)) }}
                                </div>
                                <span class="fw-medium">{{ $payment->student->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 fw-semibold" style="color: #0f3c91;">₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td class="py-3 pe-4">
                            @if($payment->status == 'paid')
                                <span class="badge-paid">
                                    <i class="fas fa-check-circle me-1"></i> Paid
                                </span>
                            @elseif($payment->status == 'pending')
                                <span class="badge-pending">
                                    <i class="fas fa-clock me-1"></i> Pending
                                </span>
                            @else
                                <span class="badge-failed">
                                    <i class="fas fa-times-circle me-1"></i> Failed
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-credit-card fa-4x" style="color: #d1d5db;"></i>
                                <h6 class="fw-semibold mt-3" style="color: #1e293b;">No transactions found</h6>
                                <p class="text-muted small">When students make payments, they’ll appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="d-flex justify-content-center py-4">
            {{ $payments->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Report Cards */
    .report-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.8);
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }

    .report-icon {
        transition: background 0.2s ease;
    }
    .report-card:hover .report-icon {
        background: rgba(15,60,145,0.15) !important;
    }

    /* Export Buttons */
    .btn-export {
        font-weight: 500;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
    }
    .btn-pdf {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .btn-pdf:hover {
        background: #dc3545;
        color: white;
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
    }
    .btn-excel {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }
    .btn-excel:hover {
        background: #28a745;
        color: white;
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2);
    }
    .btn-view {
        background: rgba(15, 60, 145, 0.1);
        color: #0f3c91;
        font-weight: 500;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
    }
    .btn-view:hover {
        background: #0f3c91;
        color: white;
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(15, 60, 145, 0.2);
    }

    /* Transaction rows */
    .transaction-row {
        transition: all 0.2s ease;
    }
    .transaction-row:hover {
        background-color: rgba(15, 60, 145, 0.02) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }

    /* Student avatar */
    .student-avatar {
        transition: all 0.2s;
    }
    .transaction-row:hover .student-avatar {
        background: rgba(15,60,145,0.15) !important;
        transform: scale(1.02);
    }

    /* Badges (reused from payments page) */
    .badge-paid, .badge-pending, .badge-failed {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
    }
    .badge-paid {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
    }
    .badge-pending {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
    }
    .badge-failed {
        background: rgba(220, 53, 69, 0.15);
        color: #a71d2a;
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

    /* Table */
    .table td {
        border-bottom: 1px solid #f0f2f5;
        color: #334155;
        vertical-align: middle;
    }
    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e9ecef;
    }

    /* Pagination (Bootstrap 5 styling is already included, but we can keep our custom touches) */
    .pagination .page-link {
        border: none;
        color: #64748b;
        font-weight: 500;
        padding: 0.5rem 1rem;
        margin: 0 0.2rem;
        border-radius: 8px;
        background: transparent;
    }
    .pagination .page-link:hover {
        background: rgba(15, 60, 145, 0.1);
        color: #0f3c91;
    }
    .pagination .active .page-link {
        background: #0f3c91;
        color: white;
        box-shadow: 0 4px 8px rgba(15, 60, 145, 0.2);
    }
    .pagination .disabled .page-link {
        color: #cbd5e0;
        background: transparent;
    }
</style>
@endpush