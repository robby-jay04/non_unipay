@extends('admin.layouts.app')

@section('title', 'Audit Trail')

@push('styles')
<style>
    /* ===== Page Header (same as Manage Admins) ===== */
    .page-header {
        background: var(--modal-header-bg);
        border-radius: 20px;
        color: white;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
    }
    .page-header h2 {
        font-weight: 700;
        margin: 0;
        font-size: 1.6rem;
        color: white;
    }
    .page-header p {
        color: rgba(255,255,255,0.8);
        margin: 0;
        font-size: 0.9rem;
    }
    .btn-export-audit {
        background: white;
        color: var(--btn-primary);
        border: none;
        border-radius: 30px;
        padding: 0.6rem 1.4rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-export-audit:hover {
        background: rgba(255,255,255,0.85);
        transform: translateY(-1px);
        color: var(--btn-primary);
    }

    /* ===== Stat Cards (custom for audit) ===== */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: var(--bg-main);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        box-shadow: var(--card-shadow);
        transition: all 0.2s;
        border: 1px solid var(--border-color);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        border-color: var(--btn-primary);
    }
    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1;
    }
    .stat-value.high { color: #ef4444; }
    .stat-value.med  { color: #f59e0b; }

    /* ===== Filter Bar (similar to Manage Admins search bar) ===== */
    .filter-bar {
        background: var(--bg-main);
        border-radius: 20px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
    }
    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: flex-end;
    }
    .filter-group {
        flex: 1;
        min-width: 140px;
    }
    .filter-group label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        display: block;
    }
    .filter-group input,
    .filter-group select {
        background: var(--input-bg);
        border: 1.5px solid var(--input-border);
        border-radius: 12px;
        padding: 0.5rem 0.8rem;
        font-size: 0.85rem;
        color: var(--text-primary);
        width: 100%;
        transition: all 0.2s;
    }
    .filter-group input:focus,
    .filter-group select:focus {
        border-color: var(--btn-primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(15,60,145,0.1);
    }
    .btn-filter {
        background: var(--btn-primary);
        color: white;
        border: none;
        border-radius: 30px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-filter:hover {
        background: var(--btn-primary-hover);
        transform: translateY(-1px);
    }
    .btn-reset {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
        border-radius: 30px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .btn-reset:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
    }

    /* ===== Table (same as Manage Admins table) ===== */
    .card-table {
        background: var(--bg-main);
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: background 0.3s ease, box-shadow 0.3s ease;
    }
    .card-table .card-body {
        padding: 0;
    }
    .table {
        margin: 0;
        color: var(--text-secondary);
    }
    .table thead th {
        background: var(--table-header-bg);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        padding: 1rem 1.2rem;
    }
    .table tbody td {
        padding: 0.9rem 1.2rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--table-row-border);
        font-size: 0.85rem;
        background-color: var(--bg-main);
        color: var(--text-secondary);
    }
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    .table tbody tr:hover td {
        background-color: var(--hover-bg);
        cursor: pointer;
    }

    /* Severity badges */
    .sev {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.7rem;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .sev-high   { background: rgba(239,68,68,0.12); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
    .sev-medium { background: rgba(245,158,11,0.12); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .sev-low    { background: rgba(34,197,94,0.12);  color: #22c55e; border: 1px solid rgba(34,197,94,0.3); }

    /* Action type pill */
    .action-type {
        font-family: monospace;
        font-size: 0.7rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        color: var(--text-primary);
    }

    /* Pagination (same as Manage Admins style) */
    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .page-info {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
    .page-links {
        display: flex;
        gap: 0.3rem;
    }
    .page-links a,
    .page-links span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.15s;
    }
    .page-links a {
        background: var(--input-bg);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }
    .page-links a:hover {
        background: var(--btn-primary);
        color: white;
        border-color: var(--btn-primary);
    }
    .page-links span.active {
        background: var(--btn-primary);
        color: white;
        border: 1px solid var(--btn-primary);
    }
    .page-links span.disabled {
        color: var(--text-muted);
        background: transparent;
        border: 1px solid var(--border-color);
        opacity: 0.5;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }
    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }

    /* ===== Modal (same as Manage Admins) ===== */
    .modal-content {
        background: var(--bg-main);
        border-radius: 20px;
        border: none;
        box-shadow: 0 8px 40px rgba(0,0,0,0.12);
    }
    .modal-header {
        background: var(--modal-header-bg);
        border-radius: 20px 20px 0 0;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }
    .modal-header .modal-title {
        color: white;
        font-weight: 600;
    }
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
    .modal-body {
        padding: 1.5rem;
        background: var(--bg-main);
    }
    .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        background: var(--bg-main);
    }
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem 1.5rem;
        margin-bottom: 1rem;
    }
    .detail-item label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        display: block;
        margin-bottom: 0.2rem;
    }
    .detail-item p {
        font-size: 0.85rem;
        color: var(--text-primary);
        word-break: break-word;
        margin: 0;
    }
    .diff-wrap {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }
    .diff-block {
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 0.75rem;
    }
    .diff-block h4 {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }
    .diff-block pre {
        font-family: monospace;
        font-size: 0.7rem;
        color: var(--text-primary);
        white-space: pre-wrap;
        word-break: break-all;
        margin: 0;
    }
    .loading-modal {
        text-align: center;
        padding: 2rem;
        color: var(--text-muted);
    }
      .pagination .page-link { border: none; color: var(--text-muted); font-weight: 500; padding: 0.5rem 1rem; margin: 0 0.2rem; border-radius: 8px; background: transparent; }
    .pagination .page-link:hover { background: rgba(15,60,145,0.1); color: #0f3c91; }
    .pagination .active .page-link { background: #0f3c91; color: white; box-shadow: 0 4px 8px rgba(15,60,145,0.2); }
    .pagination .disabled .page-link { color: var(--text-muted); opacity: 0.5; background: transparent; }
</style>
@endpush

@section('content')

{{-- ── Success / Error messages (optional) ── --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── Page Header ── --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h2><i class="fas fa-history me-2"></i>Audit Trail</h2>
            <p>Complete history of system events, modifications, and security logs.</p>
        </div>
        <a href="{{ route('admin.superadmin.audit-logs.export', request()->only('search','module','severity','date_from','date_to')) }}"
           class="btn-export-audit">
            <i class="fas fa-download me-1"></i> Export CSV
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="stat-grid" id="statGrid">
    <div class="stat-card">
        <div class="stat-label">Events Today</div>
        <div class="stat-value" id="statEventsToday">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Fee Modifications</div>
        <div class="stat-value med" id="statFeeMods">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">High Severity</div>
        <div class="stat-value high" id="statHigh">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Student Failed Logins</div>
        <div class="stat-value high" id="statStudentFailed">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Admin Failed Logins</div>
        <div class="stat-value high" id="statFailed">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Admins</div>
        <div class="stat-value" id="statAdmins">—</div>
    </div>
</div>
{{-- ── Filter Bar ── --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.superadmin.audit-logs.index') }}" id="filterForm">
        <div class="filter-row">
            <div class="filter-group" style="flex:2;">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Description, action, IP address...">
            </div>
            <div class="filter-group">
                <label>Module</label>
                <select name="module">
                    <option value="">All</option>
                    @foreach(['Fee','Payment','Student','SchoolYear','AdminAuth','StudentAuth','ExamPeriod','Semester'] as $mod)
                        <option value="{{ $mod }}" @selected(request('module') === $mod)>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Severity</label>
                <select name="severity">
                    <option value="">All</option>
                    <option value="high"   @selected(request('severity') === 'high')>High</option>
                    <option value="medium" @selected(request('severity') === 'medium')>Medium</option>
                    <option value="low"    @selected(request('severity') === 'low')>Low</option>
                </select>
            </div>
            <div class="filter-group">
                <label>From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="filter-group">
                <label>To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search me-1"></i> Filter</button>
            <a href="{{ route('admin.superadmin.audit-logs.index') }}"class="btn-reset"><i class="fas fa-undo-alt"></i> Reset</a>
        </div>
    </form>
</div>

{{-- ── Audit Logs Table ── --}}
<div class="card-table">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Timestamp</th>
                        <th>Actor</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Severity</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                  @forelse($logs as $log)
    <tr onclick="openModal('{{ $log->id }}')">
        <td class="mono" style="font-family: monospace;">{{ $log->id }}</td>
        <td style="white-space: nowrap;">
            {{ $log->created_at->format('Y-m-d') }}<br>
            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
        </td>
        {{-- Actor column: Admin > Student > System --}}
        <td>
            @if($log->admin)
                {{ $log->admin->name }}
                <span class="badge bg-primary ms-1">Admin</span>
            @elseif($log->student && $log->student->user)
                {{ $log->student->user->name }}
                <span class="badge bg-info ms-1">Student</span>
            @else
                <span class="text-muted">System</span>
            @endif
        </td>
        <td>{{ $log->module }}</td>
        <td style="max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
            {{ $log->description }}
        </td>
        <td><span class="sev sev-{{ $log->severity }}">{{ $log->severity }}</span></td>
        <td><code>{{ $log->ip_address ?? '—' }}</code></td>
    </tr>
@empty
    <tr>
        <td colspan="7">
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <p>No audit records found for the selected filters.</p>
            </div>
        </td>
    </tr>
@endforelse
                </tbody>
            </table>
        </div>

          <!-- Pagination -->
     
        @if($logs->hasPages())
            <div class="d-flex justify-content-center py-4">
                {{ $logs->appends(request()->query())->links('pagination::no-summary') }}
            </div>
        @endif
    </div>
</div>

{{-- ── Detail Modal ── --}}
<div class="modal fade" id="auditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Audit Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="loading-modal">
                    <i class="fas fa-spinner fa-pulse me-2"></i> Loading details...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Fetch stats ──────────────────────────────────────────────────
fetch("{{ route('admin.superadmin.audit-logs.stats') }}")
    .then(res => res.json())
    .then(data => {
        document.getElementById('statEventsToday').textContent = data.events_today ?? '—';
        document.getElementById('statFeeMods').textContent     = data.fee_mods_today ?? '—';
        document.getElementById('statHigh').textContent        = data.high_severity_today ?? '—';
        document.getElementById('statFailed').textContent      = data.failed_logins_today ?? '—';
        document.getElementById('statAdmins').textContent      = data.active_admins_today ?? '—';
        document.getElementById('statStudentFailed').textContent = data.student_failed_logins_today ?? '—';
    })
    .catch(err => console.error('Stats error:', err));

// ── Modal handling ───────────────────────────────────────────────
let currentModal = null;

function openModal(id) {
    const modalEl = document.getElementById('auditModal');
    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = '<div class="loading-modal"><i class="fas fa-spinner fa-pulse me-2"></i> Loading details...</div>';
    currentModal = new bootstrap.Modal(modalEl);
    currentModal.show();

   fetch(`/admin/superadmin/audit-logs/${id}`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(log => renderModal(log))
    .catch(err => {
        modalBody.innerHTML = '<div class="alert alert-danger">Failed to load log details.</div>';
        console.error(err);
    });
}

function renderModal(log) {
    const modalBody = document.getElementById('modalBody');
    const sevClass = `sev-${log.severity}`;
    const severityHtml = `<span class="sev ${sevClass}">${log.severity}</span>`;

    const oldVal = log.old_value ? JSON.stringify(log.old_value, null, 2) : 'null';
    const newVal = log.new_value ? JSON.stringify(log.new_value, null, 2) : 'null';

    modalBody.innerHTML = `
        <div class="detail-grid">
            <div class="detail-item">
                <label>ID</label>
                <p>#${log.id}</p>
            </div>
            <div class="detail-item">
                <label>Timestamp</label>
                <p>${log.created_at}</p>
            </div>
            <div class="detail-item">
    <label>Actor</label>
    <p>
        @if($log->admin)
            {{ $log->admin->name }} (Admin)<br>
            <small>{{ $log->admin->email }}</small>
        @elseif($log->student && $log->student->user)
            {{ $log->student->user->name }} (Student)<br>
            <small>{{ $log->student->user->email }}</small>
        @else
            System / Guest
        @endif
    </p>
</div>
            <div class="detail-item">
                <label>Severity</label>
                <p>${severityHtml}</p>
            </div>
            <div class="detail-item">
                <label>Action Type</label>
                <p><span class="action-type">${log.action_type}</span></p>
            </div>
            <div class="detail-item">
                <label>Module</label>
                <p>${log.module}</p>
            </div>
            <div class="detail-item">
                <label>IP Address</label>
                <p><code>${log.ip_address ?? '—'}</code></p>
            </div>
            <div class="detail-item">
                <label>Session ID</label>
                <p><small>${log.session_id ?? '—'}</small></p>
            </div>
            <div class="detail-item">
                <label>HTTP Method</label>
                <p>${log.http_method ?? '—'}</p>
            </div>
            <div class="detail-item">
                <label>URL</label>
                <p style="word-break: break-all;">${log.url ?? '—'}</p>
            </div>
            <div class="detail-item" style="grid-column:1/-1">
                <label>Description</label>
                <p>${log.description}</p>
            </div>
        </div>
        ${(log.old_value || log.new_value) ? `
        <div class="diff-wrap">
            <div class="diff-block">
                <h4>Before</h4>
                <pre>${escapeHtml(oldVal)}</pre>
            </div>
            <div class="diff-block">
                <h4>After</h4>
                <pre>${escapeHtml(newVal)}</pre>
            </div>
        </div>` : ''}
    `;
}

function escapeHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
</script>
@endpush