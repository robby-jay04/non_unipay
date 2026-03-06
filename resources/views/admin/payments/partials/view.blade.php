<div class="container-fluid p-3">
    <div class="row">
        <!-- Student Information -->
        <div class="col-md-6">
            <h6 class="fw-bold">Student Information</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="120">Name:</td>
                    <td><strong>{{ $payment->student->user->name }}</strong></td>
                </tr>
                <tr>
                    <td>Student No:</td>
                    <td>{{ $payment->student->student_no }}</td>
                </tr>
                <tr>
                    <td>Course:</td>
                    <td>{{ $payment->student->course }}</td>
                </tr>
                <tr>
                    <td>Year Level:</td>
                    <td>{{ $payment->student->year_level }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Details -->
        <div class="col-md-6">
            <h6 class="fw-bold">Payment Details</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="120">Reference No:</td>
                    <td><strong>{{ $payment->reference_no }}</strong></td>
                </tr>
                <tr>
                    <td>Total Amount:</td>
                    <td>₱{{ number_format($payment->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td>
                        @if($payment->status == 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($payment->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Payment Date:</td>
                    <td>{{ $payment->payment_date ? date('M d, Y h:i A', strtotime($payment->payment_date)) : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Method:</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Fees Breakdown -->
    @if($payment->fees->count())
        <div class="mt-4">
            <h6 class="fw-bold">Fees Paid</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Fee Name</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment->fees as $fee)
                            <tr>
                                <td>{{ $fee->name }}</td>
                                <td class="text-end">₱{{ number_format($fee->pivot->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td class="text-end">Total:</td>
                            <td class="text-end">₱{{ number_format($payment->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> No fee items recorded for this payment.
        </div>
    @endif

    <!-- Transaction Details (if available) -->
    @if($payment->transaction)
        <div class="mt-4">
            <h6 class="fw-bold">Transaction Details</h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="120">Transaction ID:</td>
                    <td>{{ $payment->transaction->transaction_id }}</td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td><span class="badge bg-success">Completed</span></td>
                </tr>
            </table>
        </div>
    @endif
</div>