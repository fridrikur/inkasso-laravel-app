<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->requiresTwoFactor()) {

            if (! $user->hasConfiguredTwoFactor()) {

                // store FIRST
                $request->session()->put(
                    '2fa_setup_user_id',
                    $user->id
                );

                Auth::logout();

                return redirect()->route(
                    'two-factor.setup-required'
                );
            }

            // store FIRST
            $request->session()->put(
                '2fa_user_id',
                $user->id
            );

            Auth::logout();

            return redirect()->route(
                'two-factor.login'
            );
        }

        return redirect()->intended(
            route($user->dashboardRoute())
        );
    }
}