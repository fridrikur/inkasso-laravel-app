<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Kreditorer;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OnboardingWizard extends Component
{
    public bool $showWizard = false;
    public bool $hasDatabaseTables = false;

    public function mount(): void
    {
        $this->checkDatabaseState();
    }

    public function checkDatabaseState(): void
    {
        try {
            $this->hasDatabaseTables = Schema::hasTable('migrations') && Schema::hasTable('users');
        } catch (\Throwable $e) {
            $this->hasDatabaseTables = false;
        }

        if (! $this->hasDatabaseTables) {
            $this->showWizard = true;
            return;
        }

        try {
            $settings = app(SettingsService::class);
            $isCompleted = (bool) $settings->get('setup_completed', false);

            if (! $isCompleted && Sager::count() === 0 && Kreditorer::count() === 0) {
                $this->showWizard = true;
            } else {
                $this->showWizard = false;
            }
        } catch (\Throwable $e) {
            $this->showWizard = true;
        }
    }

    public function executeSystemInstallation(): bool
    {
        try {
            @set_time_limit(180);

            try {
                Schema::disableForeignKeyConstraints();

                Artisan::call('migrate', ['--force' => true]);

                if (class_exists(\Database\Seeders\UserSeeder::class)) {
                    Artisan::call('db:seed', [
                        '--class' => \Database\Seeders\UserSeeder::class,
                        '--force' => true,
                    ]);
                }
            } finally {
                Schema::enableForeignKeyConstraints();
            }

            $this->checkDatabaseState();

            $this->dispatch('toast', [
                'message' => 'Systemet er installeret med succes!',
                'type'    => 'success'
            ]);

            return true;

        } catch (\Throwable $e) {
            $this->dispatch('toast', [
                'message' => 'Fejl under installation: ' . $e->getMessage(),
                'type'    => 'error'
            ]);

            return false;
        }
    }

    public function startFresh(): void
    {
        app(SettingsService::class)->set('setup_completed', true);
        $this->showWizard = false;

        $this->dispatch('toast', [
            'message' => 'Ren installation startet! Velkommen.',
            'type'    => 'success'
        ]);
    }

    public function goToImport(): void
    {
        app(SettingsService::class)->set('setup_completed', true);
        $this->redirect(route('sager.import.log'));
    }

    public function installDemoData() // Fjern ': bool' så den ikke brokker sig over returtyper
    {
        try {
            // Skru tidsgrænsen op til 5 minutter for at undgå timeouts på remote servere
            @set_time_limit(300);
            @ini_set('max_execution_time', '300');

            Schema::disableForeignKeyConstraints();

            // Kør dine ægte seedere i den korrekte rækkefølge
            $seedersToRun = [
                \Database\Seeders\UserSeeder::class,
                \Database\Seeders\KreditorSeeder::class,
                \Database\Seeders\SagerSeeder::class,
            ];

            foreach ($seedersToRun as $seederClass) {
                if (class_exists($seederClass)) {
                    logger()->info("Starter seeder: {$seederClass}");
                    (new $seederClass())->run();
                }
            }

        } catch (\Throwable $e) {
            Schema::disableForeignKeyConstraints();

            logger()->error('Fejl under kørsel af demo-seedere: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'message' => 'Fejl ved demo-data: ' . $e->getMessage(),
                'type'    => 'error'
            ]);

            return; // Returner ingenting ved fejl
        } finally {
            Schema::disableForeignKeyConstraints();
        }

        // Marker opsætning som fuldført
        app(SettingsService::class)->set('setup_completed', true);
        $this->showWizard = false;
        
        // Kør redirect som en kommando UDEN 'return' foran
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.onboarding-wizard');
    }
}