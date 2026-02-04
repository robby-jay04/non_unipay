<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->isStudent()) {
            // If API request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Student access required.',
                ], 403);
            }

            // If web request, redirect to login or student dashboard
            return redirect()->route('login')->with('error', 'Student access required.');
        }

        return $next($request);
    }
}
