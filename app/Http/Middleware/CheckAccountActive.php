<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckAccountActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Your account has been deactivated. Please contact the super admin.',
                    ]);
            }
        }

        return $next($request);
    }
}