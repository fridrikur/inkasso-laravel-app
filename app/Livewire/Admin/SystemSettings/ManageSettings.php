<?php

namespace App\Livewire\Admin\SystemSettings;

use Livewire\Component;
use App\Services\SettingsService;
use Spatie\Permission\Models\Role; // ELLER App\Models\Role afhængigt af din opsætning
use Twilio\Rest\Client;
use Exception;

class ManageSettings extends Component
{
    // Default farver
    public const DEFAULT_PRIMARY           = '#4f46e5';
    public const DEFAULT_SIDEBAR_BG        = '#0f172a';
    public const DEFAULT_SAG_EDITOR_BG     = '#ffffff';
    public const DEFAULT_SAG_EDITOR_WRAPPER= '#f1f5f9';
    public const DEFAULT_SAG_EDITOR_HEADER = '#4f46e5';

    // Legacy farver
    public const LEGACY_PRIMARY            = '#1e3a8a';
    public const LEGACY_SIDEBAR_BG         = '#1e293b';
    public const LEGACY_SAG_EDITOR_BG      = '#ffffff';
    public const LEGACY_SAG_EDITOR_WRAPPER = '#e2e8f0';
    public const LEGACY_SAG_EDITOR_HEADER  = '#3b82f6';

    // System & Slogan
    public string $app_name = '';
    public string $app_slogan = '';
    
    // Tema valgt
    public string $theme_preset = 'default';

    // Farvetema
    public string $theme_primary = self::DEFAULT_PRIMARY;
    public string $theme_sidebar_bg = self::DEFAULT_SIDEBAR_BG;
    public string $theme_sag_editor_bg = self::DEFAULT_SAG_EDITOR_BG;
    public string $theme_sag_editor_wrapper_bg = self::DEFAULT_SAG_EDITOR_WRAPPER;
    public string $theme_sag_editor_header = self::DEFAULT_SAG_EDITOR_HEADER;

    // Twilio 2FA / SMS
    public string $twilio_sid = '';
    public string $twilio_token = '';
    public string $twilio_verify_sid = '';
    public bool $twilio_enabled = false;
    public string $test_phone = '';

    // 🟢 2FA Indstillinger
    public bool $enable_2fa = true;
    public string $two_factor_provider = 'totp'; // 'totp' eller 'twilio'
    
    // 🟢 Rolle 2FA status (key = role_id, value = boolean)
    public array $role_2fa = [];

    public function mount(): void
    {
        $settings = app(SettingsService::class);

        $this->app_name                     = $settings->get('app_name', 'Sagsbehandling');
        $this->app_slogan                   = $settings->get('app_slogan', 'Sagsadministration');
        $this->theme_preset                 = $settings->get('theme_preset', 'default');
        
        $this->theme_primary                = $settings->get('theme_primary', self::DEFAULT_PRIMARY);
        $this->theme_sidebar_bg             = $settings->get('theme_sidebar_bg', self::DEFAULT_SIDEBAR_BG);
        $this->theme_sag_editor_bg          = $settings->get('theme_sag_editor_bg', self::DEFAULT_SAG_EDITOR_BG);
        $this->theme_sag_editor_wrapper_bg  = $settings->get('theme_sag_editor_wrapper_bg', self::DEFAULT_SAG_EDITOR_WRAPPER);
        $this->theme_sag_editor_header      = $settings->get('theme_sag_editor_header', self::DEFAULT_SAG_EDITOR_HEADER);

        $this->twilio_sid                    = $settings->get('twilio_sid', '');
        $this->twilio_token                  = $settings->get('twilio_token', '');
        $this->twilio_verify_sid             = $settings->get('twilio_verify_sid', '');
        $this->twilio_enabled                = (bool) $settings->get('twilio_enabled', false);

        // Indlæs 2FA-indstillinger
        $this->enable_2fa                    = (bool) $settings->get('enable_2fa', true);
        $this->two_factor_provider           = $settings->get('two_factor_provider', 'totp');

        // 🟢 Indlæs 2FA status pr. rolle fra databasen
        $roles = Role::all();
        foreach ($roles as $role) {
            $this->role_2fa[$role->id] = (bool) ($role->requires_two_factor ?? false);
        }
    }

    public function save(): void
    {
        $settings = app(SettingsService::class);

        $settings->set('app_name', $this->app_name);
        $settings->set('app_slogan', $this->app_slogan);
        $settings->set('theme_preset', $this->theme_preset);
        
        // Gem farver
        $settings->set('theme_primary', $this->theme_primary);
        $settings->set('theme_sidebar_bg', $this->theme_sidebar_bg);
        $settings->set('theme_sag_editor_bg', $this->theme_sag_editor_bg);
        $settings->set('theme_sag_editor_wrapper_bg', $this->theme_sag_editor_wrapper_bg);
        $settings->set('theme_sag_editor_header', $this->theme_sag_editor_header);

        // Gem Twilio
        $settings->set('twilio_sid', trim($this->twilio_sid));
        $settings->set('twilio_token', trim($this->twilio_token));
        $settings->set('twilio_verify_sid', trim($this->twilio_verify_sid));
        $settings->set('twilio_enabled', $this->twilio_enabled);

        // Gem 2FA-indstillinger
        $settings->set('enable_2fa', $this->enable_2fa);
        $settings->set('two_factor_provider', $this->two_factor_provider);

        // 🟢 Gem 2FA-krav pr. rolle i databasen
        foreach ($this->role_2fa as $roleId => $required) {
            Role::where('id', $roleId)->update([
                'requires_two_factor' => (bool) $required,
            ]);
        }

        $this->dispatch('toast', [
            'message' => 'Systemindstillingerne og rollekrav blev gemt!',
            'type'    => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.system-settings.manage-settings', [
            'allRoles' => Role::all()
        ]);
    }
}