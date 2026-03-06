<?php

namespace App\Providers;
 use Illuminate\Auth\Notifications\ResetPassword;
// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
  

public function boot()
{
    ResetPassword::createUrlUsing(function ($user, string $token) {
        // Send link directly to mobile app (replace with your deep link)
        return "nonunipay://reset-password?token={$token}&email={$user->email}";
    });
}
}
