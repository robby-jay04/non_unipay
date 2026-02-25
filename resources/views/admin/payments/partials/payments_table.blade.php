<tbody id="paymentsTableBody">
    @foreach($payments as $payment)
    <tr>
        <td>{{ $payment->id }}</td>
        <td>{{ $payment->student->user->name }}</td>
        <td>₱{{ number_format($payment->total_amount, 2) }}</td>
        <td>
            <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                {{ ucfirst($payment->status) }}
            </span>
        </td>
        <td>{{ $payment->reference_no }}</td>
        <td>{{ $payment->created_at->format('M d, Y') }}</td>
        <td>
            <button class="btn btn-sm btn-info viewPaymentBtn" 
                    data-id="{{ $payment->id }}" 
                    data-bs-toggle="modal" 
                    data-bs-target="#viewPaymentModal">
                View
            </button>
        </td>
    </tr>
    @endforeach
</tbody>

<div id="paymentsPagination" class="d-flex justify-content-center my-2">
    <ul class="pagination pagination-sm mb-0">
        {{-- Previous --}}
        @if($payments->onFirstPage())
            <li class="page-item disabled"><span class="page-link">&lt;</span></li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $payments->previousPageUrl() }}">&lt;</a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach(range(1, $payments->lastPage()) as $i)
            <li class="page-item {{ $payments->currentPage() == $i ? 'active' : '' }}">
                <a class="page-link" href="{{ $payments->url($i) }}">{{ $i }}</a>
            </li>
        @endforeach

        {{-- Next --}}
        @if($payments->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $payments->nextPageUrl() }}">&gt;</a>
            </li>
        @else
            <li class="page-item disabled"><span class="page-link">&gt;</span></li>
        @endif
    </ul>
</div>