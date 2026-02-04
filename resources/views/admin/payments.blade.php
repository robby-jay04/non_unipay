@extends('admin.layouts.app')

@section('title', 'Payments')

@section('content')
<h2 class="mb-4">Payment Management</h2>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Payments</h5>
        <div>
            <button class="btn btn-sm btn-primary" id="filterBtn">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button class="btn btn-sm btn-success" id="exportBtn">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="paymentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Reference</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated via controller -->
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->student->user->name }}</td>
                        <td>₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                        <td>{{ $payment->reference_no }}</td>
                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        <td>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
