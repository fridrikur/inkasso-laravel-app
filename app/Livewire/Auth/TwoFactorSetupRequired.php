<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Services\SettingsService;
use App\Services\TwilioService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticationProvider;
use Illuminate\Contracts\Encryption\DecryptException;

class TwoFactorSetupRequired extends Component
{
    public User $user;

    public string $code = '';
    public string $phone = '';
    public string $providerType = 'totp';
    public bool $smsSent = false;

    /**
     * QR-code secret used by the view.
     *
     * This is deliberately kept separate from the
     * encrypted database value.
     */
    public ?string $twoFactorSecret = null;

    public function mount(SettingsService $settings)
    {
        $userId = session('2fa_setup_user_id');

        abort_unless($userId, 403);

        $this->user = User::findOrFail($userId);

        /*
        |--------------------------------------------------------------------------
        | 2FA disabled
        |--------------------------------------------------------------------------
        */

        if (! (bool) $settings->get('enable_2fa', true)) {
            return $this->completeLogin();
        }

        $this->providerType = $settings->get(
            'two_factor_provider',
            'totp'
        );

        /*
        |--------------------------------------------------------------------------
        | TOTP
        |--------------------------------------------------------------------------
        */

        if ($this->providerType === 'totp') {

            $this->prepareTotp();

        } else {

            /*
            |--------------------------------------------------------------------------
            | Twilio
            |--------------------------------------------------------------------------
            */

            $this->phone =
                $this->user->mobil
                ?? $this->user->tlf
                ?? '';
        }
    }

    /**
     * Prepare a valid TOTP secret.
     */
    protected function prepareTotp(): void
    {
        $provider = app(
            TwoFactorAuthenticationProvider::class
        );

        $secret = null;

        /*
        |--------------------------------------------------------------------------
        | Try existing secret
        |--------------------------------------------------------------------------
        */

        if (! empty($this->user->two_factor_secret)) {

            try {

                /*
                 * Fortify normally handles the encrypted
                 * secret through the model's encryption.
                 *
                 * We simply access it here.
                 */
                $secret = $this->user->two_factor_secret;

            } catch (DecryptException $e) {

                /*
                 * Existing secret was encrypted with
                 * another APP_KEY or is otherwise invalid.
                 */
                $secret = null;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Generate a new secret when necessary
        |--------------------------------------------------------------------------
        */

        if (empty($secret)) {

            $secret = $provider->generateSecretKey();

            /*
             * Store the encrypted value exactly as Fortify expects.
             *
             * IMPORTANT:
             * If your User model has an encrypted cast for this field,
             * use the model assignment.
             */
            $this->user->forceFill([
                'two_factor_secret' => $secret,
                'two_factor_confirmed_at' => null,
            ])->save();

            $this->user->refresh();

            /*
             * Use the plain secret for this component.
             */
            $this->twoFactorSecret = $secret;

        } else {

            $this->twoFactorSecret = $secret;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SMS
    |--------------------------------------------------------------------------
    */

    public function sendSmsCode()
    {
        $this->validate([
            'phone' => 'required|string|min:8',
        ]);

        if ($this->user->mobil !== $this->phone) {

            $this->user->update([
                'mobil' => $this->phone,
            ]);
        }

        $smsCode = random_int(100000, 999999);

        session([
            '2fa_setup_sms_code' => $smsCode,
        ]);

        app(TwilioService::class)->sendSms(
            $this->phone,
            "Din verifikationskode til 2FA-opsætning er: {$smsCode}"
        );

        $this->smsSent = true;

        $this->dispatch(
            'toast',
            message: 'Verifikationskode sendt på SMS!',
            type: 'success'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm
    |--------------------------------------------------------------------------
    */

    public function confirm(
        TwoFactorAuthenticationProvider $provider
    ) {
        $this->validate([
            'code' => 'required|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Twilio
        |--------------------------------------------------------------------------
        */

        if ($this->providerType === 'twilio') {

            $sessionCode = session(
                '2fa_setup_sms_code'
            );

            if (
                ! $sessionCode ||
                (string) $this->code !== (string) $sessionCode
            ) {
                $this->addError(
                    'code',
                    'Ugyldig SMS-verifikationskode.'
                );

                return;
            }

            session()->forget(
                '2fa_setup_sms_code'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | TOTP
        |--------------------------------------------------------------------------
        */

        else {

            if (empty($this->twoFactorSecret)) {

                $this->addError(
                    'code',
                    '2FA-nøglen kunne ikke indlæses.'
                );

                return;
            }

            if (
                ! $provider->verify(
                    $this->twoFactorSecret,
                    $this->code
                )
            ) {
                $this->addError(
                    'code',
                    'Ugyldig godkendelseskode fra din app.'
                );

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Mark 2FA as confirmed
        |--------------------------------------------------------------------------
        */

        $this->user->forceFill([
            'two_factor_confirmed_at' => Carbon::now(),
        ])->save();

        return $this->completeLogin();
    }

    /*
    |--------------------------------------------------------------------------
    | Complete login
    |--------------------------------------------------------------------------
    */

    private function completeLogin()
    {
        Auth::login($this->user);

        session()->forget([
            '2fa_setup_user_id',
            '2fa_setup_sms_code',
        ]);

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