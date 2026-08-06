<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class TwoFactorSetupRequired extends Component
{
    public User $user;

    public string $code = '';

    public function mount()
    {
        $userId = session('2fa_setup_user_id');

        abort_unless($userId, 403);

        $this->user = User::findOrFail($userId);

        if (empty($this->user->two_factor_secret)) {

            $this->user->forceFill([
                'two_factor_secret' => encrypt(
                    app(TwoFactorAuthenticationProvider::class)
                        ->generateSecretKey()
                ),
            ])->save();
        }
    }

    public function confirm(
        TwoFactorAuthenticationProvider $provider
    )
    {
        if (
            ! $provider->verify(
                decrypt($this->user->two_factor_secret),
                $this->code
            )
        ) {
            $this->addError(
                'code',
                'Invalid authentication code.'
            );

            return;
        }

        $this->user->forceFill([
            'two_factor_confirmed_at' => Carbon::now(),
        ])->save();

        Auth::login($this->user);

        session()->forget('2fa_setup_user_id');

        return redirect()->route(
            $this->user->dashboardRoute()
        );
    }

    public function render()
    {
        return view(
            'livewire.auth.two-factor-setup-required'
        );
    }
}