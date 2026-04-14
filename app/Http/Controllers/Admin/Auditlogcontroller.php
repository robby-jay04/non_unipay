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
     * For HTML requests -> returns view
     * For JSON requests -> returns JsonResponse
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
            'per_page'   => 'nullable|integer|min:10|max:100',
        ]);

        $logs = AuditLog::with('admin:id,name,email')
            ->when($request->search, fn($q, $s) =>
                $q->where(function ($q) use ($s) {
                    $q->where('description', 'like', "%{$s}%")
                      ->orWhere('action_type', 'like', "%{$s}%")
                      ->orWhere('ip_address', 'like', "%{$s}%");
                })
            )
            ->when($request->module,   fn($q, $v) => $q->forModule($v))
            ->when($request->severity, fn($q, $v) => $q->ofSeverity($v))
            ->when($request->action,   fn($q, $v) => $q->where('action_type', $v))
            ->when($request->admin_id, fn($q, $v) => $q->where('admin_user_id', $v))
            ->dateRange($request->date_from, $request->date_to)
            ->latest('created_at')
            ->paginate($request->integer('per_page', 20));

        if ($request->wantsJson()) {
            return response()->json($logs);
        }

        return view('admin.audit-logs.index', compact('logs'));
    }

    /**
     * GET /admin/audit-logs/{id}
     * For HTML -> returns show view
     * For JSON -> returns JsonResponse
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
     * Returns JSON summary for dashboard stat cards.
     */
    public function stats(): JsonResponse
    {
        $today = now()->startOfDay();

        return response()->json([
            'events_today'        => AuditLog::where('created_at', '>=', $today)->count(),
            'fee_mods_today'      => AuditLog::where('created_at', '>=', $today)->where('module', 'LoanFees')->count(),
            'high_severity_today' => AuditLog::where('created_at', '>=', $today)->where('severity', 'high')->count(),
            'failed_logins_today' => AuditLog::where('created_at', '>=', $today)->where('action_type', 'auth.fail')->count(),
            'active_admins_today' => AuditLog::where('created_at', '>=', $today)->distinct('admin_user_id')->count('admin_user_id'),
        ]);
    }

    /**
     * GET /admin/audit-logs/export
     * Streams a CSV download.
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
                ->when($request->severity,  fn($q, $v) => $q->ofSeverity($v))
                ->dateRange($request->date_from, $request->date_to)
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