<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use App\Models\Student;
use Illuminate\Support\Facades\Password;

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
  use App\Models\Student;
     use Illuminate\Support\Facades\Password;


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
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        // Allow both admin and superadmin to log in via web
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'student') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Students are not allowed to log in via web. Please use the mobile app.',
            ])->onlyInput('email');
        }
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
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
    // API Login (Sanctum)
    // -------------------------------
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'No account found with that email address.'
        ], 401);
    }

    // Handle accounts without a password (imported data)
    if (!$user->password) {
        if ($request->password !== 'password123') {
            return response()->json([
                'message' => 'Incorrect password. Please try again.'
            ], 401);
        }
    } else {
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect password. Please try again.'
            ], 401);
        }
    }

    // 🔒 BLOCK ADMIN LOGIN VIA MOBILE APP
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

    // Create token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
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
    


// ...

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
        'name.required'      => 'Full name is required.',
        'email.required'     => 'Email is required.',
        'email.email'        => 'Please enter a valid email address.',
        'student_no.required'=> 'Student number is required.',
        'course.required'    => 'Course is required.',
        'year_level.required'=> 'Year level is required.',
        'contact.required'   => 'Contact number is required.',
        'semester.required'  => 'Semester is required.',
        'school_year.required'=> 'School year is required.',
    ]);

    $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role'     => 'student',
    ]);

    Student::create([
        'user_id'    => $user->id,
        'student_no' => $validated['student_no'],
        'course'     => $validated['course'],
        'year_level' => $validated['year_level'],
        'contact'    => $validated['contact'],
        'semester'   => $validated['semester'],
        'school_year'=> $validated['school_year'],
        'is_confirmed' => false,
    ]);

    return response()->json([
        'success' => true,
        'status'  => 'pending',
        'message' => 'Registration successful. Please wait for admin approval.',
    ], 201);
}

public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password'     => 'required|min:8|confirmed',
    ]);
 
    $user = $request->user();
 
    // Handle accounts that were imported without a real password
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
    // Get Authenticated User
    // -------------------------------
    public function me(Request $request)
    {


        return response()->json($request->user());
    }
 

public function showResetForm(Request $request, $token = null)
{
    // Validate email query parameter
    $request->validate([
        'email' => 'required|email',
    ]);

    $email = $request->query('email');

    // Check if user exists
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        return redirect()->back()->withErrors([
            'email' => 'Email not found in our system',
        ]);
    }

    // Return Blade view instead of JSON
    return view('emails.password-reset', [
        'token' => $token,
        'email' => $email,
    ]);
}
}
