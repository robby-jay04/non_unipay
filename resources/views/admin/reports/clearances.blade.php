@extends('admin.layouts.app')

@section('title', 'Clearance Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="javascript:history.back()" class="btn-back rounded-circle d-flex align-items-center justify-content-center"
           style="width: 42px; height: 42px; background: var(--input-bg); color: #0f3c91; transition: all 0.2s;">
            <i class="fas fa-arrow-left fa-lg"></i>
        </a>
        <h2 class="fw-bold mb-0" style="color: var(--text-primary);">
            <i class="fas fa-clipboard-list me-2"></i> Student Clearance Status
        </h2>
    </div>
    <button type="button" class="btn-download-pdf rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#downloadModal">
        <i class="fas fa-file-pdf me-2"></i> Download PDF
    </button>
</div>

{{-- Current Semester Info Banner --}}
@php
    $current = $currentSemester ?? null;
@endphp

@if($current)
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, var(--banner-gradient-start) 0%, var(--banner-gradient-end) 100%);">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 48px; height: 48px; background: rgba(15,60,145,0.15);">
                <i class="fas fa-calendar-alt fa-lg" style="color: #0f3c91;"></i>
            </div>
            <div>
                <span class="text-uppercase small fw-semibold text-muted" style="letter-spacing: 0.5px; color: var(--text-muted) !important;">Current Academic Period</span>
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
                    {{ $current->name }} – {{ $current->schoolYear->name ?? 'N/A' }}
                </h5>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Clearance Table Card --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3" style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-users me-2"></i> Clearance Report
        </h5>
        <div class="d-flex gap-2">
            <span class="badge-cleared">
                <i class="fas fa-check-circle me-1"></i> Cleared: {{ $clearances->total() }}
            </span>
            <span class="badge-pending-status">
                <i class="fas fa-clock me-1"></i> Pending: {{ $pendingCount }}
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        {{-- Filter Row --}}
        <div class="p-4 border-bottom" style="background: var(--table-header-bg); border-color: var(--border-color) !important;">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1" style="color: var(--text-muted);">Course</label>
                    <select name="course" form="filterForm" class="form-select rounded-pill border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course }}" {{ request('course') == $course ? 'selected' : '' }}>{{ $course }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold mb-1" style="color: var(--text-muted);">Year Level</label>
                    <select name="year_level" form="filterForm" class="form-select rounded-pill border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                        <option value="">All Year Levels</option>
                        @foreach($yearLevels as $level)
                            <option value="{{ $level }}" {{ request('year_level') == $level ? 'selected' : '' }}>Year {{ $level }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold mb-1" style="color: var(--text-muted);">Search Student</label>
                    <div class="input-group">
                        <input type="text" name="search" form="filterForm" class="form-control rounded-pill border-0 px-3 py-2"
                               placeholder="Name or student number..." value="{{ request('search') }}"
                               style="background: var(--input-bg); color: var(--text-primary);">
                        <button type="submit" form="filterForm" class="btn btn-primary rounded-pill px-4 ms-2">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden form for auto-submit --}}
        <form id="filterForm" method="GET" action="{{ route('admin.reports.clearances') }}" style="display: none;"></form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 clearance-table">
                <thead style="background: var(--table-header-bg);">
                    <tr>
                        <th class="px-4 py-3" style="color: var(--text-primary);">Student No.</th>
                        <th class="py-3" style="color: var(--text-primary);">Student Name</th>
                        <th class="py-3" style="color: var(--text-primary);">Course</th>
                        <th class="py-3" style="color: var(--text-primary);">Year Level</th>
                        <th class="py-3" style="color: var(--text-primary);">Semester</th>
                        <th class="py-3" style="color: var(--text-primary);">School Year</th>
                        <th class="py-3" style="color: var(--text-primary);">Status</th>
                        <th class="py-3 pe-4" style="color: var(--text-primary);">Last Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clearances as $student)
                    <tr class="clearance-row">
                        <td class="px-4 py-3 fw-medium" style="color: var(--text-secondary);">{{ $student->student_no ?? '—' }}</td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="student-avatar rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 42px; height: 42px; background: rgba(15,60,145,0.1); font-size: 1rem; font-weight: 700; color: #0f3c91;">
                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                </div>
                                <span class="fw-semibold" style="color: var(--text-primary);">{{ $student->user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3">
                            @if($student->course)
                                <span class="badge-course">{{ $student->course }}</span>
                            @else
                                <span class="text-muted" style="color: var(--text-muted) !important;">—</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($student->year_level)
                                <span class="badge-year-level">Year {{ $student->year_level }}</span>
                            @else
                                <span class="text-muted" style="color: var(--text-muted) !important;">—</span>
                            @endif
                        </td>
                        <td class="py-3" style="color: var(--text-secondary);">{{ $current->name ?? '—' }}</td>
                        <td class="py-3" style="color: var(--text-secondary);">{{ $current->schoolYear->name ?? '—' }}</td>
                        <td class="py-3">
                            @if($student->clearance_status === 'cleared')
                                <span class="badge-cleared-status">
                                    <i class="fas fa-check-circle me-1"></i> Cleared
                                </span>
                            @else
                                <span class="badge-pending-status">
                                    <i class="fas fa-clock me-1"></i> Not Cleared
                                </span>
                            @endif
                        </td>
                        <td class="py-3 pe-4" style="color: var(--text-secondary);">
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
                        <td colspan="8" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-user-slash fa-4x" style="color: var(--text-muted);"></i>
                                <h6 class="fw-semibold mt-3" style="color: var(--text-primary);">No students found</h6>
                                <p class="small" style="color: var(--text-muted);">Try adjusting your filters or search term.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clearances->hasPages())
        <div class="d-flex justify-content-center py-4">
            {{ $clearances->links('pagination::no-summary') }}
        </div>
        @endif
    </div>
</div>

{{-- Download Modal (Dark mode compatible) --}}
<div class="modal fade" id="downloadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #0f3c91, #1a4da8);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background: rgba(255,255,255,0.15);">
                        <i class="fas fa-file-pdf fa-lg text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Download Clearance Report</h5>
                        <small class="text-white-50">PDF format</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="downloadForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase mb-2" style="color: var(--text-muted);">Course</label>
                        <select name="course" class="form-select rounded-pill border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course }}">{{ $course }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase mb-2" style="color: var(--text-muted);">Year Level</label>
                        <select name="year_level" class="form-select rounded-pill border-0 px-3 py-2" style="background: var(--input-bg); color: var(--text-primary);">
                            <option value="">All Year Levels</option>
                            @foreach($yearLevels as $level)
                                <option value="{{ $level }}">Year {{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="search" id="modalSearchInput" value="{{ request('search') }}">
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal" style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                        <i class="fas fa-download me-2"></i> Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Loading Overlay for PDF Generation (unchanged) --}}
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

    /* Dark mode variables for banner */
    :root {
        --banner-gradient-start: #e8f0fe;
        --banner-gradient-end: #f0f4ff;
    }
    body.dark {
        --banner-gradient-start: #1e293b;
        --banner-gradient-end: #0f172a;
    }

    /* Back button */
    .btn-back:hover {
        background: #0f3c91 !important;
        color: white !important;
        transform: scale(1.05);
    }
    body.dark .btn-back {
        background: var(--input-bg) !important;
        color: #60a5fa !important;
    }
    body.dark .btn-back:hover {
        background: #0f3c91 !important;
        color: white !important;
    }

    /* Download PDF button */
    .btn-download-pdf {
        background: #dc3545;
        color: white;
        border: none;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .btn-download-pdf:hover {
        background: #c82333;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(220,53,69,0.2);
        color: white;
    }

    /* Badges */
    .badge-cleared, .badge-pending-status, .badge-course, .badge-year-level {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        gap: 0.4rem;
    }
    .badge-cleared {
        background: rgba(76,175,80,0.12);
        color: #2e7d32;
    }
    .badge-pending-status {
        background: rgba(244,180,20,0.12);
        color: #b26a00;
    }
    .badge-cleared-status {
        background: rgba(76,175,80,0.12);
        color: #2e7d32;
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
    }
    .badge-course, .badge-year-level {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
        font-weight: 500;
        padding: 0.35rem 0.85rem;
    }
    body.dark .badge-cleared,
    body.dark .badge-cleared-status {
        background: rgba(76,175,80,0.25);
        color: #81c784;
    }
    body.dark .badge-pending-status {
        background: rgba(244,180,20,0.25);
        color: #ffd54f;
    }
    body.dark .badge-course,
    body.dark .badge-year-level {
        background: rgba(59,130,246,0.2);
        color: #93c5fd;
    }

    /* Clearance row hover */
    .clearance-row {
        transition: all 0.2s ease;
    }
    .clearance-row:hover {
        background-color: var(--hover-bg) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .student-avatar {
        transition: all 0.2s;
    }
    .clearance-row:hover .student-avatar {
        background: rgba(15,60,145,0.15) !important;
        transform: scale(1.05);
    }

    /* Dark mode table overrides */
    .clearance-table,
    .clearance-table tbody,
    .clearance-table tr,
    .clearance-table td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
    }
    .clearance-table thead th {
        background-color: var(--table-header-bg);
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
    }
    .clearance-table tbody tr {
        border-bottom: 1px solid var(--table-row-border);
        transition: background 0.2s;
    }
    .clearance-table tbody td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
        border-bottom: none;
    }
    .clearance-table tbody td:first-child {
        color: var(--text-primary);
        font-weight: 500;
    }

    /* Placeholder dark mode */
    .form-control::placeholder,
    input::placeholder {
        color: var(--text-muted);
        opacity: 0.7;
    }
    body.dark .form-control::placeholder,
    body.dark input::placeholder {
        color: #94a3b8;
        opacity: 0.6;
    }

    /* Table base styling */
    .table td {
        border-bottom: 1px solid var(--table-row-border);
        color: var(--text-secondary);
        vertical-align: middle;
    }
    .table th {
        font-weight: 600;
        color: var(--text-primary);
        border-bottom: 2px solid var(--border-color);
        background: var(--table-header-bg);
    }

    /* Empty state */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    .empty-state i {
        opacity: 0.7;
    }

    /* Pagination */
    .pagination .page-link {
        border: none;
        color: var(--text-muted);
        font-weight: 500;
        padding: 0.5rem 1rem;
        margin: 0 0.2rem;
        border-radius: 10px;
        background: transparent;
    }
    .pagination .page-link:hover {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
    }
    .pagination .active .page-link {
        background: #0f3c91;
        color: white;
        box-shadow: 0 4px 8px rgba(15,60,145,0.2);
    }
    .pagination .disabled .page-link {
        color: var(--text-muted);
        opacity: 0.5;
        background: transparent;
    }

    /* Form controls */
    .form-select, .form-control {
        background-color: var(--input-bg);
        border-color: var(--input-border);
        color: var(--text-primary);
        transition: all 0.2s;
    }
    .form-select:focus, .form-control:focus {
        border-color: #0f3c91;
        box-shadow: 0 0 0 3px rgba(15,60,145,0.12);
        background-color: var(--input-bg);
    }
    .btn-primary {
        background: #0f3c91;
        border: none;
        font-weight: 500;
    }
    .btn-primary:hover {
        background: #1a4da8;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(15,60,145,0.2);
    }
    body.dark .btn-primary {
        background: #3b82f6;
    }
    body.dark .btn-primary:hover {
        background: #2563eb;
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-submit filter form on select changes
    const filterForm = document.getElementById('filterForm');
    const selects = document.querySelectorAll('select[form="filterForm"]');
    selects.forEach(select => {
        select.addEventListener('change', function () {
            filterForm.submit();
        });
    });

    // Search input debounce
    const searchInput = document.querySelector('input[name="search"][form="filterForm"]');
    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimer);
                filterForm.submit();
            }
        });
    }

    // PDF download via AJAX with loader
    const downloadForm = document.getElementById('downloadForm');
    const loader = document.getElementById('clearanceLoader');
    const modalEl = document.getElementById('downloadModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

    if (downloadForm) {
        downloadForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const course = downloadForm.querySelector('select[name="course"]').value;
            const yearLevel = downloadForm.querySelector('select[name="year_level"]').value;
            const search = document.getElementById('modalSearchInput').value;

            const url = new URL('{{ route("admin.reports.clearances.pdf") }}', window.location.origin);
            if (course) url.searchParams.append('course', course);
            if (yearLevel) url.searchParams.append('year_level', yearLevel);
            if (search) url.searchParams.append('search', search);

            modal.hide();
            if (loader) loader.style.display = 'flex';

            try {
                const response = await fetch(url.toString(), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) throw new Error(`Server error: ${response.status}`);

                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = 'clearance_report.pdf';
                if (contentDisposition) {
                    const match = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (match && match[1]) filename = match[1].replace(/['"]/g, '');
                }
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
                if (loader) loader.style.display = 'none';
            }
        });
    }
});
</script>
@endpush