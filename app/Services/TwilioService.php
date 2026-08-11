<?php

namespace App\Services;

use Twilio\Rest\Client;
use App\Models\Setting;

class TwilioService
{
    protected ?Client $client = null;
    protected string $verifySid = '';

    public function __construct()
    {
        $sid       = Setting::get('twilio_sid', config('services.twilio.sid'));
        $token     = Setting::get('twilio_token', config('services.twilio.token'));
        $this->verifySid = Setting::get('twilio_verify_sid', config('services.twilio.verify_sid'));

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send 2FA Verifikationskode via SMS
     */
    public function sendVerificationCode(string $phone): bool
    {
        if (!$this->client || !$this->verifySid) {
            return false;
        }

        $formattedPhone = '+45' . ltrim($phone, '+45');

        $this->client->verify->v2->services($this->verifySid)
            ->verifications
            ->create($formattedPhone, 'sms', ['locale' => 'da']);

        return true;
    }

    /**
     * Bekræft indtastet 2FA kode
     */
    public function checkVerificationCode(string $phone, string $code): bool
    {
        if (!$this->client || !$this->verifySid) {
            return false;
        }

        $formattedPhone = '+45' . ltrim($phone, '+45');

        $verificationCheck = $this->client->verify->v2->services($this->verifySid)
            ->verificationChecks
            ->create([
                'to'   => $formattedPhone,
                'code' => $code
            ]);

        return $verificationCheck->status === 'approved';
    }
}