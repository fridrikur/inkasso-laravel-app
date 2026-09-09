<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PerformanceMonitor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PerformanceMonitor::class, function ($app) {
            return new PerformanceMonitor();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(PerformanceMonitor $monitor): void
    {
        $monitor->start();
        
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
        DB::listen(function ($query) {
            Log::info('SQL', [
                'time' => $query->time . ' ms',
                'sql' => $query->sql,
                'bindings' => $query->bindings,
            ]);
        });
    }
}
