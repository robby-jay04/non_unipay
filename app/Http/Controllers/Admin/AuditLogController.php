<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * GET /admin/audit-logs
     * Returns paginated, filtered log entries.
     */
    public function index(Request $request): View|JsonResponse
    {
        $request->validate([
            'search'     => 'nullable|string|max:100',
            'module'     => 'nullable|string|max:60',
            'action'     => 'nullable|string|max:60',
            'severity'   => 'nullable|in:low,medium,high',
            'admin_id'   => 'nullable|integer',
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date',
            'per_page'   => 'nullable|integer|min:5|max:100',
        ]);

        // Default per page = 10
        $perPage = $request->integer('per_page', 10);

        $logs = AuditLog::with(['admin:id,name,email', 'student.user'])
            ->when($request->search, fn($q, $s) =>
                $q->where(function ($q) use ($s) {
                    $q->where('description', 'like', "%{$s}%")
                      ->orWhere('action_type', 'like', "%{$s}%")
                      ->orWhere('ip_address', 'like', "%{$s}%");
                })
            )
            ->when($request->module,   fn($q, $v) => $q->where('module', $v))
            ->when($request->severity, fn($q, $v) => $q->where('severity', $v))
            ->when($request->action,   fn($q, $v) => $q->where('action_type', $v))
            ->when($request->admin_id, fn($q, $v) => $q->where('admin_user_id', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to,   fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest('created_at')
            ->paginate($perPage)
            ->appends($request->query()); // Preserve filters in pagination links

        if ($request->wantsJson()) {
            // paginate() already returns JSON with links, current_page, etc.
            return response()->json($logs);
        }

        return view('admin.audit-logs.index', compact('logs'));
    }

    /**
     * GET /admin/audit-logs/{id}
     */
    public function show(AuditLog $auditLog, Request $request): View|JsonResponse
    {
        $auditLog->load('admin:id,name,email');

        if ($request->wantsJson()) {
            return response()->json($auditLog);
        }

        return view('admin.audit-logs.show', compact('auditLog'));
    }

    /**
     * GET /admin/audit-logs/stats
     */
    public function stats(): JsonResponse
{
    $today = now()->startOfDay();

    return response()->json([
        'events_today'              => AuditLog::whereDate('created_at', '>=', $today)->count(),
        'fee_mods_today'            => AuditLog::whereDate('created_at', '>=', $today)->where('module', 'Fee')->count(),
        'high_severity_today'       => AuditLog::whereDate('created_at', '>=', $today)->where('severity', 'high')->count(),
        
        // Admin failed logins (admin_user_id not null)
        'admin_failed_logins_today' => AuditLog::whereDate('created_at', '>=', $today)
                                        ->where('action_type', 'auth.fail')
                                        ->whereNotNull('admin_user_id')
                                        ->count(),
        
        // Student failed logins (student_id not null)
        'student_failed_logins_today' => AuditLog::whereDate('created_at', '>=', $today)
                                        ->where('action_type', 'auth.fail')
                                        ->whereNotNull('student_id')
                                        ->count(),
        
        'active_admins_today'       => AuditLog::whereDate('created_at', '>=', $today)
                                        ->distinct('admin_user_id')->count('admin_user_id'),
    ]);
}

    /**
     * GET /admin/audit-logs/export
     */
    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to'   => 'nullable|date',
            'severity'  => 'nullable|in:low,medium,high',
        ]);

        $fileName = 'audit_log_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Timestamp', 'Admin', 'Action', 'Module', 'Description', 'Severity', 'IP', 'Session']);

            AuditLog::with('admin:id,name')
                ->when($request->severity,  fn($q, $v) => $q->where('severity', $v))
                ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
                ->when($request->date_to,   fn($q, $v) => $q->whereDate('created_at', '<=', $v))
                ->latest()
                ->chunk(500, function ($logs) use ($handle) {
                    foreach ($logs as $l) {
                        fputcsv($handle, [
                            $l->id,
                            $l->created_at,
                            $l->admin?->name,
                            $l->action_type,
                            $l->module,
                            $l->description,
                            $l->severity,
                            $l->ip_address,
                            $l->session_id,
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}