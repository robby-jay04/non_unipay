<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        // Allow both admin and superadmin
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            // If request expects JSON (API)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            // Otherwise, redirect to login page for web
            return redirect()->route('login')->with('error', 'Admin access required.');
        }

        return $next($request);
    }
}