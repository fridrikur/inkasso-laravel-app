<?php

namespace App\Actions\Auth;

use App\Services\SettingsService;
use App\Services\TwilioService;
use Illuminate\Http\Request;

class CheckTwoFactorStatus
{
    public function handle(Request $request, $next)
    {
        $user = $request->user();
        $settings = app(SettingsService::class);

        // 1. Tjek om 2FA er slået til i Systemindstillinger
        $globalTwoFactorEnabled = (bool) $settings->get('enable_2fa', false);

        // 2. Tjek om brugerens rolle kræver 2FA ($user->requiresTwoFactor())
        $roleRequiresTwoFactor = $user->requiresTwoFactor();

        // 🟢 2FA UDFØRES KUN HVIS BÅDE GLOBAL 2FA ER AKTIV OG ROLLERNE KRÆVER DET
        if ($globalTwoFactorEnabled && $roleRequiresTwoFactor) {

            // SCENARIE A: Brugeren mangler at opsætte sin 2FA (QR-kode eller SMS)
            if (! $user->hasConfiguredTwoFactor()) {
                session(['2fa_setup_user_id' => $user->id]);

                // Log ud fra den midlertidige Fortify-session
                auth()->guard('web')->logout();

                return redirect()->route('two-factor.setup-required');
            }

            // SCENARIE B: Brugeren har allerede opsat sin 2FA -> Kræv verifikationskode
            session(['2fa_user_id' => $user->id]);

            // Hvis Twilio SMS benyttes, afsend koden med det samme
            if ($settings->get('two_factor_provider') === 'twilio') {
                $smsCode = rand(100000, 999999);
                session(['2fa_sms_code' => $smsCode]);

                app(TwilioService::class)->sendSms(
                    $user->mobil ?? $user->tlf, 
                    "Din login-kode er: {$smsCode}"
                );
            }

            // Log ud fra den midlertidige Fortify-session indtil koden er indtastet
            auth()->guard('web')->logout();

            return redirect()->route('two-factor.login');
        }

        // 🔴 HVIS 2FA IKKE KRÆVES FOR DENNE ROLLE ELLER ER SLÅET FRA GLOBAL T -> LOG IND DIREKTE
        return $next($request);
    }
}