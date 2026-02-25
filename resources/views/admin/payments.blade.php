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
        <div class="table-responsive">
            <table class="table table-striped">
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

                {{-- Table Body + Pagination --}}
                @include('admin.payments.partials.payments_table')
            </table>
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
                            <option value="">All</option>
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
    <div class="modal-dialog">
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
                modalBody.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>`;
                try {
                    const res = await fetch(`/admin/payments/${paymentId}`);
                    const html = await res.text();
                    modalBody.innerHTML = html;
                } catch(err){
                    modalBody.innerHTML = `<div class="alert alert-danger">Error loading payment details.</div>`;
                    console.error(err);
                }
            });
        });
    };

    handleViewButtons();

    // AJAX Load Payments (Filter & Pagination)
    const loadPayments = async (url) => {
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();

            // Parse and replace table body + pagination to avoid duplicates
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('paymentsTableBody');
            const newPagination = doc.getElementById('paymentsPagination');

            document.getElementById('paymentsTableBody').innerHTML = newTbody.innerHTML;
            document.getElementById('paymentsPagination').innerHTML = newPagination.innerHTML;

            handleViewButtons(); // rebind buttons for new rows
        } catch(err){
            console.error(err);
            alert('Failed to load payments.');
        }
    };

    // Filter submit
    const filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function(e){
        e.preventDefault();
        const status = document.getElementById('status').value;
        const url = '{{ route("admin.payments") }}' + (status ? '?status=' + status : '');
        loadPayments(url);
        bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
    });

    // Pagination click
    document.addEventListener('click', function(e){
        const link = e.target.closest('.pagination a');
        if(link){
            e.preventDefault();
            loadPayments(link.href);
        }
    });

});
</script>
@endpush