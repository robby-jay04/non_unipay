<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    protected AuditLogger $auditLogger;

    public function __construct(AuditLogger $auditLogger)
    {
        $this->auditLogger = $auditLogger;
    }

    /** Upload profile picture */
    public function updatePicture(Request $request)
    {
        $request->validate(['profile_picture' => 'required|image|max:2048']);

        /** @var User $user */
        $user = User::findOrFail(auth()->id());

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile-pictures', 'public');

        $user->profile_picture = $path;
        $user->save();

        $this->auditLogger->log(
            actionType: 'admin.profile.picture',
            module: 'Profile',
            description: "Admin {$user->email} updated their profile picture",
            entity: $user,
            severity: 'low'
        );

        return response()->json([
            'success' => true,
            'url'     => Storage::url($path),
        ]);
    }

    /** Update name and/or email */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = User::findOrFail(auth()->id());

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $oldEmail = $user->email;
        $oldName  = $user->name;
        $changes  = [];

        if ($request->email !== $oldEmail) {
            $changes['email'] = ['from' => $oldEmail, 'to' => $request->email];
        }
        if ($request->name !== $oldName) {
            $changes['name'] = ['from' => $oldName, 'to' => $request->name];
        }

        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        if (!empty($changes)) {
            $this->auditLogger->log(
                actionType: 'admin.profile.update',
                module: 'Profile',
                description: 'Admin updated profile: ' . collect($changes)
                    ->map(fn ($c, $k) => "{$k} changed from '{$c['from']}' to '{$c['to']}'")
                    ->implode(', '),
                oldValue: ['name' => $oldName,       'email' => $oldEmail],
                newValue: ['name' => $request->name, 'email' => $request->email],
                entity: $user,
                severity: 'medium'
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
        ]);
    }

    /** Change password */
    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = User::findOrFail(auth()->id());

        $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        $this->auditLogger->log(
            actionType: 'admin.profile.password',
            module: 'Profile',
            description: "Admin {$user->email} changed their password",
            entity: $user,
            severity: 'medium'
        );

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }
}