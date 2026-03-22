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
                    <button type="button"
                            class="btn-export btn-pdf rounded-pill px-4 py-2"
                            onclick="openExportModal('pdf')">
                        <i class="fas fa-file-pdf me-2"></i> Export PDF
                    </button>
                    <button type="button"
                            class="btn-export btn-excel rounded-pill px-4 py-2"
                            onclick="openExportModal('excel')">
                        <i class="fas fa-file-excel me-2"></i> Export Excel
                    </button>
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
                            <span class="fw-medium" style="color: #1e293b;">
                                {{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : $payment->created_at->format('M d, Y') }}
                            </span>
                            <small class="d-block text-muted">
                                {{ $payment->payment_date ? $payment->payment_date->format('h:i A') : $payment->created_at->format('h:i A') }}
                            </small>
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
                                <p class="text-muted small">When students make payments, they'll appear here.</p>
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

<!-- ── Export Filter Modal ── -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Modal Header -->
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div id="exportModalIcon"
                         class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 48px; height: 48px;">
                        <i id="exportModalIconEl" style="font-size: 1.4rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" id="exportModalTitle" style="color: #ffffff;"></h5>
                        <small class="text-muted" id="exportModalSubtitle"></small>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 pb-2">

                <!-- Quick Presets -->
                <p class="text-muted mb-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    Quick Select
                </p>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="today"
                            onclick="applyPreset('today', this)">
                        <i class="fas fa-calendar-day me-1"></i> Today
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="yesterday"
                            onclick="applyPreset('yesterday', this)">
                        <i class="fas fa-calendar-minus me-1"></i> Yesterday
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="this_week"
                            onclick="applyPreset('this_week', this)">
                        <i class="fas fa-calendar-week me-1"></i> This Week
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="this_month"
                            onclick="applyPreset('this_month', this)">
                        <i class="fas fa-calendar-alt me-1"></i> This Month
                    </button>
                    <button type="button" class="preset-btn rounded-pill px-3 py-1"
                            data-preset="all"
                            onclick="applyPreset('all', this)">
                        <i class="fas fa-list me-1"></i> All Time
                    </button>
                </div>

                <!-- Custom Date Range -->
                <p class="text-muted mb-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    Custom Date Range
                </p>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label text-muted small mb-1">From</label>
                        <input type="date" id="exportFromDate"
                               class="form-control rounded-3 border-0 bg-light">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted small mb-1">To</label>
                        <input type="date" id="exportToDate"
                               class="form-control rounded-3 border-0 bg-light">
                    </div>
                </div>

                <!-- Status Filter -->
                <p class="text-muted mb-2" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                    Payment Status
                </p>
                <select id="exportStatus" class="form-select rounded-3 border-0 bg-light mb-1">
                    <option value="">All Statuses</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>

            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex gap-2">
                <button type="button"
                        class="btn btn-light rounded-pill px-4 flex-grow-1"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button"
                        class="btn rounded-pill px-4 flex-grow-1 fw-semibold"
                        id="exportConfirmBtn"
                        onclick="confirmExport()">
                    <i id="exportBtnIcon" class="me-2"></i>
                    <span id="exportBtnLabel">Generate</span>
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* ── Report Cards ── */
    .report-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.8);
    }
    .report-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .report-icon { transition: background 0.2s ease; }
    .report-card:hover .report-icon { background: rgba(15,60,145,0.15) !important; }

    /* ── Export / View Buttons ── */
    .btn-export {
        font-weight: 500;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-pdf  { background: rgba(220,53,69,0.1); color: #dc3545; }
    .btn-pdf:hover  { background: #dc3545; color: white; transform: scale(1.02); box-shadow: 0 4px 8px rgba(220,53,69,0.2); }
    .btn-excel { background: rgba(40,167,69,0.1); color: #28a745; }
    .btn-excel:hover { background: #28a745; color: white; transform: scale(1.02); box-shadow: 0 4px 8px rgba(40,167,69,0.2); }
    .btn-view {
        background: rgba(15,60,145,0.1);
        color: #0f3c91;
        font-weight: 500;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
    }
    .btn-view:hover { background: #0f3c91; color: white; transform: scale(1.02); box-shadow: 0 4px 8px rgba(15,60,145,0.2); }

    /* ── Transaction Rows ── */
    .transaction-row { transition: all 0.2s ease; }
    .transaction-row:hover {
        background-color: rgba(15,60,145,0.02) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.02);
    }
    .student-avatar { transition: all 0.2s; }
    .transaction-row:hover .student-avatar {
        background: rgba(15,60,145,0.15) !important;
        transform: scale(1.02);
    }

    /* ── Badges ── */
    .badge-paid, .badge-pending, .badge-failed {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
    }
    .badge-paid    { background: rgba(76,175,80,0.15);  color: #2e7d32; }
    .badge-pending { background: rgba(244,180,20,0.15); color: #b26a00; }
    .badge-failed  { background: rgba(220,53,69,0.15);  color: #a71d2a; }

    /* ── Empty State ── */
    .empty-state { padding: 2rem; }
    .empty-state i { opacity: 0.7; }
    .empty-state h6 { font-size: 1.1rem; }
    .empty-state p  { font-size: 0.9rem; max-width: 300px; margin: 0 auto; }

    /* ── Table ── */
    .table td { border-bottom: 1px solid #f0f2f5; color: #334155; vertical-align: middle; }
    .table th  { font-weight: 600; color: #475569; border-bottom: 2px solid #e9ecef; }

    /* ── Pagination ── */
    .pagination .page-link {
        border: none; color: #64748b; font-weight: 500;
        padding: 0.5rem 1rem; margin: 0 0.2rem;
        border-radius: 8px; background: transparent;
    }
    .pagination .page-link:hover { background: rgba(15,60,145,0.1); color: #0f3c91; }
    .pagination .active .page-link { background: #0f3c91; color: white; box-shadow: 0 4px 8px rgba(15,60,145,0.2); }
    .pagination .disabled .page-link { color: #cbd5e0; background: transparent; }

    /* ── Modal Preset Buttons ── */
    .preset-btn {
        background: #f0f4ff;
        color: #0f3c91;
        border: 1px solid #dce3f5;
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
    }
    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(15,60,145,0.12);
        border-color: #0f3c91 !important;
        background: white !important;
    }
</style>
@endpush

@push('scripts')
<script>
    let exportType = 'pdf';

    const pdfRoute   = "{{ route('admin.reports.pdf') }}";
    const excelRoute = "{{ route('admin.reports.excel') }}";

    function openExportModal(type) {
        exportType = type;

        // Reset form
        document.getElementById('exportFromDate').value = '';
        document.getElementById('exportToDate').value   = '';
        document.getElementById('exportStatus').value   = '';
        document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));

        if (type === 'pdf') {
            document.getElementById('exportModalIcon').style.background  = 'rgba(220,53,69,0.1)';
            document.getElementById('exportModalIconEl').className       = 'fas fa-file-pdf';
            document.getElementById('exportModalIconEl').style.color     = '#dc3545';
            document.getElementById('exportModalTitle').textContent      = 'Export PDF Report';
            document.getElementById('exportModalSubtitle').textContent   = 'Choose a date range to include in the PDF';
            document.getElementById('exportConfirmBtn').style.background = '#dc3545';
            document.getElementById('exportConfirmBtn').style.color      = 'white';
            document.getElementById('exportBtnIcon').className           = 'fas fa-file-pdf me-2';
            document.getElementById('exportBtnLabel').textContent        = 'Generate PDF';
        } else {
            document.getElementById('exportModalIcon').style.background  = 'rgba(40,167,69,0.1)';
            document.getElementById('exportModalIconEl').className       = 'fas fa-file-excel';
            document.getElementById('exportModalIconEl').style.color     = '#28a745';
            document.getElementById('exportModalTitle').textContent      = 'Export Excel Report';
            document.getElementById('exportModalSubtitle').textContent   = 'Choose a date range to include in the Excel file';
            document.getElementById('exportConfirmBtn').style.background = '#28a745';
            document.getElementById('exportConfirmBtn').style.color      = 'white';
            document.getElementById('exportBtnIcon').className           = 'fas fa-file-excel me-2';
            document.getElementById('exportBtnLabel').textContent        = 'Generate Excel';
        }

        // ✅ Default to today without needing a click event
        applyPresetByName('today');

        // ✅ Mark today button as active
        document.querySelectorAll('.preset-btn').forEach(b => {
            b.classList.toggle('active', b.getAttribute('data-preset') === 'today');
        });

        new bootstrap.Modal(document.getElementById('exportModal')).show();
    }

    // ✅ Called from button clicks — passes the button element to mark it active
    function applyPreset(presetName, btnEl) {
        applyPresetByName(presetName);
        document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
        btnEl.classList.add('active');
    }

    // ✅ Pure date logic — no event dependency
    function applyPresetByName(preset) {
        const today = new Date();
        const fmt   = d => d.toISOString().split('T')[0];

        let from = '', to = fmt(today);

        if (preset === 'today') {
            from = fmt(today);
            to   = fmt(today);
        } else if (preset === 'yesterday') {
            const y = new Date(today);
            y.setDate(y.getDate() - 1);
            from = fmt(y);
            to   = fmt(y);
        } else if (preset === 'this_week') {
            const mon = new Date(today);
            mon.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1));
            from = fmt(mon);
            to   = fmt(today);
        } else if (preset === 'this_month') {
            from = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
            to   = fmt(today);
        } else if (preset === 'all') {
            from = '';
            to   = '';
        }

        document.getElementById('exportFromDate').value = from;
        document.getElementById('exportToDate').value   = to;
    }

    function confirmExport() {
        const from   = document.getElementById('exportFromDate').value;
        const to     = document.getElementById('exportToDate').value;
        const status = document.getElementById('exportStatus').value;

        const base   = exportType === 'pdf' ? pdfRoute : excelRoute;
        const params = new URLSearchParams();

        if (from)   params.append('from_date', from);
        if (to)     params.append('to_date', to);
        if (status) params.append('status', status);

        bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        window.location.href = base + (params.toString() ? '?' + params.toString() : '');
    }
</script>
@endpush