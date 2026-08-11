<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class TwoFactorLogin extends Component
{
    public string $code = '';
    public string $providerType = 'totp'; // 'totp' eller 'twilio'
    public bool $smsResent = false;

    public function mount(SettingsService $settings): void
    {
        $userId = session('2fa_user_id');
        abort_unless($userId, 403);

        // 🟢 Hent fra SettingsService i stedet for SystemSetting::get(...)
        $this->providerType = $settings->get('two_factor_provider', 'totp');
    }

    public function verify(TwoFactorAuthenticationProvider $fortifyProvider)
    {
        $this->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa_user_id');
        abort_unless($userId, 403);

        $user = User::findOrFail($userId);

        // 🟢 1. TWILIO SMS GODKENDELSE
        if ($this->providerType === 'twilio') {
            $sessionCode = session('2fa_sms_code');

            if ($sessionCode && (string)$this->code === (string)$sessionCode) {
                return $this->loginUser($user);
            }

            $this->addError('code', 'Ugyldig SMS-kode.');
            return;
        }

        // 🟢 2. AUTHENTICATOR APP (TOTP) GODKENDELSE
        // $user->two_factor_secret dekrypteres automatisk af TwoFactorAuthenticatable traiten!
        if ($fortifyProvider->verify($user->two_factor_secret, $this->code)) {
            return $this->loginUser($user);
        }

        $this->addError('code', 'Ugyldig godkendelseskode fra app.');
    }

    public function resendSms(SettingsService $settings): void
    {
        if ($this->providerType !== 'twilio') return;

        $userId = session('2fa_user_id');
        $user = User::findOrFail($userId);

        $smsCode = rand(100000, 999999);
        session(['2fa_sms_code' => $smsCode]);

        app(TwilioService::class)->sendSms(
            $user->mobil ?? $user->tlf, 
            "Din nye login-kode er: {$smsCode}"
        );

        $this->smsResent = true;
    }

    private function loginUser(User $user)
    {
        Auth::login($user);
        session()->forget(['2fa_user_id', '2fa_sms_code']);

        // 🟢 Sender brugeren til den rigtige startside baseret på deres rolle
        return redirect()->intended(route($user->dashboardRoute()));
    }

    public function render()
    {
        return view('livewire.auth.two-factor-login');
    }
}