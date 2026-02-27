
@extends('admin.layouts.app')

@section('title', 'Payments')

@section('content')
<h2 class="mb-4">Payment Management</h2>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Payments</h5>
        <div>
            <button class="btn btn-sm btn-primary" id="filterBtn" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter
            </button>
            <button class="btn btn-sm btn-success" id="exportBtn">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Active Filter Badge -->
      
@if(request()->filled('status'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>Filtered by:</strong> {{ ucfirst(request('status')) }}
    <a href="{{ route('admin.payments') }}" class="btn btn-sm btn-outline-secondary ms-2">
        <i class="fas fa-times"></i> Clear Filter
    </a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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
                <tbody id="paymentsTableBody">
                    @include('admin.payments.partials.payments_rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
       <div class="d-flex justify-content-center my-3" id="paymentsPagination">
    <ul class="pagination pagination-sm mb-0">

        {{-- Previous --}}
        @if ($payments->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">&lt;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" 
                   href="{{ $payments->previousPageUrl() }}">&lt;</a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach(range(1, $payments->lastPage()) as $i)
            <li class="page-item {{ $payments->currentPage() == $i ? 'active' : '' }}">
                <a class="page-link" 
                   href="{{ $payments->appends(request()->query())->url($i) }}">
                    {{ $i }}
                </a>
            </li>
        @endforeach

        {{-- Next --}}
        @if ($payments->hasMorePages())
            <li class="page-item">
                <a class="page-link" 
                   href="{{ $payments->nextPageUrl() }}">&gt;</a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">&gt;</span>
            </li>
        @endif

    </ul>
</div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="filterForm" method="GET" action="{{ route('admin.payments') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Payments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Payments</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewPaymentBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    // Export Button
    document.getElementById('exportBtn').addEventListener('click', function() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("admin.payments.export") }}?' + params.toString();
    });

    // Handle View Payment Buttons
    const handleViewButtons = () => {
        document.querySelectorAll('.viewPaymentBtn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const paymentId = this.dataset.id;
                const modalBody = document.getElementById('viewPaymentBody');
                
                modalBody.innerHTML = `<div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`;
                
                try {
                    const res = await fetch(`/admin/payments/${paymentId}`);
                    if (!res.ok) throw new Error('Failed to load');
                    const html = await res.text();
                    modalBody.innerHTML = html;
                } catch(err) {
                    modalBody.innerHTML = `<div class="alert alert-danger">Error loading payment details.</div>`;
                    console.error(err);
                }
            });
        });
    };

    // Handle Verify Buttons
    const handleVerifyButtons = () => {
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
                        badge.className = 'badge bg-success';
                        badge.textContent = 'Paid';

                        document.getElementById(`verify-btn-${paymentId}`).remove();
                        document.getElementById(`reject-btn-${paymentId}`).remove();

                        alert('✅ Payment verified successfully!');
                    } else {
                        alert('❌ ' + (data.message || 'Failed to verify payment'));
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
    };

    // Handle Reject Buttons
    const handleRejectButtons = () => {
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
                        badge.className = 'badge bg-danger';
                        badge.textContent = 'Failed';

                        document.getElementById(`verify-btn-${paymentId}`).remove();
                        document.getElementById(`reject-btn-${paymentId}`).remove();

                        alert('Payment rejected.');
                    } else {
                        alert('❌ ' + (data.message || 'Failed to reject payment'));
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
    };

    // Initialize all handlers
    handleViewButtons();
    handleVerifyButtons();
    handleRejectButtons();

    // AJAX Load Payments (Filter & Pagination)
    const loadPayments = async (url) => {
        try {
            const res = await fetch(url, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' } 
            });
            
            if (!res.ok) throw new Error('Failed to load payments');
            
            const html = await res.text();

            // Replace table body and pagination
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTbody = doc.getElementById('paymentsTableBody');
            const newPagination = doc.getElementById('paymentsPagination');

            if (newTbody) {
                document.getElementById('paymentsTableBody').innerHTML = newTbody.innerHTML;
            }
            
            if (newPagination) {
                document.getElementById('paymentsPagination').innerHTML = newPagination.innerHTML;
            }

            // Rebind all handlers
            handleViewButtons();
            handleVerifyButtons();
            handleRejectButtons();

        } catch(err) {
            console.error(err);
            alert('Failed to load payments.');
        }
    };

    // Filter Form Submit
    const filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const status = document.getElementById('status').value;
        
        // ✅ Build URL correctly - empty status means all payments
        let url = '{{ route("admin.payments") }}';
        if (status) {
            url += '?status=' + status;
        }
        
        console.log('Filter URL:', url); // Debug log
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
        
        // Load filtered payments
        loadPayments(url);
    });

    // Pagination Click Handler
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