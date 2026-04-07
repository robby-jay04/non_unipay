@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold" style="color: var(--text-primary);">Dashboard Overview</h2>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-5">
    <div class="col-md-3 col-sm-6">
        <a href="{{ route('admin.payments') }}" class="stat-card d-flex flex-column h-100 text-decoration-none" style="background: var(--bg-main); border-radius: 1.5rem; padding: 1.5rem; box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-semibold" style="color: var(--text-muted);">Total Revenue</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(15, 60, 145, 0.1); width: 48px; height: 48px; transition: all 0.3s ease;">
                    <i class="fas fa-money-bill-wave" style="color: #0f3c91; font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: var(--primary-color, #0f3c91);">₱{{ number_format($stats['total_revenue'], 2) }}</h3>
                <small class="text-muted">All time</small>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6">
        <a href="{{ route('admin.payments', ['status' => 'pending']) }}" class="stat-card d-flex flex-column h-100 text-decoration-none" style="background: var(--bg-main); border-radius: 1.5rem; padding: 1.5rem; box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-semibold" style="color: var(--text-muted);">Pending Payments</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(244, 180, 20, 0.1); width: 48px; height: 48px; transition: all 0.3s ease;">
                    <i class="fas fa-clock" style="color: #f4b414; font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: #f4b414;">{{ $stats['pending_payments'] }}</h3>
                <small class="text-muted">Awaiting confirmation</small>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6">
        <a href="{{ route('admin.reports.clearances') }}" class="stat-card d-flex flex-column h-100 text-decoration-none" style="background: var(--bg-main); border-radius: 1.5rem; padding: 1.5rem; box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-semibold" style="color: var(--text-muted);">Cleared Students</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(76, 175, 80, 0.1); width: 48px; height: 48px; transition: all 0.3s ease;">
                    <i class="fas fa-check-circle" style="color: #4caf50; font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: #4caf50;">{{ $stats['cleared_students'] }}</h3>
                <small class="text-muted">Ready for exams</small>
            </div>
        </a>
    </div>

    <div class="col-md-3 col-sm-6">
        <a href="{{ route('admin.students') }}" class="stat-card d-flex flex-column h-100 text-decoration-none" style="background: var(--bg-main); border-radius: 1.5rem; padding: 1.5rem; box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-uppercase small fw-semibold" style="color: var(--text-muted);">Total Students</span>
                <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center" style="background: rgba(15, 60, 145, 0.1); width: 48px; height: 48px; transition: all 0.3s ease;">
                    <i class="fas fa-users" style="color: #0f3c91; font-size: 24px;"></i>
                </div>
            </div>
            <div>
                <h3 class="fw-bold mb-0" style="color: #0f3c91;">{{ $stats['total_students'] }}</h3>
                <small class="text-muted">Registered</small>
            </div>
        </a>
    </div>
</div>

<!-- Additional Mini Stats Row -->
<div class="row g-4 mb-5">
    <div class="col-md-3 col-sm-6">
        <div class="d-flex align-items-center gap-3 p-3 rounded-4 h-100" style="background: var(--bg-main); box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(15,60,145,0.1);">
                <i class="fas fa-calendar-day" style="color: #0f3c91; font-size: 20px;"></i>
            </div>
            <div>
                <span class="small text-uppercase" style="color: var(--text-muted);">Today's Revenue</span>
                <h5 class="fw-bold mb-0" style="color: #0f3c91;">₱{{ number_format($stats['today_revenue'] ?? 0, 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="d-flex align-items-center gap-3 p-3 rounded-4 h-100" style="background: var(--bg-main); box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(244,180,20,0.1);">
                <i class="fas fa-calendar-alt" style="color: #f4b414; font-size: 20px;"></i>
            </div>
            <div>
                <span class="small text-uppercase" style="color: var(--text-muted);">This Month</span>
                <h5 class="fw-bold mb-0" style="color: #f4b414;">₱{{ number_format($stats['monthly_revenue'] ?? 0, 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="d-flex align-items-center gap-3 p-3 rounded-4 h-100" style="background: var(--bg-main); box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(76,175,80,0.1);">
                <i class="fas fa-chart-line" style="color: #4caf50; font-size: 20px;"></i>
            </div>
            <div>
                <span class="small text-uppercase" style="color: var(--text-muted);">Average Payment</span>
                <h5 class="fw-bold mb-0" style="color: #4caf50;">₱{{ number_format($stats['average_payment'] ?? 0, 2) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="d-flex align-items-center gap-3 p-3 rounded-4 h-100" style="background: var(--bg-main); box-shadow: var(--card-shadow); transition: all 0.3s ease;">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(15,60,145,0.1);">
                <i class="fas fa-trophy" style="color: #0f3c91; font-size: 20px;"></i>
            </div>
            <div>
                <span class="small text-uppercase" style="color: var(--text-muted);">Top Student</span>
                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 120px; color: var(--text-primary);">{{ $stats['top_student'] ?? 'N/A' }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="card-header border-0 py-3 px-4" style="background: var(--bg-main);">
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">Revenue Trend (Last 7 Days)</h5>
            </div>
            <div class="card-body p-4">
                <canvas id="revenueChart" style="width:100%; height:300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="card-header border-0 py-3 px-4" style="background: var(--bg-main);">
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">Payment Status</h5>
            </div>
            <div class="card-body p-4 d-flex justify-content-center align-items-center" style="height: 300px;">
                <canvas id="statusChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Clearance Activity -->
<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
            <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="background: var(--bg-main);">
                <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">Recently Cleared Students</h5>
                <a href="{{ route('admin.reports.clearances') }}" class="btn-view-all btn btn-sm rounded-pill px-3">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 dashboard-table">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Student</th>
                                <th class="py-3">Course</th>
                                <th class="py-3">Year</th>
                                <th class="py-3">Cleared At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_cleared'] ?? [] as $cleared)
                            <tr>
                                <td class="px-4 py-3">{{ $cleared->student->user->name }}</td>
                                <td class="py-3">{{ $cleared->student->course }}</td>
                                <td class="py-3">{{ $cleared->student->year_level }}</td>
                                <td class="py-3">{{ $cleared->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4">No recent clearances</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-main);">
    <div class="card-header border-0 py-3 px-4" style="background: var(--bg-main);">
        <h5 class="mb-0 fw-bold" style="color: var(--text-primary);">Recent Payments</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 dashboard-table">
                <thead>
                    <tr>
                        <th class="px-4 py-3">Student</th>
                        <th class="py-3">Student No</th>
                        <th class="py-3">Amount</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 pe-4">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['recent_payments'] as $payment)
                    <tr>
                        <td class="px-4 py-3 fw-medium">{{ $payment->student->user->name }}</td>
                        <td class="py-3">{{ $payment->student->student_no }}</td>
                        <td class="py-3 fw-semibold" style="color: #0f3c91;">₱{{ number_format($payment->total_amount, 2) }}</td>
                        <td class="py-3">
                            @if($payment->status == 'paid')
                                <span class="badge-paid">Paid</span>
                            @elseif($payment->status == 'pending')
                                <span class="badge-pending">Pending</span>
                            @else
                                <span class="badge-failed">Failed</span>
                            @endif
                        </td>
                        <td class="py-3 pe-4">{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===== DARK MODE TABLE OVERRIDES (FIXES ROWS) ===== */
    .dashboard-table,
    .dashboard-table tbody,
    .dashboard-table tr,
    .dashboard-table td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
    }
    .dashboard-table thead th {
        background-color: var(--table-header-bg);
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
        font-weight: 600;
    }
    .dashboard-table tbody tr {
        border-bottom: 1px solid var(--table-row-border);
        transition: background 0.2s;
        background-color: var(--bg-main);
    }
    .dashboard-table tbody tr:hover {
        background-color: var(--hover-bg) !important;
    }
    .dashboard-table tbody td {
        background-color: var(--bg-main);
        color: var(--text-secondary);
        border-bottom: none;
    }
    .dashboard-table tbody td:first-child {
        color: var(--text-primary);
        font-weight: 500;
    }
    /* Empty state text */
    .text-center.py-4 {
        color: var(--text-muted);
    }

    /* Status badges (dark mode friendly) */
    .badge-paid, .badge-pending, .badge-failed {
        font-weight: 600;
        padding: 0.45rem 1rem;
        border-radius: 40px;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        gap: 0.4rem;
    }
    .badge-paid {
        background: rgba(76, 175, 80, 0.15);
        color: #2e7d32;
    }
    .badge-pending {
        background: rgba(244, 180, 20, 0.15);
        color: #b26a00;
    }
    .badge-failed {
        background: rgba(220, 53, 69, 0.15);
        color: #a71d2a;
    }
    body.dark .badge-paid {
        background: rgba(76, 175, 80, 0.25);
        color: #81c784;
    }
    body.dark .badge-pending {
        background: rgba(244, 180, 20, 0.25);
        color: #ffd54f;
    }
    body.dark .badge-failed {
        background: rgba(220, 53, 69, 0.25);
        color: #ef9a9a;
    }

    /* View All button */
    .btn-view-all {
        background: rgba(15, 60, 145, 0.1);
        color: #0f3c91;
        transition: all 0.2s;
    }
    body.dark .btn-view-all {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }
    .btn-view-all:hover {
        background: #0f3c91;
        color: white;
        transform: translateY(-1px);
    }
    body.dark .btn-view-all:hover {
        background: #3b82f6;
        color: white;
    }

    /* Stat card hover effects */
    .stat-card {
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease, background 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: #0f3c91;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .stat-card:hover::before {
        opacity: 1;
    }
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 35px rgba(0, 0, 0, 0.12) !important;
        background-color: var(--hover-bg) !important;
    }
    .stat-card:hover .stat-icon {
        transform: scale(1.05);
        background: rgba(15, 60, 145, 0.2) !important;
    }
    .stat-card:hover .stat-icon i {
        color: #0f3c91 !important;
    }

    /* Mini stat cards hover */
    .row.g-4.mb-5 .col-md-3 > div {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
    }
    .row.g-4.mb-5 .col-md-3 > div:hover {
        transform: translateY(-4px);
        background-color: var(--hover-bg) !important;
        box-shadow: 0 8px 20px rgba(15, 60, 145, 0.15) !important;
    }
    .row.g-4.mb-5 .col-md-3 > div:hover .rounded-circle {
        transform: scale(1.05);
        transition: transform 0.3s ease;
    }
    .row.g-4.mb-5 .col-md-3 > div:hover .rounded-circle i {
        color: #0f3c91 !important;
    }

    /* Chart cards hover */
    .card.border-0.shadow-sm.rounded-4 {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .card.border-0.shadow-sm.rounded-4:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(15, 60, 145, 0.15) !important;
        background-color: var(--hover-bg) !important;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Store PHP data as JSON --}}
<script id="dashboard-data" type="application/json">
{
    "revenueLabels": {!! json_encode($stats['revenue_labels'] ?? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!},
    "revenueData": {!! json_encode($stats['revenue_data'] ?? [0,0,0,0,0,0,0]) !!},
    "paidCount": {{ $stats['paid_count'] ?? 0 }},
    "pendingCount": {{ $stats['pending_count'] ?? 0 }},
    "failedCount": {{ $stats['failed_count'] ?? 0 }}
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let revenueChart, statusChart;
    const data = JSON.parse(document.getElementById('dashboard-data').textContent);

    function getThemeColors() {
        const isDark = document.body.classList.contains('dark');
        return {
            gridColor: isDark ? '#334155' : '#e2e8f0',
            textColor: isDark ? '#cbd5e1' : '#475569',
            revenueLineColor: '#0f3c91',
            revenueFillColor: isDark ? 'rgba(59,130,246,0.1)' : 'rgba(15,60,145,0.1)'
        };
    }

    function initCharts() {
        const colors = getThemeColors();
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const statusCtx = document.getElementById('statusChart').getContext('2d');

        if (revenueChart) revenueChart.destroy();
        if (statusChart) statusChart.destroy();

        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: data.revenueLabels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: data.revenueData,
                    borderColor: colors.revenueLineColor,
                    backgroundColor: colors.revenueFillColor,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        grid: { color: colors.gridColor },
                        ticks: { color: colors.textColor }
                    },
                    x: {
                        grid: { color: colors.gridColor },
                        ticks: { color: colors.textColor }
                    }
                }
            }
        });

        statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Pending', 'Failed'],
                datasets: [{
                    data: [data.paidCount, data.pendingCount, data.failedCount],
                    backgroundColor: ['#4caf50', '#f4b414', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: colors.textColor } }
                }
            }
        });
    }

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                initCharts();
            }
        });
    });
    observer.observe(document.body, { attributes: true });

    initCharts();
});
</script>
@endpush