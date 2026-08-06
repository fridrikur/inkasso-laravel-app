<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class TwoFactorLogin extends Component
{
    public string $code = '';

    public function verify(
        TwoFactorAuthenticationProvider $provider
    )
    {
        $userId = session('2fa_user_id');

        abort_unless($userId, 403);

        $user = User::findOrFail($userId);

        if (
            $provider->verify(
                decrypt($user->two_factor_secret),
                $this->code
            )
        ) {

            Auth::login($user);

            session()->forget('2fa_user_id');

            return redirect()->route(
                $user->dashboardRoute()
            );
        }

        $this->addError(
            'code',
            'Invalid authentication code.'
        );
    }

    public function render()
    {
        return view(
            'livewire.auth.two-factor-login'
        );
    }
}