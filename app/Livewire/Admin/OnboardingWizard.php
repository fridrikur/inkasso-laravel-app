<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Kreditorer;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

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

    public function installDemoData(): bool
    {
        try {
            // Skru tidsgrænsen op til max
            @set_time_limit(300);
            @ini_set('max_execution_time', '300');

            Schema::disableForeignKeyConstraints();

            // Kør seederne direkte som klasser i stedet for Artisan-kald (forhindrer timeout og CLI-fejl)
            $seeders = [
                \Database\Seeders\UserSeeder::class,
                \Database\Seeders\KreditorSeeder::class,
                \Database\Seeders\SagerSeeder::class,
                \Database\Seeders\DemoDataSeeder::class,
                \Database\Seeders\DatabaseSeeder::class,
            ];

            foreach ($seeders as $seederClass) {
                if (class_exists($seederClass)) {
                    try {
                        (new $seederClass())->run();
                    } catch (\Throwable $seederEx) {
                        // Log specifik fejl for denne seeder, men lad processen fortsætte
                        logger()->warning("Seeder {$seederClass} advarsel: " . $seederEx->getMessage());
                    }
                }
            }

        } catch (\Throwable $e) {
            logger()->error('Kritisk fejl under demo-data installation: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'message' => 'Fejl: ' . $e->getMessage(),
                'type'    => 'error'
            ]);

            return false;
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        // Marker opsætning som fuldført
        app(SettingsService::class)->set('setup_completed', true);
        $this->showWizard = false;
        
        return true;
    }

    public function render()
    {
        return view('livewire.admin.onboarding-wizard');
    }
}