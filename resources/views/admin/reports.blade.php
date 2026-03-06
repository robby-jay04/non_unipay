@extends('admin.layouts.app')

@section('title', 'Reports')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Reports & Analytics</h2>
</div>

<div class="row g-4 mb-5">
    <!-- Payment Reports Card -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(15, 60, 145, 0.1);">
                        <i class="fas fa-file-invoice-dollar" style="color: #0f3c91; font-size: 24px;"></i>
                    </div>
                    <h5 class="fw-bold mb-0" style="color: #0f3c91;">Payment Reports</h5>
                </div>
                <p class="text-muted mb-4">Generate comprehensive payment reports in PDF or Excel format.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('admin.reports.pdf') }}" class="btn rounded-pill px-4 py-2" style="background: #dc3545; color: white;">
                        <i class="fas fa-file-pdf me-2"></i> Export PDF
                    </a>
                    <a href="{{ route('admin.reports.excel') }}" class="btn rounded-pill px-4 py-2" style="background: #28a745; color: white;">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Clearance Reports Card -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(244, 180, 20, 0.1);">
                        <i class="fas fa-clipboard-check" style="color: rgb(244, 180, 20); font-size: 24px;"></i>
                    </div>
                    <h5 class="fw-bold mb-0" style="color: #0f3c91;">Clearance Reports</h5>
                </div>
                <p class="text-muted mb-4">View and export student clearance status.</p>
                <a href="{{ route('admin.reports.clearances') }}" class="btn rounded-pill px-4 py-2" style="background: #0f3c91; color: white;">
                    <i class="fas fa-eye me-2"></i> View Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">Recent Transactions</h5>
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
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-4 py-3">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td class="py-3">{{ $payment->student->user->name }}</td>
                        <td class="py-3 fw-semibold" style="color: #0f3c91;">₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td class="py-3 pe-4">
                            @if($payment->status == 'paid')
                                <span class="badge-paid">Paid</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge-pending">Pending</span>
                            @else
                                <span class="badge-failed">Failed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination (matching payments page) -->
        <div class="d-flex justify-content-center py-4">
            <ul class="pagination pagination-sm mb-0">
                @if ($payments->onFirstPage())
                    <li class="page-item disabled"><span class="page-link rounded-start-3">&laquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link rounded-start-3" href="{{ $payments->previousPageUrl() }}">&laquo;</a></li>
                @endif

                @foreach(range(1, $payments->lastPage()) as $i)
                    <li class="page-item {{ $payments->currentPage() == $i ? 'active' : '' }}">
                        <a class="page-link" href="{{ $payments->url($i) }}">{{ $i }}</a>
                    </li>
                @endforeach

                @if ($payments->hasMorePages())
                    <li class="page-item"><a class="page-link rounded-end-3" href="{{ $payments->nextPageUrl() }}">&raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link rounded-end-3">&raquo;</span></li>
                @endif
            </ul>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Reuse badge classes from payments page */
    .badge-paid {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-pending {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-failed {
        background: rgba(220, 53, 69, 0.15);
        color: #a71d2a;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }

    /* Pagination */
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

    /* Table rows */
    .table td {
        border-bottom: 1px solid #f0f2f5;
        color: #334155;
    }
    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e9ecef;
    }
</style>
@endpush