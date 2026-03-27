<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Send password reset link via email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email not found in our system',
            ], 404);
        }

        try {
            $token = Password::createToken($user);

            $webUrl = url("/password/reset/{$token}?email=" . urlencode($user->email));

            Mail::to($user->email)->send(new ResetPasswordMail($webUrl));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email',
            ]);
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset email. Please try again later.'
            ], 500);
        }
    }

    /**
     * Handle clickable link from email
     * Shows the web reset form directly
     */
    public function redirectToMobile($token, Request $request)
    {
        $email = $request->query('email');

        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    // Handle form submission
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return view('auth.password-reset-success');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}