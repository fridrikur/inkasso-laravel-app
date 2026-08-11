<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\TwilioService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class TwoFactorSetupRequired extends Component
{
    public User $user;

    public string $code = '';
    public string $phone = '';
    public string $providerType = 'totp'; // 'totp' eller 'twilio'
    public bool $smsSent = false;

    public function mount(SettingsService $settings)
    {
        $userId = session('2fa_setup_user_id');
        abort_unless($userId, 403);

        $this->user = User::findOrFail($userId);

        if (! (bool) $settings->get('enable_2fa', true)) {
            return $this->completeLogin();
        }

        $this->providerType = $settings->get('two_factor_provider', 'totp');

        if ($this->providerType === 'totp') {
            if (empty($this->user->two_factor_secret)) {
                // 🟢 Fortify krypterer automatisk via sin trait – undlad encrypt() her!
                $this->user->forceFill([
                    'two_factor_secret' => app(TwoFactorAuthenticationProvider::class)->generateSecretKey(),
                ])->save();
            }
        } else {
            $this->phone = $this->user->mobil ?? $this->user->tlf ?? '';
        }
    }

    /**
     * Send verifikations-SMS ved opsætning af Twilio 2FA
     */
    public function sendSmsCode()
    {
        $this->validate([
            'phone' => 'required|string|min:8',
        ]);

        // Gem/opdatér mobilnummer på brugeren
        if ($this->user->mobil !== $this->phone) {
            $this->user->update(['mobil' => $this->phone]);
        }

        $smsCode = rand(100000, 999999);
        session(['2fa_setup_sms_code' => $smsCode]);

        // Send koden via Twilio
        app(TwilioService::class)->sendSms(
            $this->phone,
            "Din verifikationskode til 2FA-opsætning er: {$smsCode}"
        );

        $smsSent = true;

        $this->dispatch('toast', [
            'message' => 'Verifikationskode sendt på SMS!',
            'type'    => 'success'
        ]);
    }

    public function confirm(TwoFactorAuthenticationProvider $provider)
    {
        $this->validate([
            'code' => 'required|string',
        ]);

        // 🟢 Bekræftelse ved Twilio SMS
        if ($this->providerType === 'twilio') {
            $sessionCode = session('2fa_setup_sms_code');

            if (!$sessionCode || (string)$this->code !== (string)$sessionCode) {
                $this->addError('code', 'Ugyldig SMS-verifikationskode.');
                return;
            }

            session()->forget('2fa_setup_sms_code');
        } 
        // 🟢 Bekræftelse ved Authenticator App (TOTP)
        else {
            if (! $provider->verify(decrypt($this->user->two_factor_secret), $this->code)) {
                $this->addError('code', 'Ugyldig godkendelseskode fra din app.');
                return;
            }
        }

        // Markér 2FA som bekræftet på brugeren
        $this->user->forceFill([
            'two_factor_confirmed_at' => Carbon::now(),
        ])->save();

        return $this->completeLogin();
    }

    private function completeLogin()
    {
        Auth::login($this->user);
        session()->forget('2fa_setup_user_id');

        return redirect()->route($this->user->dashboardRoute());
    }

    public function render()
    {
        return view('livewire.auth.two-factor-setup-required');
    }
}