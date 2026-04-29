<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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

    $user = User::findOrFail(auth()->id());

    // Delete old Cloudinary image if exists
    if ($user->profile_picture_public_id) {
        Cloudinary::destroy($user->profile_picture_public_id);
    }

    $uploadedFile = $request->file('profile_picture');

    $result = Cloudinary::upload($uploadedFile->getRealPath(), [
        'folder'    => 'admin_profiles',
        'public_id' => 'admin_' . $user->id . '_' . time(),
        'overwrite' => true,
    ]);

    $user->profile_picture = $result->getSecurePath();
    $user->profile_picture_public_id = $result->getPublicId();
    $user->save();

    // Your existing audit log
    $this->auditLogger->log(
        actionType: 'admin.profile.picture',
        module: 'Profile',
        description: "Admin {$user->email} updated their profile picture with Cloudinary",
        entity: $user,
        severity: 'low'
    );

    return response()->json([
        'success' => true,
        'url'     => $user->profile_picture,
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