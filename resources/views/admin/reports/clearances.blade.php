@extends('admin.layouts.app')

@section('title', 'Clearance Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Student Clearance Status</h2>
</div>

{{-- Current Semester Info --}}
@php
   
    $current = $currentSemester ?? null;
@endphp 

@if($current)
<div class="alert alert-info d-flex align-items-center mb-4" role="alert" style="background: rgba(15,60,145,0.05); border: none; border-left: 4px solid #0f3c91;">
    <i class="fas fa-calendar-alt me-3 fs-4" style="color:#0f3c91;"></i>
    <div>
        <strong>Current Academic Period:</strong> {{ $current->name }} – {{ $current->schoolYear->name ?? 'N/A' }}
    </div>
</div>
@endif

<!-- Clearance Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">Clearance Report</h5>
        {{-- Optional search / filter bar --}}
        <div class="d-flex gap-2">
            <span class="badge rounded-pill px-3 py-2" style="background:rgba(76,175,80,0.15); color:#2e7d32;">
                <i class="fas fa-check me-1"></i>Cleared: {{ $clearances->where('clearance_status', 'cleared')->count() }}
            </span>
            <span class="badge rounded-pill px-3 py-2" style="background:rgba(244,180,20,0.15); color:#b26a00;">
                <i class="fas fa-clock me-1"></i>Pending: {{ $clearances->where('clearance_status', '!=', 'cleared')->count() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Student No.</th>
                        <th class="py-3">Student Name</th>
                        <th class="py-3">Course</th>
                        <th class="py-3">Year Level</th>
                        <th class="py-3">Semester</th>
                        <th class="py-3">School Year</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 pe-4">Last Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clearances as $student)
                    <tr>
                        <td class="px-4 py-3 text-muted">{{ $student->student_no ?? '—' }}</td>
                        <td class="py-3 fw-medium">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:36px; height:36px; background: rgba(15,60,145,0.1); font-size:14px; font-weight:700; color:#0f3c91;">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                </div>
                                {{ $student->user->name }}
                            </div>
                        </td>
                        <td class="py-3 text-muted">{{ $student->course ?? '—' }}</td>
                        <td class="py-3 text-muted">
                            {{ $student->year_level ? 'Year ' . $student->year_level : '—' }}
                        </td>
                        <td class="py-3 text-muted">
                            {{ $current->name ?? '—' }}
                        </td>
                        <td class="py-3 text-muted">
                            {{ $current->schoolYear->name ?? '—' }}
                        </td>
                        <td class="py-3">
                            @if($student->clearance_status === 'cleared')
                                <span class="badge-paid">
                                    <i class="fas fa-check-circle me-1"></i>Cleared
                                </span>
                            @else
                                <span class="badge-pending">
                                    <i class="fas fa-clock me-1"></i>Not Cleared
                                </span>
                            @endif
                        </td>
                        <td class="py-3 pe-4 text-muted">
                            {{-- Show last payment date if available, otherwise student updated_at --}}
                            @php
                                $lastPayment = $student->payments()
                                            ->where('status', 'paid')
                                            ->latest('payment_date')
                                            ->first();
                            @endphp
                            @if($lastPayment && $lastPayment->payment_date)
                                {{ \Carbon\Carbon::parse($lastPayment->payment_date)->format('M d, Y') }}
                            @else
                                {{ $student->updated_at->format('M d, Y') }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 d-block" style="color:#ccc;"></i>
                            No students found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-paid {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
        font-size: 13px;
    }
    .badge-pending {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
        font-size: 13px;
    }
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