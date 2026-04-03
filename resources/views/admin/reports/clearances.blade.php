@extends('admin.layouts.app')

@section('title', 'Clearance Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="fw-bold mb-0" style="color: #0f3c91;">Student Clearance Status</h2>
    </div>
    <div>
        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#downloadModal">
            <i class="fas fa-download me-2"></i> Download PDF
        </button>
    </div>
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
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">Clearance Report</h5>
        <div class="d-flex gap-2">
            <span class="badge rounded-pill px-3 py-2" style="background:rgba(76,175,80,0.15); color:#2e7d32;">
                <i class="fas fa-check me-1"></i>Cleared: {{ $clearances->total() }}
            </span>
            <span class="badge rounded-pill px-3 py-2" style="background:rgba(244,180,20,0.15); color:#b26a00;">
                <i class="fas fa-clock me-1"></i>Pending: {{ $pendingCount }}
            </span>
        </div>

        <div class="d-flex gap-2 ms-auto">
            <!-- Course Filter Form (auto-submit) -->
            <form method="GET" class="d-flex gap-2" action="{{ route('admin.reports.clearances') }}" id="courseFilterForm">
                <select name="course" class="form-select rounded-pill bg-light px-4 py-2" style="min-width: 150px;" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course }}" {{ request('course') == $course ? 'selected' : '' }}>{{ $course }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Year Level Filter Form (auto-submit) -->
            <form method="GET" class="d-flex gap-2" action="{{ route('admin.reports.clearances') }}" id="yearLevelFilterForm">
                <select name="year_level" class="form-select rounded-pill bg-light px-4 py-2" style="min-width: 150px;" onchange="this.form.submit()">
                    <option value="">All Year Levels</option>
                    @foreach($yearLevels as $level)
                        <option value="{{ $level }}" {{ request('year_level') == $level ? 'selected' : '' }}>Year {{ $level }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Search Form -->
            <form method="GET" class="d-flex gap-2" action="{{ route('admin.reports.clearances') }}" id="searchForm">
                <input type="search" name="search" class="form-control rounded-pill border-0 bg-light px-4 py-2"
                       placeholder="Search students..." value="{{ request('search') }}" style="min-width: 250px;">
                <button type="submit" class="btn rounded-pill px-4" style="background: #0f3c91; color: white;">
                    <i class="fas fa-search me-2"></i> Search
                </button>
            </form>
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
                            No cleared students found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($clearances->hasPages())
        <div class="d-flex justify-content-center py-4" id="clearances-pagination">
            {{ $clearances->links('pagination::no-summary') }}
        </div>
        @endif
    </div>
</div>

<!-- Download Modal -->
<div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #ffffff;" id="downloadModalLabel">Download Clearance Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="downloadForm">
                    @csrf
                    <div class="mb-3">
                        <label for="courseSelect" class="form-label fw-semibold">Select Course</label>
                        <select name="course" id="courseSelect" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course }}">{{ $course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="yearLevelSelect" class="form-label fw-semibold">Select Year Level</label>
                        <select name="year_level" id="yearLevelSelect" class="form-select">
                            <option value="">All Year Levels</option>
                            @foreach($yearLevels as $level)
                                <option value="{{ $level }}">Year {{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="search" id="searchInput" value="{{ request('search') }}">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-download me-2"></i> Download PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Loading Overlay for PDF Generation --}}
<div id="clearanceLoader" style="display: none; position: fixed; inset: 0; z-index: 100000; background: rgba(5, 15, 50, 0.75); backdrop-filter: blur(6px); align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
    <div class="loader-card" style="background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%); border-radius: 28px; padding: 2rem 2.5rem; text-align: center; min-width: 240px;">
        <div class="loader-logo-ring" style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
            <img src="{{ asset('logo.png') }}" alt="Non-UniPay" style="width: 70px; height: 70px; border-radius: 50%; background: white; padding: 6px; object-fit: contain;">
            <div class="loader-spinner" style="position: absolute; inset: -5px; border-radius: 50%; border: 3px solid transparent; border-top-color: #f4b400; border-right-color: rgba(244, 180, 0, 0.3); animation: loader-spin 0.85s linear infinite;"></div>
        </div>
        <p class="loader-text" style="color: white; font-weight: 600; margin-top: 1rem;">Generating PDF</p>
        <p class="loader-subtext" style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Please wait...</p>
        <div class="loader-bar-track" style="width: 140px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 99px; overflow: hidden; margin: 0.75rem auto 0;">
            <div class="loader-bar-fill" style="height: 100%; background: #f4b400; border-radius: 99px; animation: loader-bar 1.1s ease-in-out infinite alternate;"></div>
        </div>
    </div>
</div>

<style>
    @keyframes loader-spin {
        to { transform: rotate(360deg); }
    }
    @keyframes loader-bar {
        from { width: 15%; margin-left: 0; }
        to   { width: 70%; margin-left: 30%; }
    }
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const downloadForm = document.getElementById('downloadForm');
    const loader = document.getElementById('clearanceLoader');
    const modalEl = document.getElementById('downloadModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

    if (downloadForm) {
        downloadForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Get form values
            const course = document.getElementById('courseSelect').value;
            const yearLevel = document.getElementById('yearLevelSelect').value;
            const search = document.getElementById('searchInput').value;

            // Build URL with query parameters
            const url = new URL('{{ route("admin.reports.clearances.pdf") }}', window.location.origin);
            if (course) url.searchParams.append('course', course);
            if (yearLevel) url.searchParams.append('year_level', yearLevel);
            if (search) url.searchParams.append('search', search);

            // Close the modal
            modal.hide();

            // Show loader
            if (loader) loader.style.display = 'flex';

            try {
                const response = await fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Server responded with ${response.status}: ${response.statusText}`);
                }

                // Get filename from Content-Disposition header
                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = 'clearance_report.pdf';
                if (contentDisposition) {
                    const match = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (match && match[1]) {
                        filename = match[1].replace(/['"]/g, '');
                    }
                }

                // Create blob and trigger download
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = blobUrl;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(blobUrl);

            } catch (error) {
                console.error('PDF download failed:', error);
                alert('Failed to generate PDF report. Please try again.\n' + error.message);
            } finally {
                // Hide loader
                if (loader) loader.style.display = 'none';
            }
        });
    }
});
</script>
@endpush