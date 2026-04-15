<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends Controller
{
    // ── Helper to log actions ─────────────────────────────────────────────
    private function audit(string $action, string $description, array $old = null, array $new = null, string $severity = 'medium')
    {
        AuditLog::create([
            'admin_id'    => auth()->id(),
            'action_type' => $action,
            'module'      => 'Admin',
            'description' => $description,
            'old_value'   => $old,
            'new_value'   => $new,
            'severity'    => $severity,
            'ip_address'  => request()->ip(),
            'http_method' => request()->method(),
            'url'         => request()->fullUrl(),
            'session_id'  => session()->getId(),
        ]);
    }

    public function index(Request $request)
    {
        $search = $request->query('search');

        $admins = User::whereIn('role', ['admin', 'superadmin'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('role', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->appends($request->query());

        if ($request->has('ajax')) {
            $html = view('admin.superadmin.admins.partials.admins_table', compact('admins', 'search'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.superadmin.admins.index', compact('admins', 'search'));
    }

    public function create()
    {
        return redirect()->route('admin.superadmin.admins.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('createBag', [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role'     => ['required', 'in:admin,superadmin'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        $admin = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'role'      => $validated['role'],
            'is_active' => $request->has('is_active'),
            'password'  => Hash::make($validated['password']),
        ]);

        $this->audit(
            'CREATE',
            "Created admin account: {$admin->email} with role {$admin->role}",
            null,
            ['name' => $admin->name, 'email' => $admin->email, 'role' => $admin->role, 'is_active' => $admin->is_active],
            'high'
        );

        return redirect()->route('admin.superadmin.admins.index')
            ->with('success', 'Admin account created successfully.');
    }

    public function edit(User $admin)
    {
        return redirect()->route('admin.superadmin.admins.index');
    }

    public function update(Request $request, User $admin)
    {
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.superadmin.admins.index')
                ->with('error', 'You cannot edit your own account here.');
        }

        $validated = $request->validateWithBag('editBag', [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email,' . $admin->id],
            'role'     => ['required', 'in:admin,superadmin'],
            'password' => ['nullable', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        // Capture old values before updating
        $old = [
            'name'      => $admin->name,
            'email'     => $admin->email,
            'role'      => $admin->role,
            'is_active' => $admin->is_active,
        ];

        $admin->name      = $validated['name'];
        $admin->email     = $validated['email'];
        $admin->role      = $validated['role'];
        $admin->is_active = $request->has('is_active');

        $passwordChanged = false;
        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
            $passwordChanged = true;
        }

        $admin->save();

        $new = [
            'name'             => $admin->name,
            'email'            => $admin->email,
            'role'             => $admin->role,
            'is_active'        => $admin->is_active,
            'password_changed' => $passwordChanged,
        ];

        $this->audit(
            'UPDATE',
            "Updated admin account: {$admin->email}" . ($passwordChanged ? ' (password changed)' : ''),
            $old,
            $new,
            'high'
        );

        return redirect()->route('admin.superadmin.admins.index')
            ->with('success', 'Admin account updated successfully.');
    }

    public function destroy(User $admin)
    {
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.superadmin.admins.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $old = [
            'name'      => $admin->name,
            'email'     => $admin->email,
            'role'      => $admin->role,
            'is_active' => $admin->is_active,
        ];

        $admin->delete();

        $this->audit(
            'DELETE',
            "Deleted admin account: {$old['email']} (role: {$old['role']})",
            $old,
            null,
            'high'
        );

        return redirect()->route('admin.superadmin.admins.index')
            ->with('success', 'Admin account deleted successfully.');
    }
}