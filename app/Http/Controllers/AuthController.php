<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
  use App\Models\Student;

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

            // Only allow admins to log in via web
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'student') {
                Auth::logout(); // Log them out immediately
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
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    // Allow login if password is empty in DB and user enters "password123"
    if (!$user->password) {
        if ($request->password !== 'password123') {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }
    } else {
        // Normal Hash check
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }
    }

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
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'student_no' => 'required|string|unique:students',
        'course' => 'required|string',
        'year_level' => 'required|integer',
        'contact' => 'required|string',
    ]);

    // Create the User
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'student',
    ]);

    // Create the Student linked to the user
    $student = Student::create([
        'user_id' => $user->id,
        'student_no' => $validated['student_no'],
        'course' => $validated['course'],
        'year_level' => $validated['year_level'],
        'contact' => $validated['contact'],
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'student_no' => $student->student_no,
            'course' => $student->course,
            'year_level' => $student->year_level,
            'contact' => $student->contact,
        ],
    ], 201);
}



      // -------------------------------
    // Get Authenticated User
    // -------------------------------
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
