
<tbody id="paymentsTableBody">
    @include('admin.payments.partials.payments_rows')
</tbody>

<div class="d-flex justify-content-center" id="paymentsPagination">
    {{ $payments->appends(request()->query())->links() }}
</div>