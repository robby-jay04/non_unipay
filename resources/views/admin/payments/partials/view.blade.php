@extends('admin.layouts.app')

@section('title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: #0f3c91;">Payment Management</h2>
</div>

<!-- Filter Badge (if active) -->
@if(request()->filled('status'))
<div class="alert alert-light alert-dismissible fade show d-flex align-items-center justify-content-between shadow-sm rounded-3 mb-4 p-3" style="border-left: 4px solid rgb(244, 180, 20); background: white;">
    <div>
        <i class="fas fa-filter me-2" style="color: rgb(244, 180, 20);"></i>
        <strong>Filtered by:</strong> {{ ucfirst(request('status')) }}
    </div>
    <div>
        <a href="{{ route('admin.payments') }}" class="btn btn-sm btn-outline-secondary rounded-pill me-2">
            <i class="fas fa-times"></i> Clear
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
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
            {{ $payments->appends(request()->query())->links('pagination::bootstrap-4') }}
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

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #0f3c91, #1a4da8); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i> Payment Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="viewPaymentBody">
                <!-- Loaded via AJAX -->
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

    /* Custom badge styles (to match students page) */
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    // ========== VIEW BUTTONS ==========
    function attachViewHandlers() {
        document.querySelectorAll('.viewPaymentBtn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const paymentId = this.dataset.id;
                const modalBody = document.getElementById('viewPaymentBody');
                modalBody.innerHTML = `<div class="text-center py-5"><div class="spinner-border" style="color: #0f3c91;" role="status"><span class="visually-hidden">Loading...</span></div></div>`;
                try {
                    const res = await fetch(`/admin/payments/${paymentId}`);
                    if (!res.ok) throw new Error('Failed to load');
                    const html = await res.text();
                    modalBody.innerHTML = html;
                } catch(err) {
                    modalBody.innerHTML = `<div class="alert alert-danger rounded-3">Error loading payment details.</div>`;
                }
            });
        });
    }

    // ========== VERIFY BUTTONS ==========
    function attachVerifyHandlers() {
        document.querySelectorAll('.verifyPaymentBtn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const paymentId = this.dataset.id;
                if (!confirm('Are you sure you want to verify this payment as PAID?')) return;
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                try {
                    const res = await fetch(`/admin/payments/${paymentId}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        const badge = document.getElementById(`status-badge-${paymentId}`);
                        if (badge) {
                            badge.className = 'badge-paid'; // custom paid class
                            badge.textContent = 'Paid';
                        }
                        // Remove verify/reject buttons
                        document.getElementById(`verify-btn-${paymentId}`)?.remove();
                        document.getElementById(`reject-btn-${paymentId}`)?.remove();
                        alert('✅ Payment verified successfully!');
                    } else {
                        alert('❌ ' + (data.message || 'Failed to verify'));
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-check"></i> Verify';
                    }
                } catch (err) {
                    console.error(err);
                    alert('❌ An error occurred');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-check"></i> Verify';
                }
            });
        });
    }

    // ========== REJECT BUTTONS ==========
    function attachRejectHandlers() {
        document.querySelectorAll('.rejectPaymentBtn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const paymentId = this.dataset.id;
                if (!confirm('Are you sure you want to reject this payment?')) return;
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rejecting...';
                try {
                    const res = await fetch(`/admin/payments/${paymentId}/reject`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        const badge = document.getElementById(`status-badge-${paymentId}`);
                        if (badge) {
                            badge.className = 'badge-failed'; // custom failed class
                            badge.textContent = 'Failed';
                        }
                        document.getElementById(`verify-btn-${paymentId}`)?.remove();
                        document.getElementById(`reject-btn-${paymentId}`)?.remove();
                        alert('Payment rejected.');
                    } else {
                        alert('❌ ' + (data.message || 'Failed to reject'));
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-times"></i> Reject';
                    }
                } catch (err) {
                    console.error(err);
                    alert('❌ An error occurred');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-times"></i> Reject';
                }
            });
        });
    }

    // ========== AJAX LOAD PAYMENTS ==========
    function loadPayments(url) {
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('paymentsTableBody');
                const newPagination = doc.getElementById('paymentsPagination');
                if (newTbody) document.getElementById('paymentsTableBody').innerHTML = newTbody.innerHTML;
                if (newPagination) document.getElementById('paymentsPagination').innerHTML = newPagination.innerHTML;
                attachViewHandlers();
                attachVerifyHandlers();
                attachRejectHandlers();
            })
            .catch(err => console.error(err));
    }

    // ========== AUTO‑REFRESH EVERY 10 SECONDS ==========
    let refreshInterval = setInterval(() => loadPayments(window.location.href), 10000);

    // Stop refreshing when the page is hidden (saves resources)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(refreshInterval);
        } else {
            refreshInterval = setInterval(() => loadPayments(window.location.href), 10000);
        }
    });

    // ========== INITIAL ATTACH ==========
    attachViewHandlers();
    attachVerifyHandlers();
    attachRejectHandlers();

    // ========== FILTER FORM SUBMIT ==========
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const status = document.getElementById('status').value;
            let url = '{{ route("admin.payments") }}';
            if (status) url += '?status=' + status;
            bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
            loadPayments(url);
        });
    }

    // ========== PAGINATION CLICK ==========
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link && !link.classList.contains('disabled')) {
            e.preventDefault();
            loadPayments(link.href);
        }
    });
});
</script>
@endpush