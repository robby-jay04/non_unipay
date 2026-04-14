<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    // Severity map — action prefix → severity
    private const SEVERITY_MAP = [
        'fee.'         => 'high',
        'payment.'     => 'high',
        'delete.'      => 'high',
        'user.delete'  => 'high',
        'user.create'  => 'medium',
        'user.role'    => 'medium',
        'auth.fail'    => 'medium',
        'export.'      => 'low',
        'auth.success' => 'low',
        'report.'      => 'low',
    ];

    public function __construct(private Request $request) {}

    /**
     * Log an auditable action.
     *
     * @param string      $actionType  e.g. 'fee.update', 'payment.reverse'
     * @param string      $module      e.g. 'LoanFees', 'Payments'
     * @param string      $description Human-readable summary
     * @param array|null  $oldValue    State before the action
     * @param array|null  $newValue    State after the action
     * @param object|null $entity      Eloquent model being acted on
     * @param string|null $severity    Override auto-detected severity
     * @param int|null    $studentId   Override student ID (for API calls where auth is not the actor)
     */
    public function log(
        string $actionType,
        string $module,
        string $description,
        ?array $oldValue = null,
        ?array $newValue = null,
        ?object $entity = null,
        ?string $severity = null,
        ?int $studentId = null,
    ): AuditLog {
        $data = [
            'action_type'   => $actionType,
            'module'        => $module,
            'entity_type'   => $entity ? get_class($entity) : null,
            'entity_id'     => $entity?->getKey(),
            'old_value'     => $oldValue,
            'new_value'     => $newValue,
            'description'   => $description,
            'severity'      => $severity ?? $this->detectSeverity($actionType),
            'ip_address'    => $this->request->ip(),
            'user_agent'    => $this->request->userAgent(),
            'session_id'    => session()->getId(),
            'url'           => $this->request->fullUrl(),
            'http_method'   => $this->request->method(),
            'created_at'    => now(),
            'admin_user_id' => null,
            'student_id'    => null,
        ];

        // Determine actor (admin or student) from authentication
        $user = Auth::user();
        if ($user) {
            if (in_array($user->role, ['admin', 'superadmin'])) {
                $data['admin_user_id'] = $user->id;
            } elseif ($user->role === 'student' && $user->student) {
                $data['student_id'] = $user->student->id;
            }
        }

        // Override student_id if explicitly provided (e.g., when logging from a webhook)
        if ($studentId !== null) {
            $data['student_id'] = $studentId;
            // If a student ID is provided and no admin, ensure admin_user_id is null
            if (empty($data['admin_user_id'])) {
                $data['admin_user_id'] = null;
            }
        }

        // If entity is a Student model, also set student_id (useful for model events)
        if ($entity instanceof \App\Models\Student) {
            $data['student_id'] = $entity->id;
        }

        return AuditLog::create($data);
    }

    /**
     * Convenience: capture old/new from an Eloquent model's dirty state.
     * Call BEFORE saving the model.
     */
    public function logModelChange(
        string $actionType,
        string $module,
        string $description,
        \Illuminate\Database\Eloquent\Model $model,
        ?string $severity = null,
    ): AuditLog {
        $old = collect($model->getOriginal())
            ->only(array_keys($model->getDirty()))
            ->toArray();

        $new = $model->getDirty();

        return $this->log($actionType, $module, $description, $old, $new, $model, $severity);
    }

    private function detectSeverity(string $actionType): string
    {
        foreach (self::SEVERITY_MAP as $prefix => $sev) {
            if (str_starts_with($actionType, $prefix)) {
                return $sev;
            }
        }
        return 'low';
    }
}