<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SuperAdminController extends Controller
{
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

        return view('admin.superadmin.admins.index', compact('admins', 'search'));
    }

    public function create()
    {
        return view('admin.superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'role'     => ['required', 'in:admin,superadmin'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.superadmin.admins.index')
            ->with('success', 'Admin account created successfully.');
    }

    public function edit(User $admin)
{
    if ($admin->id === auth()->id()) {
        return redirect()->route('admin.superadmin.admins.index')
            ->with('error', 'You cannot edit your own account here.');
    }

    return view('admin.superadmin.admins.edit', ['user' => $admin]);
}

public function update(Request $request, User $admin)
{
    if ($admin->id === auth()->id()) {
        return redirect()->route('admin.superadmin.admins.index')
            ->with('error', 'You cannot edit your own account here.');
    }

    $validated = $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'email', 'unique:users,email,' . $admin->id],
        'role'     => ['required', 'in:admin,superadmin'],
        'password' => ['nullable', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
    ]);

    $admin->name  = $validated['name'];
    $admin->email = $validated['email'];
    $admin->role  = $validated['role'];

    if (!empty($validated['password'])) {
        $admin->password = Hash::make($validated['password']);
    }

    $admin->save();

    return redirect()->route('admin.superadmin.admins.index')
        ->with('success', 'Admin account updated successfully.');
}

public function destroy(User $admin)
{
    if ($admin->id === auth()->id()) {
        return redirect()->route('admin.superadmin.admins.index')
            ->with('error', 'You cannot delete your own account.');
    }

    $admin->delete();

    return redirect()->route('admin.superadmin.admins.index')
        ->with('success', 'Admin account deleted successfully.');
}
}