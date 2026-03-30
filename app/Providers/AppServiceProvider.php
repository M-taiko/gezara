<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Redirect unauthenticated users to signin (not the default /login)
        Authenticate::redirectUsing(fn() => route('signin'));
    }
}
