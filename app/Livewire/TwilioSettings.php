<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting; // Eller den model du bruger til systemindstillinger
use Twilio\Rest\Client;
use Exception;

class TwilioSettings extends Component
{
    public string $twilio_sid = '';
    public string $twilio_token = '';
    public string $twilio_verify_sid = '';
    public string $test_phone = '';

    public function mount()
    {
        // Hent eksisterende indstillinger fra databasen
        $this->twilio_sid = Setting::get('twilio_sid', '');
        $this->twilio_token = Setting::get('twilio_token', '');
        $this->twilio_verify_sid = Setting::get('twilio_verify_sid', '');
    }

    public function save()
    {
        $this->validate([
            'twilio_sid'        => 'required|string|starts_with:AC',
            'twilio_token'      => 'required|string',
            'twilio_verify_sid' => 'nullable|string|starts_with:VA',
        ], [
            'twilio_sid.starts_with' => 'Twilio Account SID skal starte med AC.',
            'twilio_verify_sid.starts_with' => 'Verify Service SID skal starte med VA.',
        ]);

        // Gem i databasen
        Setting::set('twilio_sid', trim($this->twilio_sid));
        Setting::set('twilio_token', trim($this->twilio_token));
        Setting::set('twilio_verify_sid', trim($this->twilio_verify_sid));

        $this->dispatch('toast', [
            'message' => 'Twilio indstillingerne blev gemt!',
            'type'    => 'success',
        ]);
    }

    /**
     * Testforbindelse direkte fra panelet
     */
    public function testConnection()
    {
        $this->validate([
            'test_phone' => 'required|string|min:8',
        ]);

        try {
            $client = new Client($this->twilio_sid, $this->twilio_token);

            // Send test-SMS via Twilio Verify Service
            $client->verify->v2->services($this->twilio_verify_sid)
                ->verifications
                ->create('+45' . ltrim($this->test_phone, '+45'), 'sms', ['locale' => 'da']);

            $this->dispatch('toast', [
                'message' => 'Test-SMS sendt til +45 ' . $this->test_phone,
                'type'    => 'success',
            ]);
        } catch (Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Twilio fejl: ' . $e->getMessage(),
                'type'    => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.twilio-settings');
    }
}