
<div class="row">
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Payment Information</h6>
        <table class="table table-sm table-borderless">
            <tr>
                <th width="40%">Payment ID:</th>
                <td>{{ $payment->id }}</td>
            </tr>
            <tr>
                <th>Reference No:</th>
                <td><code>{{ $payment->reference_no ?? 'N/A' }}</code></td>
            </tr>
            <tr>
                <th>Amount:</th>
                <td class="fw-bold text-success">₱{{ number_format($payment->total_amount, 2) }}</td>
            </tr>
            <tr>
                <th>Status:</th>
                <td>
                    <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'processing' ? 'info' : ($payment->status == 'pending' ? 'warning' : 'danger')) }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Payment Method:</th>
                <td>{{ strtoupper($payment->payment_method ?? 'N/A') }}</td>
            </tr>
            <tr>
                <th>Payment Date:</th>
                <td>{{ $payment->payment_date ? $payment->payment_date->format('M d, Y h:i A') : 'Not verified yet' }}</td>
            </tr>
            <tr>
                <th>Created:</th>
                <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Student Information</h6>
        <table class="table table-sm table-borderless">
            <tr>
                <th width="40%">Name:</th>
                <td>{{ $payment->student->user->name }}</td>
            </tr>
            <tr>
                <th>Student No:</th>
                <td>{{ $payment->student->student_no }}</td>
            </tr>
            <tr>
                <th>Course:</th>
                <td>{{ $payment->student->course }}</td>
            </tr>
            <tr>
                <th>Year Level:</th>
                <td>{{ $payment->student->year_level }}</td>
            </tr>
            <tr>
                <th>Contact:</th>
                <td>{{ $payment->student->contact }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $payment->student->user->email }}</td>
            </tr>
        </table>
    </div>
</div>

@if($payment->transaction)
<hr class="my-4">
<h6 class="text-muted mb-3">Transaction Details</h6>
<div class="bg-light p-3 rounded">
    <table class="table table-sm table-borderless mb-0">
        <tr>
            <th width="20%">Transaction ID:</th>
            <td>{{ $payment->transaction->id }}</td>
        </tr>
        <tr>
            <th>Reference:</th>
            <td><code>{{ $payment->transaction->reference_no }}</code></td>
        </tr>
        <tr>
            <th>Status:</th>
            <td>{{ ucfirst($payment->transaction->status) }}</td>
        </tr>
        @if($payment->paymongo_source_id)
        <tr>
            <th>PayMongo Source:</th>
            <td><small><code>{{ $payment->paymongo_source_id }}</code></small></td>
        </tr>
        @endif
    </table>
</div>
@endif
