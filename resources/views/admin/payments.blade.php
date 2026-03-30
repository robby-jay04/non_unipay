@extends('admin.layouts.app')

@section('title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Payment Management</h2>
</div>

<!-- Filter Badge (if active) -->
@if(request()->filled('status'))
<div class="alert alert-light d-flex align-items-center justify-content-between shadow-sm rounded-3 mb-4 p-3" style="border-left: 4px solid rgb(244, 180, 20); background: white;">
    <div>
        <i class="fas fa-filter me-2" style="color: rgb(244, 180, 20);"></i>
        <strong>Filtered by:</strong> {{ ucfirst(request('status')) }}
    </div>
    <a href="{{ route('admin.payments') }}" class="text-muted" style="font-size: 1.5rem; line-height: 1; text-decoration: none;">
        <i class="fas fa-times"></i>
    </a>
</div>
@endif

<!-- Main Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold" style="color: #0f3c91;">All Payments</h5>
        <button class="btn btn-sm rounded-pill px-4" style="background: #0f3c91; color: white;" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-filter me-2"></i> Filter
        </button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="py-3">Student</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Reference</th>
                        <th class="py-3">Date</th>
                        <th class="py-3 pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="paymentsTableBody">
                    @include('admin.payments.partials.payments_rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center py-4" id="paymentsPagination">
            <ul class="pagination pagination-sm mb-0">
                @if ($payments->onFirstPage())
                    <li class="page-item disabled"><span class="page-link rounded-start-3">&laquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link rounded-start-3" href="{{ $payments->previousPageUrl() }}">&laquo;</a></li>
                @endif

                @foreach(range(1, $payments->lastPage()) as $i)
                    <li class="page-item {{ $payments->currentPage() == $i ? 'active' : '' }}">
                        <a class="page-link" href="{{ $payments->appends(request()->query())->url($i) }}">{{ $i }}</a>
                    </li>
                @endforeach

                @if ($payments->hasMorePages())
                    <li class="page-item"><a class="page-link rounded-end-3" href="{{ $payments->nextPageUrl() }}">&raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link rounded-end-3">&raquo;</span></li>
                @endif
            </ul>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-filter me-2"></i> Filter Payments</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="filterForm" method="GET" action="{{ route('admin.payments') }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="status" class="form-label fw-medium">Status</label>
                        <select class="form-select rounded-pill border-0 bg-light px-4 py-2" id="status" name="status">
                            <option value="">All Payments</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn rounded-pill px-4" style="background: #0f3c91; color: white;">Apply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div id="confirmIconWrap" class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:64px;height:64px;"></div>
            </div>
            <div class="modal-body text-center px-4 pb-2 mt-2">
                <h5 class="fw-bold mb-2" id="confirmTitle"></h5>
                <p class="text-muted mb-0" id="confirmMessage"></p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn rounded-pill px-4 fw-semibold" id="confirmActionBtn"></button>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div id="resultIconWrap" class="rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width:64px;height:64px;"></div>
            </div>
            <div class="modal-body text-center px-4 pb-2 mt-2">
                <h5 class="fw-bold mb-2" id="resultTitle"></h5>
                <p class="text-muted mb-0" id="resultMessage"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn rounded-pill px-5 fw-semibold" data-bs-dismiss="modal" style="background:#0f3c91;color:white;">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
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
@endsection

@push('styles')
<style>
    /* Pagination */
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

    /* Status badges */
    .badge-paid {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-pending {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }
    .badge-failed {
        background: rgba(220, 53, 69, 0.15);
        color: #a71d2a;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        display: inline-block;
    }

    /* Table rows */
    .table td {
        border-bottom: 1px solid #f0f2f5;
        color: #334155;
    }
    .table th {
        font-weight: 600;
        color: #475569;
        border-bottom: 2px solid #e9ecef;
    }

    /* Buttons */
    .btn-outline-secondary {
        border-color: #e2e8f0;
        color: #475569;
    }
    .btn-outline-secondary:hover {
        background: #e2e8f0;
        border-color: #cbd5e0;
        color: #0f3c91;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    // ── Modal helpers ────────────────────────────────────────────────────────

    function showConfirm({ title, message, confirmText, confirmStyle, onConfirm }) {
        document.getElementById('confirmTitle').textContent   = title;
        document.getElementById('confirmMessage').textContent = message;

        const iconWrap = document.getElementById('confirmIconWrap');
        iconWrap.style.background = confirmStyle.iconBg;
        iconWrap.innerHTML        = `<i class="${confirmStyle.icon}" style="font-size:1.6rem;color:${confirmStyle.iconColor};"></i>`;

        // Clone button to remove old listeners
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
            error:   { iconBg: 'rgba(220,53,69,0.12)',  icon: 'fas fa-times-circle', iconColor: '#a71d2a' },
            info:    { iconBg: 'rgba(15,60,145,0.12)',  icon: 'fas fa-info-circle',  iconColor: '#0f3c91' },
        };
        const p = palettes[type] || palettes.info;

        document.getElementById('resultTitle').textContent   = title;
        document.getElementById('resultMessage').textContent = message;

        const iconWrap = document.getElementById('resultIconWrap');
        iconWrap.style.background = p.iconBg;
        iconWrap.innerHTML        = `<i class="${p.icon}" style="font-size:1.6rem;color:${p.iconColor};"></i>`;

        new bootstrap.Modal(document.getElementById('resultModal')).show();
    }

    // ── Button handlers ──────────────────────────────────────────────────────

    const handleViewButtons = () => {
        document.querySelectorAll('.viewPaymentBtn').forEach(btn => {
            btn.addEventListener('click', async function () {
                const paymentId = this.dataset.id;
                const modalBody = document.getElementById('viewPaymentBody');
                modalBody.innerHTML = `<div class="text-center py-5"><div class="spinner-border" style="color:#0f3c91;" role="status"><span class="visually-hidden">Loading...</span></div></div>`;
                try {
                    const res = await fetch(`/admin/payments/${paymentId}`);
                    if (!res.ok) throw new Error('Failed to load');
                    modalBody.innerHTML = await res.text();
                } catch {
                    modalBody.innerHTML = `<div class="alert alert-danger rounded-3">Error loading payment details.</div>`;
                }
            });
        });
    };

    const handleVerifyButtons = () => {
        document.querySelectorAll('.verifyPaymentBtn').forEach(btn => {
            btn.addEventListener('click', function () {
                const paymentId = this.dataset.id;
                const self      = this;

                showConfirm({
                    title:       'Verify Payment',
                    message:     'Are you sure you want to mark this payment as PAID?',
                    confirmText: 'Yes, Verify',
                    confirmStyle: {
                        iconBg:    'rgba(76,175,80,0.12)',
                        icon:      'fas fa-check-circle',
                        iconColor: '#2e7d32',
                        btnBg:     '#2e7d32',
                    },
                    onConfirm: async () => {
                        self.disabled  = true;
                        self.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        try {
                            const res  = await fetch(`/admin/payments/${paymentId}/verify`, {
                                method:  'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept':       'application/json'
                                },
                            });
                            const data = await res.json();
                            if (data.success) {
                                const badge = document.getElementById(`status-badge-${paymentId}`);
                                badge.className   = 'badge-paid';
                                badge.textContent = 'Paid';
                                document.getElementById(`verify-btn-${paymentId}`)?.remove();
                                document.getElementById(`reject-btn-${paymentId}`)?.remove();
                                showResult({
                                    type:    'success',
                                    title:   'Payment Verified',
                                    message: 'The payment has been marked as paid successfully.'
                                });
                            } else {
                                showResult({
                                    type:    'error',
                                    title:   'Verification Failed',
                                    message: data.message || 'Unable to verify this payment.'
                                });
                                self.disabled  = false;
                                self.innerHTML = '<i class="fas fa-check"></i> Verify';
                            }
                        } catch {
                            showResult({
                                type:    'error',
                                title:   'Error',
                                message: 'An unexpected error occurred. Please try again.'
                            });
                            self.disabled  = false;
                            self.innerHTML = '<i class="fas fa-check"></i> Verify';
                        }
                    },
                });
            });
        });
    };

    const handleRejectButtons = () => {
        document.querySelectorAll('.rejectPaymentBtn').forEach(btn => {
            btn.addEventListener('click', function () {
                const paymentId = this.dataset.id;
                const self      = this;

                showConfirm({
                    title:       'Reject Payment',
                    message:     'Are you sure you want to reject this payment? This action cannot be undone.',
                    confirmText: 'Yes, Reject',
                    confirmStyle: {
                        iconBg:    'rgba(220,53,69,0.12)',
                        icon:      'fas fa-times-circle',
                        iconColor: '#a71d2a',
                        btnBg:     '#a71d2a',
                    },
                    onConfirm: async () => {
                        self.disabled  = true;
                        self.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        try {
                            const res  = await fetch(`/admin/payments/${paymentId}/reject`, {
                                method:  'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept':       'application/json'
                                },
                            });
                            const data = await res.json();
                            if (data.success) {
                                const badge = document.getElementById(`status-badge-${paymentId}`);
                                badge.className   = 'badge-failed';
                                badge.textContent = 'Failed';
                                document.getElementById(`verify-btn-${paymentId}`)?.remove();
                                document.getElementById(`reject-btn-${paymentId}`)?.remove();
                                showResult({
                                    type:    'info',
                                    title:   'Payment Rejected',
                                    message: 'The payment has been rejected successfully.'
                                });
                            } else {
                                showResult({
                                    type:    'error',
                                    title:   'Rejection Failed',
                                    message: data.message || 'Unable to reject this payment.'
                                });
                                self.disabled  = false;
                                self.innerHTML = '<i class="fas fa-times"></i> Reject';
                            }
                        } catch {
                            showResult({
                                type:    'error',
                                title:   'Error',
                                message: 'An unexpected error occurred. Please try again.'
                            });
                            self.disabled  = false;
                            self.innerHTML = '<i class="fas fa-times"></i> Reject';
                        }
                    },
                });
            });
        });
    };

    // ── AJAX pagination & filter ─────────────────────────────────────────────

    const rebindAll = () => {
        handleViewButtons();
        handleVerifyButtons();
        handleRejectButtons();
    };

  const loadPayments = async (url) => {
    // Only force https on non-localhost (e.g. ngrok)
    if (!url.includes('localhost') && !url.includes('127.0.0.1')) {
        url = url.replace(/^http:\/\//i, 'https://');
    }
    console.log('Fetching URL:', url);

    try {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        console.log('Status:', res.status);
        const text = await res.text();
        console.log('RAW:', text.substring(0, 1000));

        const data = JSON.parse(text);
        document.getElementById('paymentsTableBody').innerHTML = data.rows;
        document.getElementById('paymentsPagination').innerHTML = data.pagination;
        rebindAll();
    } catch (err) {
        console.error('FETCH ERROR:', err.message);
    }
};

    rebindAll();

   document.getElementById('filterForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const status = document.getElementById('status').value;
    let url = window.location.origin + '/admin/payments';
    if (status) url += '?status=' + encodeURIComponent(status);
    bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
    loadPayments(url);
});

    document.addEventListener('click', function (e) {
    const link = e.target.closest('#paymentsPagination a');
    if (link) {
        e.preventDefault();
        loadPayments(link.href);
    }
});
});
</script>
@endpush