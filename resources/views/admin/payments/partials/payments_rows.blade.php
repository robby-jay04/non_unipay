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
