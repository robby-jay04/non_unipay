<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Models\Student;
use Illuminate\Support\Facades\Password;
use App\Services\AuditLogger;

class AuthController extends Controller
{
    // -------------------------------
    // Show Login Form (Web)
    // -------------------------------
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // -------------------------------
    // Web Login for Admin Only
    // Students cannot log in via web
    // -------------------------------
    public function loginWeb(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Case 1: User not found
        if (!$user) {
            app(AuditLogger::class)->log(
                actionType: 'auth.fail',
                module: 'AdminAuth',
                description: "Failed admin login - email not found: {$request->email}",
                severity: 'medium'
            );

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Case 2: Not an admin or superadmin
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            app(AuditLogger::class)->log(
                actionType: 'auth.fail',
                module: 'AdminAuth',
                description: "Failed admin login - non-admin role ({$user->role}) attempted: {$request->email}",
                severity: 'medium'
            );

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Case 3: Account inactive
        if (!$user->isActive()) {
            app(AuditLogger::class)->log(
                actionType: 'auth.fail',
                module: 'AdminAuth',
                description: "Failed admin login - inactive account: {$request->email}",
                severity: 'medium'
            );

            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact the super admin.',
            ])->onlyInput('email');
        }

        // Case 4: Password incorrect
        if (!Auth::attempt($credentials)) {
            app(AuditLogger::class)->log(
                actionType: 'auth.fail',
                module: 'AdminAuth',
                description: "Failed admin login - incorrect password for: {$request->email}",
                entity: $user,   // to capture admin_user_id
                severity: 'medium'
            );

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // ✅ Success: log successful admin login
        app(AuditLogger::class)->log(
            actionType: 'auth.success',
            module: 'AdminAuth',
            description: "Admin {$user->email} logged in successfully",
            entity: $user,
            severity: 'low'
        );

        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    // -------------------------------
    // Web Logout
    // -------------------------------
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // -------------------------------
    // API Login (Sanctum) – for students
    // -------------------------------
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Case 1: User not found
        if (!$user) {
            app(AuditLogger::class)->log(
                actionType: 'auth.fail',
                module: 'StudentAuth',
                description: "Failed student login - email not found: {$request->email}",
                severity: 'medium'
            );

            return response()->json(['message' => 'No account found with that email address.'], 401);
        }

        // Handle imported accounts (no password stored)
        $passwordValid = $user->password
            ? Hash::check($request->password, $user->password)
            : ($request->password === 'password123');

        if (!$passwordValid) {
            // Log failed attempt with student_id if the user is a student
            $studentId = $user->isStudent() ? $user->student?->id : null;
            app(AuditLogger::class)->log(
                actionType: 'auth.fail',
                module: 'StudentAuth',
                description: "Failed student login for email: {$request->email}",
                studentId: $studentId,
                severity: 'medium'
            );

            return response()->json(['message' => 'Incorrect password. Please try again.'], 401);
        }

        // 🔒 Block admin login via mobile API
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return response()->json([
                'message' => 'Admin accounts cannot log in to the mobile app. Please use the web admin panel.'
            ], 403);
        }

        // Check student confirmation
        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student || !$student->is_confirmed) {
                return response()->json([
                    'message' => 'Your account is pending admin approval.'
                ], 403);
            }
        }

        // ✅ Success: log successful student login
        app(AuditLogger::class)->log(
            actionType: 'auth.success',
            module: 'StudentAuth',
            description: "Student {$user->email} logged in successfully",
            studentId: $user->student?->id,
            severity: 'low'
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }

    // -------------------------------
    // API Logout (Sanctum token)
    // -------------------------------
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    // -------------------------------
    // Student Registration
    // -------------------------------
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|confirmed|min:6',
            'student_no'  => 'required|string|unique:students,student_no',
            'course'      => 'required|string',
            'year_level'  => 'required',
            'contact'     => 'required|string|unique:students,contact',
            'semester'    => 'required|string',
            'school_year' => 'required|string',
        ], [
            'email.unique'       => 'This email is already registered.',
            'student_no.unique'  => 'This student number is already registered.',
            'contact.unique'     => 'This contact number is already used by another student.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min'       => 'Password must be at least 6 characters.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'student',
        ]);

        Student::create([
            'user_id'      => $user->id,
            'student_no'   => $validated['student_no'],
            'course'       => $validated['course'],
            'year_level'   => $validated['year_level'],
            'contact'      => $validated['contact'],
            'semester'     => $validated['semester'],
            'school_year'  => $validated['school_year'],
            'is_confirmed' => false,
        ]);

        return response()->json([
            'success' => true,
            'status'  => 'pending',
            'message' => 'Registration successful. Please wait for admin approval.',
        ], 201);
    }

    // -------------------------------
    // Change Password (API)
    // -------------------------------
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if ($user->password && !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors'  => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }

    // -------------------------------
    // Get Authenticated User (API)
    // -------------------------------
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // -------------------------------
    // Show Password Reset Form (Web)
    // -------------------------------
    public function showResetForm(Request $request, $token = null)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withErrors([
                'email' => 'Email not found in our system',
            ]);
        }

        return view('emails.password-reset', [
            'token' => $token,
            'email' => $email,
        ]);
    }
}