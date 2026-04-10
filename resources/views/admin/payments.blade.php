@extends('admin.layouts.app')

@section('title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: var(--text-primary);">Payment Management</h2>
</div>

<!-- Main Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center gap-3 flex-wrap" style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">All Payments</h5>
        <div class="d-flex align-items-center gap-2 flex-wrap w-100 justify-content-end" style="max-width: 100%;">
            <!-- Status Filter Dropdown -->
            <select id="statusFilter" class="form-select rounded-pill px-4 py-2" style="width: auto; min-width: 150px; background: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);">
                <option value="">All Payments</option>
                <option value="paid"    {{ request('status') == 'paid'    ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed"  {{ request('status') == 'failed'  ? 'selected' : '' }}>Failed</option>
            </select>

            <!-- Search Input -->
            <div class="search-wrap flex-grow-1" style="min-width: 200px; max-width: 320px;">
                <input type="text" id="searchInput"
                       class="form-control rounded-pill border-0 px-4"
                       placeholder="Search student or reference..."
                       value="{{ request('search') }}"
                       autocomplete="off"
                       style="background: var(--input-bg); color: var(--text-primary); padding-right: 2.5rem; width: 100%;">
                <i class="fas fa-circle-notch fa-spin" id="searchSpinner"></i>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 payments-table">
                <thead style="background: var(--table-header-bg);">
                    <tr>
                        <th class="px-4 py-3" style="color: var(--text-primary);">ID</th>
                        <th class="py-3"      style="color: var(--text-primary);">Student</th>
                        <th class="py-3"      style="color: var(--text-primary);">Amount</th>
                        <th class="py-3"      style="color: var(--text-primary);">Status</th>
                        <th class="py-3"      style="color: var(--text-primary);">Reference</th>
                        <th class="py-3"      style="color: var(--text-primary);">Date</th>
                        <th class="py-3 pe-4" style="color: var(--text-primary);">Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentsTableBody">
                    @include('admin.payments.partials.payments_rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center py-4" id="paymentsPagination">
            {!! $payments->appends(request()->query())->links('pagination::no-summary') !!}
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div id="confirmIconWrap" class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:64px;height:64px;"></div>
            </div>
            <div class="modal-body text-center px-4 pb-2 mt-2">
                <h5 class="fw-bold mb-2" id="confirmTitle"   style="color: var(--text-primary);"></h5>
                <p  class="mb-0"         id="confirmMessage" style="color: var(--text-secondary);"></p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"
                        style="background: var(--input-bg); color: var(--text-primary);">Cancel</button>
                <button type="button" class="btn rounded-pill px-4 fw-semibold" id="confirmActionBtn"></button>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div id="resultIconWrap" class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:64px;height:64px;"></div>
            </div>
            <div class="modal-body text-center px-4 pb-2 mt-2">
                <h5 class="fw-bold mb-2" id="resultTitle"   style="color: var(--text-primary);"></h5>
                <p  class="mb-0"         id="resultMessage" style="color: var(--text-secondary);"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn rounded-pill px-5 fw-semibold" data-bs-dismiss="modal"
                        style="background:#0f3c91;color:white;">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4" style="background: var(--bg-main);">
            <div class="modal-header border-0"
                 style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i> Payment Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewPaymentBody">
                <div class="text-center py-5">
                    <div class="spinner-border" style="color: #0f3c91;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Page Loader --}}
<div id="pageLoader" style="display: none; position: fixed; inset: 0; z-index: 100000; background: rgba(5,15,50,0.75); backdrop-filter: blur(6px); align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
    <div class="loader-card" style="background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%); border-radius: 28px; padding: 2rem 2.5rem; text-align: center; min-width: 240px;">
        <div class="loader-logo-ring" style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
            <img src="{{ asset('logo.png') }}" alt="Non-UniPay" style="width: 70px; height: 70px; border-radius: 50%; background: white; padding: 6px; object-fit: contain;">
            <div class="loader-spinner" style="position: absolute; inset: -5px; border-radius: 50%; border: 3px solid transparent; border-top-color: #f4b400; border-right-color: rgba(244,180,0,0.3); animation: loader-spin 0.85s linear infinite;"></div>
        </div>
        <p style="color: white; font-weight: 600; margin-top: 1rem;">Loading Data</p>
        <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Please wait...</p>
        <div style="width: 140px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 99px; overflow: hidden; margin: 0.75rem auto 0;">
            <div style="height: 100%; background: #f4b400; border-radius: 99px; animation: loader-bar 1.1s ease-in-out infinite alternate;"></div>
        </div>
    </div>
</div>

{{-- Action Loader --}}
<div id="paymentActionLoader" style="display: none; position: fixed; inset: 0; z-index: 100000; background: rgba(5,15,50,0.75); backdrop-filter: blur(6px); align-items: center; justify-content: center; flex-direction: column; gap: 1rem;">
    <div class="loader-card" style="background: linear-gradient(180deg, #0f3c91 0%, #1a4da8 100%); border-radius: 28px; padding: 2rem 2.5rem; text-align: center; min-width: 240px;">
        <div class="loader-logo-ring" style="position: relative; width: 70px; height: 70px; margin: 0 auto;">
            <img src="{{ asset('logo.png') }}" alt="Non-UniPay" style="width: 70px; height: 70px; border-radius: 50%; background: white; padding: 6px; object-fit: contain;">
            <div class="loader-spinner" style="position: absolute; inset: -5px; border-radius: 50%; border: 3px solid transparent; border-top-color: #f4b400; border-right-color: rgba(244,180,0,0.3); animation: loader-spin 0.85s linear infinite;"></div>
        </div>
        <p style="color: white; font-weight: 600; margin-top: 1rem;">Processing Payment</p>
        <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem;">Please wait...</p>
        <div style="width: 140px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 99px; overflow: hidden; margin: 0.75rem auto 0;">
            <div style="height: 100%; background: #f4b400; border-radius: 99px; animation: loader-bar 1.1s ease-in-out infinite alternate;"></div>
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

    .payments-table,
    .payments-table tbody,
    .payments-table tr,
    .payments-table td { background-color: var(--bg-main); color: var(--text-secondary); }
    .payments-table thead th {
        background-color: var(--table-header-bg); color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
    }
    .payments-table tbody tr { border-bottom: 1px solid var(--table-row-border); transition: background 0.2s; }
    .payments-table tbody tr:hover { background-color: var(--hover-bg) !important; }
    .payments-table tbody td { background-color: var(--bg-main); color: var(--text-secondary); border-bottom: none; }
    .payments-table tbody td:first-child { color: var(--text-primary); font-weight: 500; }

    .pagination .page-link {
        border: none; color: var(--text-muted); font-weight: 500;
        padding: 0.5rem 1rem; margin: 0 0.2rem; border-radius: 8px; background: transparent;
    }
    .pagination .page-link:hover  { background: rgba(15,60,145,0.1); color: #0f3c91; }
    .pagination .active .page-link { background: #0f3c91; color: white; box-shadow: 0 4px 8px rgba(15,60,145,0.2); }
    .pagination .disabled .page-link { color: var(--text-muted); opacity: 0.5; background: transparent; }

    .badge-paid, .badge-pending, .badge-processing, .badge-failed {
        font-weight: 600; padding: 0.45rem 1rem; border-radius: 30px;
        display: inline-flex; align-items: center; gap: 0.35rem;
        font-size: 0.85rem; white-space: nowrap; min-width: 100px; justify-content: center;
    }
    .badge-paid       { background: rgba(76,175,80,0.15);  color: #2e7d32; }
    .badge-pending    { background: rgba(244,180,20,0.15); color: #b26a00; }
    .badge-processing { background: rgba(13,110,253,0.15); color: #0a58ca; }
    .badge-failed     { background: rgba(220,53,69,0.15);  color: #a71d2a; }
    body.dark .badge-paid       { background: rgba(76,175,80,0.25);  color: #81c784; }
    body.dark .badge-pending    { background: rgba(244,180,20,0.25); color: #ffd54f; }
    body.dark .badge-processing { background: rgba(59,130,246,0.25); color: #93c5fd; }
    body.dark .badge-failed     { background: rgba(220,53,69,0.25);  color: #ef9a9a; }

    .btn-action {
        width: 36px; height: 36px; border-radius: 50%; border: none;
        display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s; cursor: pointer; background: transparent; color: var(--text-muted);
    }
    .btn-action:hover                   { background: rgba(15,60,145,0.1);  color: #0f3c91;  transform: scale(1.1); }
    .btn-action.verifyPaymentBtn:hover  { background: rgba(40,167,69,0.1);  color: #28a745; }
    .btn-action.rejectPaymentBtn:hover  { background: rgba(220,53,69,0.1);  color: #dc3545; }

    .empty-state { padding: 2rem; text-align: center; }
    .empty-state i  { opacity: 0.7; color: var(--text-muted); }
    .empty-state h6 { font-size: 1.1rem; color: var(--text-primary); }
    .empty-state p  { font-size: 0.9rem; color: var(--text-muted); }

    .form-select, .form-control {
        background-color: var(--input-bg);
        border-color: var(--input-border);
        color: var(--text-primary);
    }
    .form-select:focus, .form-control:focus {
        border-color: #0f3c91;
        box-shadow: 0 0 0 3px rgba(15,60,145,0.1);
        background-color: var(--input-bg);
    }
    .form-control::placeholder, input::placeholder { color: var(--text-muted); opacity: 0.7; }
    body.dark .form-control::placeholder,
    body.dark input::placeholder { color: #94a3b8; opacity: 0.6; }

    .payment-id { color: #1e293b; }
    @media (prefers-color-scheme: dark) { .payment-id { color: #e2e8f0; } }
    .dark-mode .payment-id { color: #e2e8f0; }

    /* Search wrapper + inline spinner */
    .search-wrap { position: relative; width: 100%; }
    #searchSpinner {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0f3c91;
        font-size: 0.8rem;
        display: none;
        pointer-events: none;
    }

    /* Mobile responsive */
    @media (max-width: 576px) {
        .card-header .d-flex.flex-wrap {
            flex-direction: column;
            align-items: stretch !important;
            justify-content: flex-start !important;
        }
        .card-header .d-flex.flex-wrap #statusFilter {
            width: 100% !important;
            min-width: unset !important;
        }
        .search-wrap {
            max-width: 100% !important;
            min-width: unset !important;
        }
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const actionLoader = document.getElementById('paymentActionLoader');
    const pageLoader   = document.getElementById('pageLoader');
    const spinner      = document.getElementById('searchSpinner');

    function showActionLoader() { if (actionLoader) actionLoader.style.display = 'flex'; }
    function hideActionLoader() { if (actionLoader) actionLoader.style.display = 'none'; }
    function showPageLoader()   { if (pageLoader)   pageLoader.style.display   = 'flex'; }
    function hidePageLoader()   { if (pageLoader)   pageLoader.style.display   = 'none'; }
    function showSpinner()      { if (spinner) spinner.style.display = 'inline-block'; }
    function hideSpinner()      { if (spinner) spinner.style.display = 'none'; }

    // ── Modal helpers ──────────────────────────────────────────────────────────
    function showConfirm({ title, message, confirmText, confirmStyle, onConfirm }) {
        document.getElementById('confirmTitle').textContent   = title;
        document.getElementById('confirmMessage').textContent = message;
        const iconWrap = document.getElementById('confirmIconWrap');
        iconWrap.style.background = confirmStyle.iconBg;
        iconWrap.innerHTML = `<i class="${confirmStyle.icon}" style="font-size:1.6rem;color:${confirmStyle.iconColor};"></i>`;
        const oldBtn   = document.getElementById('confirmActionBtn');
        const freshBtn = oldBtn.cloneNode(true);
        oldBtn.parentNode.replaceChild(freshBtn, oldBtn);
        freshBtn.textContent      = confirmText;
        freshBtn.style.background = confirmStyle.btnBg;
        freshBtn.style.color      = 'white';
        freshBtn.addEventListener('click', () => {
            bootstrap.Modal.getInstance(document.getElementById('confirmActionModal')).hide();
            onConfirm();
        });
        new bootstrap.Modal(document.getElementById('confirmActionModal')).show();
    }

    function showResult({ type, title, message }) {
        const palettes = {
            success: { iconBg: 'rgba(76,175,80,0.12)',  icon: 'fas fa-check-circle', iconColor: '#2e7d32' },
            error:   { iconBg: 'rgba(220,53,69,0.12)',   icon: 'fas fa-times-circle', iconColor: '#a71d2a' },
            info:    { iconBg: 'rgba(15,60,145,0.12)',   icon: 'fas fa-info-circle',  iconColor: '#0f3c91' },
        };
        const p = palettes[type] || palettes.info;
        document.getElementById('resultTitle').textContent   = title;
        document.getElementById('resultMessage').textContent = message;
        const iconWrap = document.getElementById('resultIconWrap');
        iconWrap.style.background = p.iconBg;
        iconWrap.innerHTML = `<i class="${p.icon}" style="font-size:1.6rem;color:${p.iconColor};"></i>`;
        new bootstrap.Modal(document.getElementById('resultModal')).show();
    }

    // ── Button handlers ────────────────────────────────────────────────────────
    function handleViewButtons() {
        document.querySelectorAll('.viewPaymentBtn').forEach(btn => {
            btn.removeEventListener('click', btn._viewListener);
            const handler = async function () {
                const paymentId = this.dataset.id;
                const modalBody = document.getElementById('viewPaymentBody');
                modalBody.innerHTML = `<div class="text-center py-5"><div class="spinner-border" style="color:#0f3c91;" role="status"><span class="visually-hidden">Loading...</span></div></div>`;
                try {
                    const res = await fetch(`/admin/payments/${paymentId}`);
                    if (!res.ok) throw new Error('Failed');
                    modalBody.innerHTML = await res.text();
                } catch {
                    modalBody.innerHTML = `<div class="alert alert-danger rounded-3">Error loading payment details.</div>`;
                }
            };
            btn.addEventListener('click', handler);
            btn._viewListener = handler;
        });
    }

    function handleVerifyButtons() {
        document.querySelectorAll('.verifyPaymentBtn').forEach(btn => {
            btn.removeEventListener('click', btn._verifyListener);
            const handler = function () {
                const paymentId = this.dataset.id;
                const self = this;
                showConfirm({
                    title: 'Verify Payment',
                    message: 'Are you sure you want to mark this payment as PAID?',
                    confirmText: 'Yes, Verify',
                    confirmStyle: { iconBg: 'rgba(76,175,80,0.12)', icon: 'fas fa-check-circle', iconColor: '#2e7d32', btnBg: '#2e7d32' },
                    onConfirm: async () => {
                        showActionLoader();
                        self.disabled = true;
                        self.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        try {
                            const res  = await fetch(`/admin/payments/${paymentId}/verify`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            });
                            const data = await res.json();
                            hideActionLoader();
                            if (data.success) {
                                const badge = document.getElementById(`status-badge-${paymentId}`);
                                if (badge) { badge.className = 'badge-paid'; badge.textContent = 'Paid'; }
                                document.getElementById(`verify-btn-${paymentId}`)?.remove();
                                document.getElementById(`reject-btn-${paymentId}`)?.remove();
                                showResult({ type: 'success', title: 'Payment Verified', message: 'The payment has been marked as paid successfully.' });
                            } else {
                                showResult({ type: 'error', title: 'Verification Failed', message: data.message || 'Unable to verify this payment.' });
                                self.disabled = false;
                                self.innerHTML = '<i class="fas fa-check"></i>';
                            }
                        } catch {
                            hideActionLoader();
                            showResult({ type: 'error', title: 'Error', message: 'An unexpected error occurred. Please try again.' });
                            self.disabled = false;
                            self.innerHTML = '<i class="fas fa-check"></i>';
                        }
                    },
                });
            };
            btn.addEventListener('click', handler);
            btn._verifyListener = handler;
        });
    }

    function handleRejectButtons() {
        document.querySelectorAll('.rejectPaymentBtn').forEach(btn => {
            btn.removeEventListener('click', btn._rejectListener);
            const handler = function () {
                const paymentId = this.dataset.id;
                const self = this;
                showConfirm({
                    title: 'Reject Payment',
                    message: 'Are you sure you want to reject this payment? This action cannot be undone.',
                    confirmText: 'Yes, Reject',
                    confirmStyle: { iconBg: 'rgba(220,53,69,0.12)', icon: 'fas fa-times-circle', iconColor: '#a71d2a', btnBg: '#a71d2a' },
                    onConfirm: async () => {
                        showActionLoader();
                        self.disabled = true;
                        self.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        try {
                            const res  = await fetch(`/admin/payments/${paymentId}/reject`, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            });
                            const data = await res.json();
                            hideActionLoader();
                            if (data.success) {
                                const badge = document.getElementById(`status-badge-${paymentId}`);
                                if (badge) { badge.className = 'badge-failed'; badge.textContent = 'Failed'; }
                                document.getElementById(`verify-btn-${paymentId}`)?.remove();
                                document.getElementById(`reject-btn-${paymentId}`)?.remove();
                                showResult({ type: 'info', title: 'Payment Rejected', message: 'The payment has been rejected successfully.' });
                            } else {
                                showResult({ type: 'error', title: 'Rejection Failed', message: data.message || 'Unable to reject this payment.' });
                                self.disabled = false;
                                self.innerHTML = '<i class="fas fa-times"></i>';
                            }
                        } catch {
                            hideActionLoader();
                            showResult({ type: 'error', title: 'Error', message: 'An unexpected error occurred. Please try again.' });
                            self.disabled = false;
                            self.innerHTML = '<i class="fas fa-times"></i>';
                        }
                    },
                });
            };
            btn.addEventListener('click', handler);
            btn._rejectListener = handler;
        });
    }

    function rebindAll() {
        handleViewButtons();
        handleVerifyButtons();
        handleRejectButtons();
    }

    // ── AJAX loadPayments ──────────────────────────────────────────────────────
    let searchAbortCtrl = null;

    async function loadPayments(url, silent = false) {
        if (searchAbortCtrl) { searchAbortCtrl.abort(); searchAbortCtrl = null; }

        if (silent) {
            showSpinner();
            searchAbortCtrl = new AbortController();
        } else {
            showPageLoader();
        }

        try {
            const separator = url.includes('?') ? '&' : '?';
            const ajaxUrl   = url + separator + 'ajax=1';
            const fetchOpts = {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            };
            if (silent) fetchOpts.signal = searchAbortCtrl.signal;

            const response = await fetch(ajaxUrl, fetchOpts);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();

            document.getElementById('paymentsTableBody').innerHTML  = data.rows;
            document.getElementById('paymentsPagination').innerHTML = data.pagination;
            rebindAll();
        } catch (error) {
            if (error.name !== 'AbortError') console.error('Load error:', error);
        } finally {
            if (silent) { hideSpinner(); searchAbortCtrl = null; }
            else          hidePageLoader();
        }
    }

    // ── Build URL ──────────────────────────────────────────────────────────────
    function buildFilterUrl() {
        const status = document.getElementById('statusFilter').value;
        const search = document.getElementById('searchInput').value.trim();
        const params = new URLSearchParams();
        if (status) params.set('status', status);
        if (search) params.set('search', search);
        let url = window.location.origin + '/admin/payments';
        if (params.toString()) url += '?' + params.toString();
        return url;
    }

    // ── Event listeners ────────────────────────────────────────────────────────
    document.getElementById('statusFilter').addEventListener('change', function () {
        loadPayments(buildFilterUrl(), false);
    });

    let searchDebounce;
    document.getElementById('searchInput').addEventListener('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => loadPayments(buildFilterUrl(), true), 350);
    });

    document.addEventListener('click', function (e) {
        const link = e.target.closest('#paymentsPagination a');
        if (!link) return;
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;
        if (link.hasAttribute('data-bs-toggle')) return;
        const href = link.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        try {
            const url = new URL(href, window.location.href);
            if (url.origin !== window.location.origin) return;
        } catch { return; }

        e.preventDefault();
        loadPayments(link.href, false);
    });

    // Initial bindings
    rebindAll();
});
</script>
@endpush