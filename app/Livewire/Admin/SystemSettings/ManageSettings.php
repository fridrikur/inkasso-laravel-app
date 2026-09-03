<?php

namespace App\Livewire\Admin\SystemSettings;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;
use App\Services\SettingsService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use App\Services\ToastService; 
use Database\Seeders\DemoDataSeeder;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;

class ManageSettings extends Component
{
    //låse kode
    public string $unlock_code = '';
    public string $unlock_code_confirmation = '';
    public bool $hasUnlockCode = false;

    // 🟢 Rolleri bestemte login URL / endpoints
    public string $login_url_admin = '';
    public string $login_url_medarbejder = '';
    public string $login_url_kreditor = '';

    // Default farver (Indigo / Slate)
    public const DEFAULT_PRIMARY           = '#4f46e5';
    public const DEFAULT_SIDEBAR_BG        = '#0f172a';
    public const DEFAULT_SAG_EDITOR_BG     = '#ffffff';
    public const DEFAULT_SAG_EDITOR_WRAPPER= '#f1f5f9';
    public const DEFAULT_SAG_EDITOR_HEADER = '#4f46e5';

    // Legacy farver (Klassisk DKG Blå)
    public const LEGACY_PRIMARY            = '#1e3a8a';
    public const LEGACY_SIDEBAR_BG         = '#1e293b';
    public const LEGACY_SAG_EDITOR_BG      = '#ffffff';
    public const LEGACY_SAG_EDITOR_WRAPPER = '#e2e8f0';
    public const LEGACY_SAG_EDITOR_HEADER  = '#3b82f6';

    // System, Slogan & URL / Miljø
    public string $app_name = '';
    public string $app_slogan = '';
    public string $app_url = '';
    public string $environment = 'sandbox'; // 'live' eller 'sandbox'
    public bool $is_live_locked = false;     // Låses permanent hvis det gemmes som live

    // Tema valgt ('default', 'legacy', 'custom')
    public string $theme_preset = 'default';

    // Farvetema variabler
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

    // 2FA Indstillinger
    public bool $enable_2fa = true;
    public string $two_factor_provider = 'totp';
    public array $role_2fa = [];

    public string $allowed_ips = '';

    public function mount(): void
    {
        $settings = app(SettingsService::class);
        $currentRequestUrl = request()->getSchemeAndHttpHost();

        $this->allowed_ips = $settings->get('allowed_ips', '');

        // 🟢 Hent gemte login URL'er (eller sæt fornuftige standarder)
        $this->login_url_admin       = $settings->get('login_url_admin', '/login/admin');
        $this->login_url_medarbejder = $settings->get('login_url_medarbejder', '/login/medarbejder');
        $this->login_url_kreditor    = $settings->get('login_url_kreditor', '/login/kreditor');

        // Systemets basisidentitet
        $this->app_name   = $settings->get('app_name', 'Sagsbehandling');
        $this->app_slogan = $settings->get('app_slogan', 'Sagsadministration');
        $this->app_url    = $settings->get('app_url', $currentRequestUrl);

        // Miljø og låse-logik
        $savedEnv = $settings->get('environment', null);

        if ($savedEnv === 'live') {
            $this->environment = 'live';
            $this->is_live_locked = true; // Låst permanent i UI når først gemt som live
        } else {
            $isLiveDomain = str_contains($currentRequestUrl, 'd1k2g3db.com');
            $this->environment = $isLiveDomain ? 'live' : ($savedEnv ?? 'sandbox');
            $this->is_live_locked = false;
        }

        $this->hasUnlockCode = SystemSetting::where('key', 'global_unlock_code')->value('value') !== null;

        // Farver & Temaer
        $this->theme_preset                = $settings->get('theme_preset', 'default');
        $this->theme_primary               = $settings->get('theme_primary', self::DEFAULT_PRIMARY);
        $this->theme_sidebar_bg            = $settings->get('theme_sidebar_bg', self::DEFAULT_SIDEBAR_BG);
        $this->theme_sag_editor_bg         = $settings->get('theme_sag_editor_bg', self::DEFAULT_SAG_EDITOR_BG);
        $this->theme_sag_editor_wrapper_bg = $settings->get('theme_sag_editor_wrapper_bg', self::DEFAULT_SAG_EDITOR_WRAPPER);
        $this->theme_sag_editor_header     = $settings->get('theme_sag_editor_header', self::DEFAULT_SAG_EDITOR_HEADER);

        // 2FA & Twilio
        $this->twilio_sid           = $settings->get('twilio_sid', '');
        $this->twilio_token         = $settings->get('twilio_token', '');
        $this->twilio_verify_sid    = $settings->get('twilio_verify_sid', '');
        $this->twilio_enabled       = (bool) $settings->get('twilio_enabled', false);

        $this->enable_2fa           = (bool) $settings->get('enable_2fa', true);
        $this->two_factor_provider  = $settings->get('two_factor_provider', 'totp');

        $roles = Role::all();
        foreach ($roles as $role) {
            $this->role_2fa[$role->id] = (bool) ($role->requires_two_factor ?? false);
        }
    }

    public function updatedEnvironment($value): void
    {
        // Hvis sitet allerede ER markeret som LIVE i DB, tillades ændring overhovedet ikke
        if ($this->is_live_locked) {
            $this->environment = 'live';
            $this->dispatch('toast', [
                'message' => 'LÅST: Et LIVE-site kan ikke fravælges eller sættes tilbage i Sandkasse!',
                'type'    => 'error'
            ]);
            return;
        }

        // Tjek om man prøver at vælge Live på en ikke-live URL
        $currentRequestUrl = request()->getSchemeAndHttpHost();
        $isLiveDomain = str_contains($currentRequestUrl, 'd1k2g3db.com');

        if ($value === 'live' && ! $isLiveDomain) {
            $this->environment = 'sandbox';
            $this->dispatch('toast', [
                'message' => 'Kan ikke sætte til Live! Browserens URL skal være https://d1k2g3db.com/',
                'type'    => 'error'
            ]);
        }
    }

    public function setPreset(string $preset): void
    {
        $this->theme_preset = $preset;

        if ($preset === 'default') {
            $this->theme_primary                = self::DEFAULT_PRIMARY;
            $this->theme_sidebar_bg             = self::DEFAULT_SIDEBAR_BG;
            $this->theme_sag_editor_bg          = self::DEFAULT_SAG_EDITOR_BG;
            $this->theme_sag_editor_wrapper_bg  = self::DEFAULT_SAG_EDITOR_WRAPPER;
            $this->theme_sag_editor_header      = self::DEFAULT_SAG_EDITOR_HEADER;
        } elseif ($preset === 'legacy') {
            $this->theme_primary                = self::LEGACY_PRIMARY;
            $this->theme_sidebar_bg             = self::LEGACY_SIDEBAR_BG;
            $this->theme_sag_editor_bg          = self::LEGACY_SAG_EDITOR_BG;
            $this->theme_sag_editor_wrapper_bg  = self::LEGACY_SAG_EDITOR_WRAPPER;
            $this->theme_sag_editor_header      = self::LEGACY_SAG_EDITOR_HEADER;
        }
    }

    public function save(): void
    {
        $settings = app(SettingsService::class);

        // 🟢 Gem rollelogin URL'er
        $settings->set('login_url_admin', trim($this->login_url_admin));
        $settings->set('login_url_medarbejder', trim($this->login_url_medarbejder));
        $settings->set('login_url_kreditor', trim($this->login_url_kreditor));

        $settings->set('app_name', $this->app_name);
        $settings->set('app_slogan', $this->app_slogan);
        $settings->set('app_url', $this->app_url);
        $settings->set('environment', $this->environment);

        // Hvis det blev gemt som live, låser vi det også med det samme i denne session
        if ($this->environment === 'live') {
            $this->is_live_locked = true;
        }

        $settings->set('theme_preset', $this->theme_preset);
        $settings->set('theme_primary', $this->theme_primary);
        $settings->set('theme_sidebar_bg', $this->theme_sidebar_bg);
        $settings->set('theme_sag_editor_bg', $this->theme_sag_editor_bg);
        $settings->set('theme_sag_editor_wrapper_bg', $this->theme_sag_editor_wrapper_bg);
        $settings->set('theme_sag_editor_header', $this->theme_sag_editor_header);

        $settings->set('twilio_sid', trim($this->twilio_sid));
        $settings->set('twilio_token', trim($this->twilio_token));
        $settings->set('twilio_verify_sid', trim($this->twilio_verify_sid));
        $settings->set('twilio_enabled', $this->twilio_enabled);

        $settings->set('enable_2fa', $this->enable_2fa);
        $settings->set('two_factor_provider', $this->two_factor_provider);

        foreach ($this->role_2fa as $roleId => $required) {
            Role::where('id', $roleId)->update([
                'requires_two_factor' => (bool) $required,
            ]);
        }

        if (!empty($this->unlock_code)) {
            $this->validate([
                'unlock_code' => 'min:4|same:unlock_code_confirmation',
            ]);

            SystemSetting::updateOrCreate(
                ['key' => 'global_unlock_code'],
                ['value' => Hash::make($this->unlock_code)]
            );

            $this->hasUnlockCode = true;
            $this->reset(['unlock_code', 'unlock_code_confirmation']);
        }

        $this->dispatch('toast', [
            'message' => 'Systemindstillingerne blev gemt!',
            'type'    => 'success'
        ]);

        $settings->set('allowed_ips', trim($this->allowed_ips));
    }

    // Nulstil DB og kør demo fra SystemSettings (Kun i Sandkasse)
    public function runDemoFromSettings()
    {
        if ($this->environment === 'live') {
            return; 
        }

        try {
            @set_time_limit(300);
            @ini_set('max_execution_time', '300');

            Schema::disableForeignKeyConstraints();

            $seedersToRun = [
                DemoDataSeeder::class,
            ];

            foreach ($seedersToRun as $seederClass) {
                if (class_exists($seederClass)) {
                    (new $seederClass())->run();
                }
            }

        } catch (\Throwable $e) {
            Schema::enableForeignKeyConstraints();
            throw new \Exception('Seeder fejl: ' . $e->getMessage() . ' (Linje: ' . $e->getLine() . ' i ' . basename($e->getFile()) . ')');
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        // Ryd alt cache
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');

        // Generér succes-toast via ToastService og flash til session
        $toastData = app(ToastService::class)->success(
            'Systemet er nulstillet med friske demo-data!',
            'Succes!'
        );
        session()->flash('toast', $toastData);

        // Sæt session flag for velkomstmodalen og redirect til dashboard via Livewire
        session(['show_welcome_modal' => true]);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.admin.system-settings.manage-settings', [
            'allRoles' => Role::all()
        ]);
    }
}