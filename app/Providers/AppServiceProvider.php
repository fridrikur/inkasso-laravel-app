<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Event::listen(Login::class, function ($event) {

            $user = $event->user;

            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_login_browser' => request()->userAgent(),
                'login_count' => ($user->login_count ?? 0) + 1,
            ])->save();
        });
    }
}
