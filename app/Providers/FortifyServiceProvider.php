<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Auth\CheckTwoFactorStatus;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use App\Http\Responses\LoginResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Validation\ValidationException;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        Fortify::ignoreRoutes();    
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 🟢 2FA PIPELINE CHECKS VED LOGIN + ROLLETJEK
        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                AttemptToAuthenticate::class,
                
                // 🟢 Valider at brugeren har denkrævede rolle til det valgte login-endpoint
                function ($request, $next) {
                    $user = auth()->user();
                    $expectedRole = $request->input('role_target'); // F.eks. 'Admin', 'Medarbejder', 'Kreditor'

                    if ($expectedRole) {
                        // Tjek om brugeren rent faktisk har rollen (case-insensitive eller via Spatie hasRole)
                        if (!$user->hasRole($expectedRole)) {
                            auth()->logout();
                            throw ValidationException::withMessages([
                                Fortify::username() => __('Du har ikke tilladelse til at logge ind her som ' . lcfirst($expectedRole) . '.'),
                            ]);
                        }
                    }

                    return $next($request);
                },

                // Vores custom tjek mod SystemSettings & Bruger-status
                CheckTwoFactorStatus::class,
                
                PrepareAuthenticatedSession::class,
            ]);
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // Omdiriger brugeren baseret på deres Spatie-rolle efter succesfuldt login
        $this->app->singleton(LoginResponseContract::class, function ($app) {
            return new class extends LoginResponse {
                public function toResponse($request)
                {
                    $user = auth()->user();

                    if ($user->hasRole('admin')) {
                        return redirect()->intended('/admin/dashboard');
                    } elseif ($user->hasRole('kreditor')) {
                        return redirect()->intended('/kreditor/dashboard');
                    }

                    // Standard for medarbejdere og andre
                    return redirect()->intended('/dashboard');
                }
            };
        });
    }
}