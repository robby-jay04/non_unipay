@extends('admin.layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2 class="fw-bold" style="color: var(--text-primary);">
        <i class="fas fa-chart-line me-2"></i> Reports & Analytics
    </h2>
</div>

<!-- Report Cards -->
<div class="row g-4 mb-5">
    <!-- Payment Reports Card -->
    <div class="col-md-6">
        <div class="card report-card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: var(--bg-main);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="report-icon rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 56px; height: 56px; background: rgba(15, 60, 145, 0.1);">
                        <i class="fas fa-file-invoice-dollar" style="color: #0f3c91; font-size: 28px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Payment Reports</h5>
                        <small class="text-muted" style="color: var(--text-muted) !important;">Comprehensive transaction exports</small>
                    </div>
                </div>
                <p class="mb-4" style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);">
                    Generate detailed reports of all payments, including date filtering and status selection.
                </p>
                <div class="d-flex gap-3">
                    <button type="button"
                            class="btn-export btn-pdf rounded-pill px-4 py-2"
                            onclick="openExportModal('pdf')">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </button>
                    <button type="button"
                            class="btn-export btn-excel rounded-pill px-4 py-2"
                            onclick="openExportModal('excel')">
                        <i class="fas fa-file-excel me-2"></i> Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Clearance Reports Card -->
    <div class="col-md-6">
        <div class="card report-card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="background: var(--bg-main);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="report-icon rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width: 56px; height: 56px; background: rgba(244, 180, 20, 0.1);">
                        <i class="fas fa-clipboard-check" style="color: #f4b414; font-size: 28px;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-primary);">Clearance Reports</h5>
                        <small class="text-muted" style="color: var(--text-muted) !important;">Student clearance & exam eligibility</small>
                    </div>
                </div>
                <p class="mb-4" style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);">
                    View and export clearance status of students to verify exam eligibility.
                </p>
                <a href="{{ route('admin.reports.clearances') }}" class="btn-view rounded-pill px-4 py-2">
                    <i class="fas fa-eye me-2"></i> View Report
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">
            <i class="fas fa-history me-2"></i> Recent Transactions
        </h5>
        <span class="badge rounded-pill px-3 py-1" style="background: var(--input-bg); color: var(--text-primary);">Last 10 payments</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 reports-table">
                <thead style="background: var(--table-header-bg);">
                    <tr>
                        <th class="px-4 py-3" style="color: var(--text-primary);">Date & Time</th>
                        <th class="py-3" style="color: var(--text-primary);">Student</th>
                        <th class="py-3" style="color: var(--text-primary);">Amount</th>
                        <th class="py-3 pe-4" style="color: var(--text-primary);">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr class="transaction-row">
                        <td class="px-4 py-3">
                            <div class="fw-medium" style="color: var(--text-primary);">
                                {{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : $payment->created_at->format('M d, Y') }}
                            </div>
                            <small class="text-muted" style="color: var(--text-muted) !important;">
                                {{ $payment->payment_date ? $payment->payment_date->format('h:i A') : $payment->created_at->format('h:i A') }}
                            </small>
                        </td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="student-avatar rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 42px; height: 42px; background: rgba(15,60,145,0.1); font-weight: 600; color: #0f3c91; font-size: 1.1rem;">
                                    {{ strtoupper(substr($payment->student->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="fw-semibold" style="color: var(--text-primary);">{{ $payment->student->user->name }}</span>
                                    <small class="d-block text-muted" style="color: var(--text-muted) !important;">{{ $payment->student->course ?? 'No course' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 fw-bold" style="color: #0f3c91; font-size: 1.05rem;">
                            ₱{{ number_format($payment->total_amount, 2) }}
                        </td>
                        <td class="py-3 pe-4">
                            @if($payment->status == 'paid')
                                <span class="badge-paid"><i class="fas fa-check-circle me-1"></i> Paid</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge-pending"><i class="fas fa-clock me-1"></i> Pending</span>
                            @else
                                <span class="badge-failed"><i class="fas fa-times-circle me-1"></i> Failed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-credit-card fa-4x" style="color: var(--text-muted);"></i>
                                <h6 class="fw-semibold mt-3" style="color: var(--text-primary);">No transactions yet</h6>
                                <p class="small" style="color: var(--text-muted);">When students make payments, they will appear here.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="d-flex justify-content-center py-4">
            {{ $payments->links('pagination::no-summary') }}
        </div>
        @endif
    </div>
</div>

<!-- Export Filter Modal (Dark mode compatible) -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #0f3c91, #1a4da8);">
                <div class="d-flex align-items-center gap-3">
                    <div id="exportModalIcon" class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px; background: rgba(255,255,255,0.15);">
                        <i id="exportModalIconEl" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white" id="exportModalTitle"></h5>
                        <small class="text-white-50" id="exportModalSubtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Quick Presets -->
                <label class="form-label fw-semibold small text-uppercase mb-2" style="color: var(--text-muted);">Quick Select</label>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="today" onclick="applyPreset('today', this)">
                        <i class="fas fa-calendar-day me-1"></i> Today
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="yesterday" onclick="applyPreset('yesterday', this)">
                        <i class="fas fa-calendar-minus me-1"></i> Yesterday
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="this_week" onclick="applyPreset('this_week', this)">
                        <i class="fas fa-calendar-week me-1"></i> This Week
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="this_month" onclick="applyPreset('this_month', this)">
                        <i class="fas fa-calendar-alt me-1"></i> This Month
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="all" onclick="applyPreset('all', this)">
                        <i class="fas fa-list me-1"></i> All Time
                    </button>
                </div>

                <!-- Custom Date Range -->
                <label class="form-label fw-semibold small text-uppercase mb-2" style="color: var(--text-muted);">Custom Range</label>
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <input type="date" id="exportFromDate" class="form-control rounded-3 border-0"
                               placeholder="From" style="background: var(--input-bg); color: var(--text-primary); border-color: var(--input-border);">
                    </div>
                    <div class="col-6">
                        <input type="date" id="exportToDate" class="form-control rounded-3 border-0"
                               placeholder="To" style="background: var(--input-bg); color: var(--text-primary); border-color: var(--input-border);">
                    </div>
                </div>

                <!-- Status Filter -->
                <label class="form-label fw-semibold small text-uppercase mb-2" style="color: var(--text-muted);">Payment Status</label>
                <select id="exportStatus" class="form-select rounded-3 border-0" style="background: var(--input-bg); color: var(--text-primary); border-color: var(--input-border);">
                    <option value="">All Statuses</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1" data-bs-dismiss="modal"
                        style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                <button type="button" class="btn rounded-pill px-4 flex-grow-1 fw-semibold text-white"
                        id="exportConfirmBtn" onclick="confirmExport()">
                    <i id="exportBtnIcon" class="me-2"></i>
                    <span id="exportBtnLabel">Generate</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Report Generation Loading Overlay (unchanged) -->
<div id="reportLoader" style="display: none; position: fixed; inset: 0; z-index: 100000; background: rgba(5, 15, 50, 0.75); backdrop-filter: blur(6px); align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
    <div class="loader-card" style="background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%); border-radius: 28px; padding: 2rem 2.5rem; text-align: center; min-width: 240px;">
        <div class="loader-logo-ring" style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
            <img src="{{ asset('logo.png') }}" alt="Non-UniPay" style="width: 70px; height: 70px; border-radius: 50%; background: white; padding: 6px; object-fit: contain;">
            <div class="loader-spinner" style="position: absolute; inset: -5px; border-radius: 50%; border: 3px solid transparent; border-top-color: #f4b400; border-right-color: rgba(244, 180, 0, 0.3); animation: loader-spin 0.85s linear infinite;"></div>
        </div>
        <p class="loader-text" style="color: white; font-weight: 600; margin-top: 1rem;">Generating Report</p>
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
</style>

@endsection

@push('styles')
<style>
    /* Dark mode table overrides */
    .reports-table,
    .reports-table tbody,
    .reports-table tr,
    .reports-table td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
    }
    .reports-table thead th {
        background-color: var(--table-header-bg);
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
    }
    .reports-table tbody tr {
        border-bottom: 1px solid var(--table-row-border);
        transition: background 0.2s;
    }
    .reports-table tbody tr:hover {
        background-color: var(--hover-bg) !important;
    }
    .reports-table tbody td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
        border-bottom: none;
    }
    .reports-table tbody td:first-child {
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

    /* Report Cards */
    .report-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.8);
    }
    body.dark .report-card {
        border-color: rgba(255,255,255,0.05);
    }
    .report-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 30px rgba(0,0,0,0.08) !important;
    }
    .report-icon {
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .report-card:hover .report-icon {
        transform: scale(1.02);
        background: rgba(15,60,145,0.15) !important;
    }

    /* Export & View Buttons */
    .btn-export, .btn-view {
        font-weight: 500;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    .btn-pdf {
        background: rgba(220,53,69,0.1);
        color: #dc3545;
    }
    .btn-pdf:hover {
        background: #dc3545;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(220,53,69,0.2);
    }
    .btn-excel {
        background: rgba(40,167,69,0.1);
        color: #28a745;
    }
    .btn-excel:hover {
        background: #28a745;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(40,167,69,0.2);
    }
    .btn-view {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
    }
    .btn-view:hover {
        background: #0f3c91;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15,60,145,0.2);
    }

    /* Transaction Row */
    .transaction-row {
        transition: all 0.2s ease;
    }
    .transaction-row:hover {
        background-color: var(--hover-bg) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }
    .student-avatar {
        transition: all 0.2s;
    }
    .transaction-row:hover .student-avatar {
        background: rgba(15,60,145,0.15) !important;
        transform: scale(1.05);
    }

    /* Badges (dark mode friendly) */
    .badge-paid, .badge-pending, .badge-failed {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        gap: 0.4rem;
    }
    .badge-paid    { background: rgba(76,175,80,0.15); color: #2e7d32; }
    .badge-pending { background: rgba(244,180,20,0.15); color: #b26a00; }
    .badge-failed  { background: rgba(220,53,69,0.15); color: #c82333; }
    body.dark .badge-paid    { background: rgba(76,175,80,0.25); color: #81c784; }
    body.dark .badge-pending { background: rgba(244,180,20,0.25); color: #ffd54f; }
    body.dark .badge-failed  { background: rgba(220,53,69,0.25); color: #ef9a9a; }

    /* Empty State */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }
    .empty-state i {
        opacity: 0.6;
    }

    /* Table styling (additional overrides) */
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

    /* Modal Preset Buttons */
    .preset-btn {
        background: var(--input-bg);
        color: var(--text-primary);
        border: 1px solid var(--input-border);
        font-size: 0.82rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    .preset-btn:hover,
    .preset-btn.active {
        background: #0f3c91;
        color: white;
        border-color: #0f3c91;
        transform: translateY(-1px);
    }
    body.dark .preset-btn.active {
        background: #3b82f6;
        border-color: #3b82f6;
    }

    /* Form controls */
    .form-control, .form-select {
        background-color: var(--input-bg);
        border-color: var(--input-border);
        color: var(--text-primary);
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 3px rgba(15,60,145,0.12);
        border-color: #0f3c91 !important;
        background-color: var(--input-bg);
    }
</style>
@endpush

@push('scripts')
<script>
    let exportType = 'pdf';
    let isExporting = false;

    const pdfRoute   = "{{ route('admin.reports.pdf') }}";
    const excelRoute = "{{ route('admin.reports.excel') }}";

    // ─── Helper: format a Date object as YYYY-MM-DD in LOCAL timezone ───
    function formatLocalDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function openExportModal(type) {
        const loader = document.getElementById('reportLoader');
        if (loader) loader.style.display = 'none';
        isExporting = false;
        exportType = type;

        // Reset fields
        document.getElementById('exportFromDate').value = '';
        document.getElementById('exportToDate').value   = '';
        document.getElementById('exportStatus').value   = '';
        document.querySelectorAll('.preset-btn').forEach(btn => btn.classList.remove('active'));

        // Update modal visuals
        if (type === 'pdf') {
            document.getElementById('exportModalIcon').style.background  = 'rgba(255,255,255,0.15)';
            document.getElementById('exportModalIconEl').className       = 'fas fa-file-pdf';
            document.getElementById('exportModalTitle').textContent      = 'Export PDF Report';
            document.getElementById('exportModalSubtitle').textContent   = 'Select date range and status';
            document.getElementById('exportConfirmBtn').style.background = '#dc3545';
            document.getElementById('exportConfirmBtn').style.color      = 'white';
            document.getElementById('exportBtnIcon').className           = 'fas fa-file-pdf me-2';
            document.getElementById('exportBtnLabel').textContent        = 'Generate PDF';
        } else {
            document.getElementById('exportModalIcon').style.background  = 'rgba(255,255,255,0.15)';
            document.getElementById('exportModalIconEl').className       = 'fas fa-file-excel';
            document.getElementById('exportModalTitle').textContent      = 'Export Excel Report';
            document.getElementById('exportModalSubtitle').textContent   = 'Select date range and status';
            document.getElementById('exportConfirmBtn').style.background = '#28a745';
            document.getElementById('exportConfirmBtn').style.color      = 'white';
            document.getElementById('exportBtnIcon').className           = 'fas fa-file-excel me-2';
            document.getElementById('exportBtnLabel').textContent        = 'Generate Excel';
        }

        // Set default preset to "Today" using LOCAL date
        applyPresetByName('today');
        const todayBtn = document.querySelector('.preset-btn[data-preset="today"]');
        if (todayBtn) todayBtn.classList.add('active');

        new bootstrap.Modal(document.getElementById('exportModal')).show();
    }

    function applyPreset(presetName, btnEl) {
        applyPresetByName(presetName);
        document.querySelectorAll('.preset-btn').forEach(btn => btn.classList.remove('active'));
        btnEl.classList.add('active');
    }

    function applyPresetByName(preset) {
        const today = new Date();
        let from = '', to = '';

        if (preset === 'today') {
            from = formatLocalDate(today);
            to = from;
        } else if (preset === 'yesterday') {
            const yest = new Date(today);
            yest.setDate(today.getDate() - 1);
            from = formatLocalDate(yest);
            to = from;
        } else if (preset === 'this_week') {
            const start = new Date(today);
            const day = today.getDay();
            const diff = day === 0 ? -6 : 1 - day;
            start.setDate(today.getDate() + diff);
            from = formatLocalDate(start);
            to = formatLocalDate(today);
        } else if (preset === 'this_month') {
            from = formatLocalDate(new Date(today.getFullYear(), today.getMonth(), 1));
            to = formatLocalDate(today);
        } else if (preset === 'all') {
            from = '';
            to = '';
        }

        document.getElementById('exportFromDate').value = from;
        document.getElementById('exportToDate').value   = to;
    }

    function confirmExport() {
        if (isExporting) return;
        isExporting = true;

        const from   = document.getElementById('exportFromDate').value;
        const to     = document.getElementById('exportToDate').value;
        const status = document.getElementById('exportStatus').value;

        const base = exportType === 'pdf' ? pdfRoute : excelRoute;
        const params = new URLSearchParams();
        if (from)   params.append('from_date', from);
        if (to)     params.append('to_date', to);
        if (status) params.append('status', status);

        const exportUrl = base + (params.toString() ? '?' + params.toString() : '');

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();

        // Show loader
        const loader = document.getElementById('reportLoader');
        if (loader) loader.style.display = 'flex';

        // Direct download – force navigation
        window.location.href = exportUrl;

        // Hide loader after a short delay (download will start)
        setTimeout(() => {
            if (loader) loader.style.display = 'none';
            isExporting = false;
        }, 2000);
    }
</script>
@endpush