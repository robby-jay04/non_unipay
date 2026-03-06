@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Dashboard Overview</h2>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-white p-4 rounded-4 shadow-sm border-0 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted text-uppercase small fw-semibold">Total Revenue</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(15, 60, 145, 0.1); width: 48px; height: 48px;">
                    <i class="fas fa-money-bill-wave" style="color: #0f3c91; font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: #0f3c91;">₱{{ number_format($stats['total_revenue'], 2) }}</h3>
                <small class="text-muted">All time</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-white p-4 rounded-4 shadow-sm border-0 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted text-uppercase small fw-semibold">Pending Payments</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(244, 180, 20, 0.1); width: 48px; height: 48px;">
                    <i class="fas fa-clock" style="color: rgb(244, 180, 20); font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: rgb(244, 180, 20);">{{ $stats['pending_payments'] }}</h3>
                <small class="text-muted">Awaiting confirmation</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-white p-4 rounded-4 shadow-sm border-0 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted text-uppercase small fw-semibold">Cleared Students</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(76, 175, 80, 0.1); width: 48px; height: 48px;">
                    <i class="fas fa-check-circle" style="color: #4caf50; font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: #4caf50;">{{ $stats['cleared_students'] }}</h3>
                <small class="text-muted">Ready for exams</small>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-white p-4 rounded-4 shadow-sm border-0 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted text-uppercase small fw-semibold">Total Students</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(15, 60, 145, 0.1); width: 48px; height: 48px;">
                    <i class="fas fa-users" style="color: #0f3c91; font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: #0f3c91;">{{ $stats['total_students'] }}</h3>
                <small class="text-muted">Registered</small>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">Recent Payments</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Student</th>
                        <th class="py-3">Student No</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 pe-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['recent_payments'] as $payment)
                    <tr>
                        <td class="px-4 py-3 fw-medium">{{ $payment->student->user->name }}</td>
                        <td class="py-3">{{ $payment->student->student_no }}</td>
                        <td class="py-3 fw-semibold" style="color: #0f3c91;">₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td class="py-3">
                            @if($payment->status == 'paid')
                                <span class="badge px-3 py-2 rounded-pill" style="background: rgba(76, 175, 80, 0.15); color: #2e7d32; font-weight: 500;">Paid</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge px-3 py-2 rounded-pill" style="background: rgba(244, 180, 20, 0.15); color: #b26a00; font-weight: 500;">Pending</span>
                            @else
                                <span class="badge px-3 py-2 rounded-pill" style="background: rgba(220, 53, 69, 0.15); color: #a71d2a; font-weight: 500;">Failed</span>
                            @endif
                        </td>
                        <td class="py-3 pe-4 text-muted">{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .stat-icon {
        transition: background 0.2s ease;
    }
    .stat-card:hover .stat-icon {
        background: rgba(15, 60, 145, 0.15) !important;
    }
    .badge {
        font-size: 0.85rem;
    }
    .table th {
        font-weight: 600;
        color: #495057;
        border-bottom-width: 1px;
    }
    .table td {
        border-bottom: 1px solid #f0f2f5;
    }
    .table > :not(caption) > * > * {
        border-bottom-color: #f0f2f5;
    }
</style>
@endpush