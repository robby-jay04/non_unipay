 @extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0" style="color: var(--text-primary);">Dashboard</h2>
        <small style="color: var(--text-muted);">{{ now()->format('l, F j, Y') }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.payments', ['status' => 'pending']) }}"
           class="btn btn-sm d-flex align-items-center gap-2 rounded-pill px-3 py-2"
           style="background: rgba(244,180,20,0.12); color: #b26a00; border: 1px solid rgba(244,180,20,0.3); font-size: 0.82rem; font-weight: 600;">
            <i class="fas fa-clock" style="font-size: 13px;"></i>
            {{ $stats['pending_payments'] }} Pending
        </a>
        <a href="{{ route('admin.reports.clearances') }}"
           class="btn btn-sm d-flex align-items-center gap-2 rounded-pill px-3 py-2"
           style="background: rgba(15,60,145,0.08); color: #0f3c91; border: 1px solid rgba(15,60,145,0.2); font-size: 0.82rem; font-weight: 600;">
            <i class="fas fa-download" style="font-size: 13px;"></i>
            Export
        </a>
    </div>
</div>

{{-- ===================== PRIMARY STAT CARDS ===================== --}}
<div class="row g-3 mb-4">

    {{-- Total Revenue --}}
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.payments') }}" class="stat-card d-block text-decoration-none">
            <div class="stat-card-accent" style="background: #0f3c91;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="stat-label">Total Revenue</span>
                <div class="stat-icon-wrap" style="background: rgba(15,60,145,0.1);">
                    <i class="fas fa-money-bill-wave" style="color: #0f3c91;"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #0f3c91;">₱{{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <small style="color: var(--text-muted);">All time</small>
                @if(isset($stats['revenue_growth']) && $stats['revenue_growth'] != 0)
                    <span class="stat-trend {{ $stats['revenue_growth'] >= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }}" style="font-size: 9px;"></i>
                        {{ abs($stats['revenue_growth']) }}% vs last month
                    </span>
                @endif
            </div>
        </a>
    </div>

    {{-- Pending Payments --}}
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.payments', ['status' => 'pending']) }}" class="stat-card d-block text-decoration-none">
            <div class="stat-card-accent" style="background: #f4b414;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="stat-label">Pending Payments</span>
                <div class="stat-icon-wrap" style="background: rgba(244,180,20,0.1);">
                    <i class="fas fa-clock" style="color: #f4b414;"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #f4b414;">{{ $stats['pending_payments'] }}</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <small style="color: var(--text-muted);">Awaiting confirmation</small>
                @if($stats['pending_payments'] > 0)
                    <span class="stat-trend trend-warn">
                        <i class="fas fa-exclamation" style="font-size: 9px;"></i>
                        Needs attention
                    </span>
                @endif
            </div>
        </a>
    </div>

    {{-- Cleared Students --}}
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.reports.clearances') }}" class="stat-card d-block text-decoration-none">
            <div class="stat-card-accent" style="background: #4caf50;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="stat-label">Cleared Students</span>
                <div class="stat-icon-wrap" style="background: rgba(76,175,80,0.1);">
                    <i class="fas fa-check-circle" style="color: #4caf50;"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #4caf50;">{{ $stats['cleared_students'] }}</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <small style="color: var(--text-muted);">Ready for exams</small>
                @if(isset($stats['cleared_today']) && $stats['cleared_today'] > 0)
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up" style="font-size: 9px;"></i>
                        +{{ $stats['cleared_today'] }} today
                    </span>
                @endif
            </div>
        </a>
    </div>

    {{-- Total Students --}}
    <div class="col-xl-3 col-md-6">
        <a href="{{ route('admin.students') }}" class="stat-card d-block text-decoration-none">
            <div class="stat-card-accent" style="background: #0f3c91;"></div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="stat-label">Total Students</span>
                <div class="stat-icon-wrap" style="background: rgba(15,60,145,0.1);">
                    <i class="fas fa-users" style="color: #0f3c91;"></i>
                </div>
            </div>
            <div class="stat-value" style="color: #0f3c91;">{{ $stats['total_students'] }}</div>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <small style="color: var(--text-muted);">Registered</small>
                @if(isset($stats['new_students_week']) && $stats['new_students_week'] > 0)
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up" style="font-size: 9px;"></i>
                        +{{ $stats['new_students_week'] }} this week
                    </span>
                @endif
            </div>
        </a>
    </div>

</div>

{{-- ===================== MINI STATS ===================== --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background: rgba(15,60,145,0.1);">
                <i class="fas fa-calendar-day" style="color: #0f3c91;"></i>
            </div>
            <div>
                <div class="mini-stat-label">Today's Revenue</div>
                <div class="mini-stat-value" style="color: #0f3c91;">₱{{ number_format($stats['today_revenue'] ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background: rgba(244,180,20,0.1);">
                <i class="fas fa-calendar-alt" style="color: #f4b414;"></i>
            </div>
            <div>
                <div class="mini-stat-label">This Month</div>
                <div class="mini-stat-value" style="color: #f4b414;">₱{{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background: rgba(76,175,80,0.1);">
                <i class="fas fa-chart-line" style="color: #4caf50;"></i>
            </div>
            <div>
                <div class="mini-stat-label">Average Payment</div>
                <div class="mini-stat-value" style="color: #4caf50;">₱{{ number_format($stats['average_payment'] ?? 0, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="mini-stat">
            <div class="mini-stat-icon" style="background: rgba(15,60,145,0.1);">
                <i class="fas fa-trophy" style="color: #f4b414;"></i>
            </div>
            <div>
                <div class="mini-stat-label">Top Student</div>
                <div class="mini-stat-value text-truncate" style="color: var(--text-primary); max-width: 140px;">
                    {{ $stats['top_student'] ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================== CHARTS ROW ===================== --}}
<div class="row g-3 mb-4">

    {{-- Revenue Chart --}}
    <div class="col-lg-8">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <div>
                    <h6 class="dash-card-title mb-0">Revenue Trend</h6>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Confirmed payments over time</small>
                </div>
                <div class="chart-period-tabs" id="periodTabs">
                    <button class="period-tab active" data-period="7">7 days</button>
                    <button class="period-tab" data-period="30">30 days</button>
                    <button class="period-tab" data-period="90">90 days</button>
                </div>
            </div>
            <div class="dash-card-body">
                <div class="d-flex align-items-center gap-4 mb-3">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Period Total</div>
                        <div style="font-size: 1.4rem; font-weight: 700; color: #0f3c91;" id="chartPeriodTotal">
                            ₱{{ number_format(array_sum($stats['revenue_data'] ?? [0]), 2) }}
                        </div>
                    </div>
                    @if(isset($stats['weekly_growth']))
                    <div class="stat-trend {{ $stats['weekly_growth'] >= 0 ? 'trend-up' : 'trend-down' }}" style="align-self: flex-end; margin-bottom: 4px;">
                        <i class="fas fa-arrow-{{ $stats['weekly_growth'] >= 0 ? 'up' : 'down' }}" style="font-size: 9px;"></i>
                        {{ abs($stats['weekly_growth']) }}% vs prev. period
                    </div>
                    @endif
                </div>
                {{-- Canvas with explicit dimensions to prevent layout shifts --}}
                <div style="height:260px; position: relative;">
    <canvas id="revenueChart"></canvas>
</div>
            </div>
        </div>
    </div>

    {{-- Status Doughnut + Progress --}}
    <div class="col-lg-4">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <div>
                    <h6 class="dash-card-title mb-0">Payment Status</h6>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">All payments breakdown</small>
                </div>
            </div>
            <div class="dash-card-body d-flex flex-column align-items-center gap-4">
    {{-- Canvas with explicit dimensions --}}
    <div style="height:200px; width:200px; position: relative;">
        <canvas id="statusChart"></canvas>
    </div>

    {{-- Progress bars --}}
    @php
        $totalPay = ($stats['paid_count'] ?? 0) + ($stats['pending_count'] ?? 0) + ($stats['failed_count'] ?? 0);
        $paidPct  = $totalPay > 0 ? round(($stats['paid_count'] ?? 0) / $totalPay * 100) : 0;
        $pendPct  = $totalPay > 0 ? round(($stats['pending_count'] ?? 0) / $totalPay * 100) : 0;
        $failPct  = $totalPay > 0 ? round(($stats['failed_count'] ?? 0) / $totalPay * 100) : 0;
    @endphp

    <div class="w-100" style="font-size: 0.8rem; max-width: 300px;">
        <div class="status-progress-row">
            <div class="d-flex justify-content-between mb-1">
                <span style="color: var(--text-muted);">Paid</span>
                <span style="color: #4caf50; font-weight: 600;">
                    {{ $stats['paid_count'] ?? 0 }} ({{ $paidPct }}%)
                </span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" data-width="{{ $paidPct }}" style="background: #4caf50;"></div>
            </div>
        </div>

        <div class="status-progress-row mt-2">
            <div class="d-flex justify-content-between mb-1">
                <span style="color: var(--text-muted);">Pending</span>
                <span style="color: #f4b414; font-weight: 600;">
                    {{ $stats['pending_count'] ?? 0 }} ({{ $pendPct }}%)
                </span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" data-width="{{ $pendPct }}" style="background: #f4b414;"></div>
            </div>
        </div>

        <div class="status-progress-row mt-2">
            <div class="d-flex justify-content-between mb-1">
                <span style="color: var(--text-muted);">Failed</span>
                <span style="color: #dc3545; font-weight: 600;">
                    {{ $stats['failed_count'] ?? 0 }} ({{ $failPct }}%)
                </span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" data-width="{{ $failPct }}" style="background: #dc3545;"></div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

{{-- ===================== TABLES ROW ===================== --}}
<div class="row g-3 mb-4">

    {{-- Recent Payments Table --}}
    <div class="col-lg-7">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <div>
                    <h6 class="dash-card-title mb-0">Recent Payments</h6>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Latest transactions</small>
                </div>
                <a href="{{ route('admin.payments') }}" class="btn-view-all">View All</a>
            </div>
            <div class="dash-card-body p-0">

                {{-- Search + Filter --}}
                <div class="table-search-bar px-3 pt-3 pb-2">
                    <div class="table-search-wrap">
                        <i class="fas fa-search table-search-icon"></i>
                        <input type="text" id="paymentsSearch" class="table-search-input" placeholder="Search student or student no...">
                    </div>
                    <div class="table-filter-tabs" id="paymentFilterTabs">
                        <button class="filter-tab active" data-filter="all">All</button>
                        <button class="filter-tab" data-filter="paid">Paid</button>
                        <button class="filter-tab" data-filter="pending">Pending</button>
                        <button class="filter-tab" data-filter="failed">Failed</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="dash-table" id="paymentsTable">
                        <thead>
                            <tr>
                                <th class="ps-3">Student</th>
                                <th>Student No.</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th class="pe-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_payments'] as $payment)
                            <tr data-status="{{ strtolower($payment->status) }}">
                                <td class="ps-3 fw-medium" style="color: var(--text-primary);">{{ $payment->student->user->name }}</td>
                                <td>{{ $payment->student->student_no }}</td>
                                <td class="fw-semibold" style="color: #0f3c91;">₱{{ number_format($payment->total_amount, 2) }}</td>
                                <td>
                                    @if($payment->status == 'paid')
                                        <span class="badge-paid">
                                            <i class="fas fa-check-circle" style="font-size: 10px;"></i> Paid
                                        </span>
                                    @elseif($payment->status == 'pending')
                                        <span class="badge-pending">
                                            <i class="fas fa-clock" style="font-size: 10px;"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge-failed">
                                            <i class="fas fa-times-circle" style="font-size: 10px;"></i> Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-3" style="color: var(--text-muted); font-size: 0.8rem;">
                                    {{ $payment->created_at->format('M d, Y') }}<br>
                                    <span style="font-size: 0.72rem;">{{ $payment->created_at->format('h:i A') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="paymentsEmpty" class="text-center py-4" style="display:none; color: var(--text-muted);">
                        <i class="fas fa-search mb-2" style="font-size: 1.5rem; opacity: 0.4;"></i>
                        <div>No results found</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recently Cleared Table --}}
    <div class="col-lg-5">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <div>
                    <h6 class="dash-card-title mb-0">Recently Cleared</h6>
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Ready for exams</small>
                </div>
                <a href="{{ route('admin.reports.clearances') }}" class="btn-view-all">View All</a>
            </div>
            <div class="dash-card-body p-0">

                {{-- Search + Course Filter --}}
                <div class="table-search-bar px-3 pt-3 pb-2">
                    <div class="table-search-wrap">
                        <i class="fas fa-search table-search-icon"></i>
                        <input type="text" id="clearancesSearch" class="table-search-input" placeholder="Search student...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="dash-table" id="clearancesTable">
                        <thead>
                            <tr>
                                <th class="ps-3">Student</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th class="pe-3">Cleared At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_cleared'] ?? [] as $cleared)
                            <tr>
                                <td class="ps-3 fw-medium" style="color: var(--text-primary);">{{ $cleared->student->user->name }}</td>
                                <td>{{ $cleared->student->course }}</td>
                                <td>
                                    <span class="year-badge">{{ $cleared->student->year_level }}</span>
                                </td>
                                <td class="pe-3" style="color: var(--text-muted); font-size: 0.8rem;">
                                    {{ $cleared->created_at->format('M d, Y') }}<br>
                                    <span style="font-size: 0.72rem;">{{ $cleared->created_at->format('h:i A') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color: var(--text-muted);">No recent clearances</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="clearancesEmpty" class="text-center py-4" style="display:none; color: var(--text-muted);">
                        <i class="fas fa-search mb-2" style="font-size: 1.5rem; opacity: 0.4;"></i>
                        <div>No results found</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ===================== JSON DATA FOR CHARTS ===================== --}}
<script id="dashboard-data" type="application/json">
{
    "revenueLabels": {!! json_encode($stats['revenue_labels'] ?? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!},
    "revenueData":   {!! json_encode($stats['revenue_data']   ?? [0,0,0,0,0,0,0]) !!},
    "paidCount":     {{ $stats['paid_count']    ?? 0 }},
    "pendingCount":  {{ $stats['pending_count'] ?? 0 }},
    "failedCount":   {{ $stats['failed_count']  ?? 0 }}
}
</script>

@endsection

{{-- ===================== STYLES ===================== --}}
@push('styles')
<style>
/* ==========================================
   STAT CARDS
   ========================================== */
.stat-card {
    background: var(--bg-main);
    border-radius: 1rem;
    padding: 1.25rem;
    box-shadow: var(--card-shadow);
    position: relative;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    color: inherit;
    cursor: pointer;
}
.stat-card-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 1rem 1rem 0 0;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.12) !important;
}
.stat-card:hover .stat-card-accent {
    opacity: 1;
}
.stat-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    font-weight: 600;
    color: var(--text-muted);
}
.stat-icon-wrap {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
    transition: transform 0.25s ease;
}
.stat-card:hover .stat-icon-wrap {
    transform: scale(1.08);
}
.stat-value {
    font-size: 1.65rem;
    font-weight: 700;
    line-height: 1.1;
    margin-top: 0.5rem;
}
.stat-trend {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 40px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}
.trend-up   { background: rgba(76,175,80,0.12);  color: #2e7d32; }
.trend-down { background: rgba(220,53,69,0.12);  color: #a71d2a; }
.trend-warn { background: rgba(244,180,20,0.12); color: #b26a00; }
body.dark .trend-up   { background: rgba(76,175,80,0.25);  color: #81c784; }
body.dark .trend-down { background: rgba(220,53,69,0.25);  color: #ef9a9a; }
body.dark .trend-warn { background: rgba(244,180,20,0.25); color: #ffd54f; }

/* ==========================================
   MINI STATS
   ========================================== */
.mini-stat {
    background: var(--bg-main);
    border-radius: 0.875rem;
    padding: 0.9rem 1rem;
    box-shadow: var(--card-shadow);
    display: flex;
    align-items: center;
    gap: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.mini-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(15,60,145,0.1) !important;
}
.mini-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.mini-stat-label {
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 2px;
}
.mini-stat-value {
    font-size: 1rem;
    font-weight: 700;
}

/* ==========================================
   DASH CARDS (Charts + Tables)
   ========================================== */
.dash-card {
    background: var(--bg-main);
    border-radius: 1rem;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.25s ease;
}
.dash-card:hover {
    box-shadow: 0 12px 28px rgba(15,60,145,0.1) !important;
}
.dash-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dash-card-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}
.dash-card-body {
    padding: 1.1rem 1.25rem;
 flex: unset; 
}

/* ==========================================
   CHART PERIOD TABS
   ========================================== */
.chart-period-tabs {
    display: flex;
    gap: 4px;
    background: var(--bg-secondary, rgba(15,60,145,0.05));
    border-radius: 8px;
    padding: 3px;
}
.period-tab {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 4px 12px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
}
.period-tab.active {
    background: #0f3c91;
    color: #fff;
}
body.dark .period-tab.active {
    background: #3b82f6;
}

/* ==========================================
   PROGRESS BARS (Status Chart)
   ========================================== */
.progress-track {
    background: var(--border-color);
    border-radius: 99px;
    height: 6px;
    overflow: hidden;
}
.progress-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 0.8s ease;
    width: 0%; /* fallback, will be overridden by JS or inline style */
}

/* ==========================================
   TABLE SEARCH & FILTER
   ========================================== */
.table-search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.table-search-wrap {
    position: relative;
    flex: 1;
    min-width: 140px;
}
.table-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.72rem;
}
.table-search-input {
    width: 100%;
    padding: 6px 10px 6px 28px;
    font-size: 0.8rem;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-main);
    color: var(--text-primary);
    outline: none;
    transition: border-color 0.2s;
}
.table-search-input:focus {
    border-color: #0f3c91;
}
.table-filter-tabs {
    display: flex;
    gap: 4px;
}
.filter-tab {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 4px 12px;
    border: 1px solid var(--border-color);
    border-radius: 99px;
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.filter-tab.active {
    background: rgba(15,60,145,0.1);
    color: #0f3c91;
    border-color: rgba(15,60,145,0.25);
}
body.dark .filter-tab.active {
    background: rgba(59,130,246,0.15);
    color: #60a5fa;
    border-color: rgba(59,130,246,0.3);
}

/* ==========================================
   DASH TABLE
   ========================================== */
.dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.83rem;
}
.dash-table thead th {
    padding: 8px 12px;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    background: var(--table-header-bg, rgba(15, 60, 145, 0.03));
    border-bottom: 1px solid var(--border-color);
    white-space: nowrap;
}
.dash-table tbody tr {
    border-bottom: 1px solid var(--table-row-border, var(--border-color));
    transition: background 0.15s ease;
}
.dash-table tbody tr:last-child {
    border-bottom: none;
}
.dash-table tbody tr:hover {
    background: var(--hover-bg, rgba(15, 60, 145, 0.03));
}
.dash-table tbody td {
    padding: 9px 12px;
    color: var(--text-secondary);
    vertical-align: middle;
}
.dash-table tbody td:first-child {
    color: var(--text-primary);
    font-weight: 600;
}
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* ==========================================
   BADGES
   ========================================== */
.badge-paid, .badge-pending, .badge-failed {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 40px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-paid    { background: rgba(76,175,80,0.12);  color: #2e7d32; }
.badge-pending { background: rgba(244,180,20,0.12); color: #b26a00; }
.badge-failed  { background: rgba(220,53,69,0.12);  color: #a71d2a; }
body.dark .badge-paid    { background: rgba(76,175,80,0.22);  color: #81c784; }
body.dark .badge-pending { background: rgba(244,180,20,0.22); color: #ffd54f; }
body.dark .badge-failed  { background: rgba(220,53,69,0.22);  color: #ef9a9a; }

/* Year badge */
.year-badge {
    font-size: 0.72rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 6px;
    background: rgba(15,60,145,0.08);
    color: #0f3c91;
    display: inline-block;
}
body.dark .year-badge {
    background: rgba(59,130,246,0.15);
    color: #60a5fa;
}

/* ==========================================
   VIEW ALL BUTTON
   ========================================== */
.btn-view-all {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 99px;
    background: rgba(15,60,145,0.08);
    color: #0f3c91;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-block;
}
.btn-view-all:hover {
    background: #0f3c91;
    color: #fff;
}
body.dark .btn-view-all {
    background: rgba(59,130,246,0.15);
    color: #60a5fa;
}
body.dark .btn-view-all:hover {
    background: #3b82f6;
    color: #fff;
}
</style>
@endpush

{{-- ===================== SCRIPTS ===================== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ------------------------------------------------
       Chart data from PHP
    ------------------------------------------------ */
    const raw = JSON.parse(document.getElementById('dashboard-data').textContent);

    /* ------------------------------------------------
       Theme helpers
    ------------------------------------------------ */
    function isDark() { return document.body.classList.contains('dark'); }

    function themeColors() {
        const dark = isDark();
        return {
            grid   : dark ? '#334155' : '#e2e8f0',
            text   : dark ? '#94a3b8' : '#64748b',
            line   : '#0f3c91',
            fill   : dark ? 'rgba(59,130,246,0.08)' : 'rgba(15,60,145,0.07)',
            tooltip: dark ? '#1e293b' : '#ffffff',
        };
    }

    /* ------------------------------------------------
       Revenue chart
    ------------------------------------------------ */
    let revenueChart, statusChart;
    let currentPeriod = 7;

    function buildRevenueChart(labels, data) {
        const canvas = document.getElementById('revenueChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const c   = themeColors();
        if (revenueChart) revenueChart.destroy();

        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data,
                    borderColor   : c.line,
                    backgroundColor: c.fill,
                    borderWidth   : 2.5,
                    tension       : 0.4,
                    fill          : true,
                    pointRadius   : 4,
                    pointBackgroundColor: c.line,
                    pointBorderColor    : '#fff',
                    pointBorderWidth    : 2,
                    pointHoverRadius    : 6,
                }]
            },
            options: {
                responsive          : true,
                maintainAspectRatio : false,
                  aspectRatio: 2,
                interaction         : { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: c.tooltip,
                        titleColor      : isDark() ? '#f1f5f9' : '#0f172a',
                        bodyColor       : isDark() ? '#94a3b8' : '#64748b',
                        borderColor     : isDark() ? '#334155' : '#e2e8f0',
                        borderWidth     : 1,
                        padding         : 10,
                        callbacks       : {
                            label: ctx => ' ₱' + ctx.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2})
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero : true,
                        grid        : { color: c.grid },
                        ticks       : {
                            color: c.text,
                            callback: v => '₱' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                        },
                        border: { display: false }
                    },
                    x: {
                        grid  : { color: c.grid },
                        ticks : { color: c.text },
                        border: { display: false }
                    }
                }
            }
        });
    }

    /* ------------------------------------------------
       Status doughnut chart
    ------------------------------------------------ */
    function buildStatusChart() {
        const canvas = document.getElementById('statusChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const c   = themeColors();
        if (statusChart) statusChart.destroy();

        statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels  : ['Paid', 'Pending', 'Failed'],
                datasets: [{
                    data           : [raw.paidCount, raw.pendingCount, raw.failedCount],
                    backgroundColor: ['#4caf50', '#f4b414', '#dc3545'],
                    borderWidth    : 0,
                    hoverOffset    : 6,
                }]
            },
            options: {
                responsive         : true,
                maintainAspectRatio: false,
                  aspectRatio: 2,
                cutout             : '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels  : {
                            color    : c.text,
                            padding  : 12,
                            boxWidth : 10,
                            font     : { size: 11 }
                        }
                    },
                    tooltip: {
                        backgroundColor: c.tooltip,
                        titleColor      : isDark() ? '#f1f5f9' : '#0f172a',
                        bodyColor       : isDark() ? '#94a3b8' : '#64748b',
                        borderColor     : isDark() ? '#334155' : '#e2e8f0',
                        borderWidth     : 1,
                    }
                }
            }
        });
    }

    /* ------------------------------------------------
       Set progress bar widths (FIX for empty bars)
    ------------------------------------------------ */
    function setProgressWidths() {
        document.querySelectorAll('.progress-fill').forEach(el => {
            const width = el.getAttribute('data-width');
            if (width !== null && width !== '') {
                el.style.width = width + '%';
            }
        });
    }

    /* ------------------------------------------------
       Period tabs
    ------------------------------------------------ */
    document.querySelectorAll('.period-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.period-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriod = parseInt(this.dataset.period);
            const labels = (raw.revenueLabels || []).slice(-currentPeriod);
            const data   = (raw.revenueData   || []).slice(-currentPeriod);
            buildRevenueChart(labels, data);
           const total = data.reduce((a, b) => Number(a) + Number(b), 0);
            const el = document.getElementById('chartPeriodTotal');
            if (el) el.textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2 });
        });
    });

    /* ------------------------------------------------
       Payments table: search + filter
    ------------------------------------------------ */
    const paymentsSearch = document.getElementById('paymentsSearch');
    const paymentsEmpty  = document.getElementById('paymentsEmpty');

    function filterPaymentsTable() {
        const query      = (paymentsSearch?.value || '').toLowerCase();
        const activeTab  = document.querySelector('#paymentFilterTabs .filter-tab.active');
        const filterVal  = activeTab ? activeTab.dataset.filter : 'all';
        const rows       = document.querySelectorAll('#paymentsTable tbody tr');
        let visible      = 0;

        rows.forEach(row => {
            const text      = row.textContent.toLowerCase();
            const status    = (row.dataset.status || '').toLowerCase();
            const matchText = text.includes(query);
            const matchStat = filterVal === 'all' || status === filterVal;

            if (matchText && matchStat) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (paymentsEmpty) paymentsEmpty.style.display = visible === 0 ? 'block' : 'none';
    }

    if (paymentsSearch) paymentsSearch.addEventListener('input', filterPaymentsTable);

    document.querySelectorAll('#paymentFilterTabs .filter-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#paymentFilterTabs .filter-tab').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterPaymentsTable();
        });
    });

    /* ------------------------------------------------
       Clearances table: search
    ------------------------------------------------ */
    const clearancesSearch = document.getElementById('clearancesSearch');
    const clearancesEmpty  = document.getElementById('clearancesEmpty');

    if (clearancesSearch) {
        clearancesSearch.addEventListener('input', function () {
            const query  = this.value.toLowerCase();
            const rows   = document.querySelectorAll('#clearancesTable tbody tr');
            let visible  = 0;
            rows.forEach(row => {
                const match = row.textContent.toLowerCase().includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (clearancesEmpty) clearancesEmpty.style.display = visible === 0 ? 'block' : 'none';
        });
    }

    /* ------------------------------------------------
       Dark-mode observer — rebuild charts & progress bars
    ------------------------------------------------ */
    new MutationObserver(() => {
        buildRevenueChart(
            (raw.revenueLabels || []).slice(-currentPeriod),
            (raw.revenueData   || []).slice(-currentPeriod)
        );
        buildStatusChart();
        setProgressWidths();  // Re-apply widths after theme change (DOM unchanged but safe)
    }).observe(document.body, { attributes: true, attributeFilter: ['class'] });

    /* ------------------------------------------------
       Optional: resize observer to redraw charts on container resize
    ------------------------------------------------ */
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (revenueChart) revenueChart.resize();
            if (statusChart) statusChart.resize();
        }, 200);
    });

    /* ------------------------------------------------
       Initialization
    ------------------------------------------------ */
    buildRevenueChart(raw.revenueLabels, raw.revenueData);
    buildStatusChart();
    setProgressWidths();  // <-- CRITICAL: set progress bar widths on load
});
</script>
@endpush