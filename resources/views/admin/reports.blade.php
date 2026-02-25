@extends('admin.layouts.app')

@section('title', 'Reports')

@section('content')
<h2 class="mb-4">Reports & Analytics</h2>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5>Payment Reports</h5>
                <p class="text-muted">Generate comprehensive payment reports</p>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reports.pdf') }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    <a href="{{ route('admin.reports.excel') }}" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5>Clearance Reports</h5>
                <p class="text-muted">View student clearance status</p>
                <a href="{{ route('admin.reports.clearances') }}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> View Report
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Transactions</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                        <td>{{ $payment->student->user->name }}</td>
                        <td>₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Minimalist Pagination --}}
            <div class="d-flex justify-content-center my-3">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous --}}
                    @if($payments->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&lt;</span></li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->previousPageUrl() }}">&lt;</a>
                        </li>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach(range(1, $payments->lastPage()) as $i)
                        <li class="page-item {{ $payments->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $payments->url($i) }}">{{ $i }}</a>
                        </li>
                    @endforeach

                    {{-- Next --}}
                    @if($payments->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $payments->nextPageUrl() }}">&gt;</a>
                        </li>
                    @else
                        <li class="page-item disabled"><span class="page-link">&gt;</span></li>
                    @endif
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection