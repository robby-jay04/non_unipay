@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<h2 class="mb-4">Dashboard Overview</h2>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3 class="mb-0">₱{{ number_format($stats['total_revenue'], 2) }}</h3>
                </div>
                <div class="text-success">
                    <i class="fas fa-money-bill fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Pending Payments</h6>
                    <h3 class="mb-0">{{ $stats['pending_payments'] }}</h3>
                </div>
                <div class="text-warning">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Cleared Students</h6>
                    <h3 class="mb-0">{{ $stats['cleared_students'] }}</h3>
                </div>
                <div class="text-info">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Total Students</h6>
                    <h3 class="mb-0">{{ $stats['total_students'] }}</h3>
                </div>
                <div class="text-primary">
                    <i class="fas fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Payments</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student No</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['recent_payments'] as $payment)
                    <tr>
                        <td>{{ $payment->student->user->name }}</td>
                        <td>{{ $payment->student->student_no }}</td>
                        <td>₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td>
                            @if($payment->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
