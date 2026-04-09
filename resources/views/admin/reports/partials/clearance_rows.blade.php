@forelse($clearances as $student)
<tr class="clearance-row">
    <td class="px-4 py-3 fw-medium" style="color: var(--text-secondary);">{{ $student->student_no ?? '—' }}</td>
    <td class="py-3">
        <div class="d-flex align-items-center gap-3">
            <div class="student-avatar rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 42px; height: 42px; background: rgba(15,60,145,0.1); font-size: 1rem; font-weight: 700; color: #0f3c91;">
                {{ strtoupper(substr($student->user->name, 0, 1)) }}
            </div>
            <span class="fw-semibold" style="color: var(--text-primary);">{{ $student->user->name }}</span>
        </div>
    </td>
    <td class="py-3">
        @if($student->course)
            <span class="badge-course">{{ $student->course }}</span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td class="py-3">
        @if($student->year_level)
            <span class="badge-year-level">Year {{ $student->year_level }}</span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td class="py-3" style="color: var(--text-secondary);">{{ $currentSemester->name ?? '—' }}</td>
    <td class="py-3" style="color: var(--text-secondary);">{{ $currentSemester->schoolYear->name ?? '—' }}</td>
    <td class="py-3">
        <span class="badge-cleared-status">
            <i class="fas fa-check-circle me-1"></i> Cleared
        </span>
    </td>
    <td class="py-3 pe-4" style="color: var(--text-secondary);">
        @php
            $lastPayment = $student->payments()->where('status', 'paid')->latest('payment_date')->first();
        @endphp
        {{ $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('M d, Y') : $student->updated_at->format('M d, Y') }}
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-5">
        <div class="empty-state">
            <i class="fas fa-user-slash fa-4x" style="color: var(--text-muted);"></i>
            <h6 class="fw-semibold mt-3" style="color: var(--text-primary);">No students found</h6>
        </div>
    </td>
</tr>
@endforelse