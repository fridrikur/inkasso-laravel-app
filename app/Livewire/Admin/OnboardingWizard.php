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
            @set_time_limit(180);

            try {
                Schema::disableForeignKeyConstraints();

                // 1. Seed Brugere & Roller
                if (class_exists(\Database\Seeders\UserSeeder::class)) {
                    Artisan::call('db:seed', [
                        '--class' => \Database\Seeders\UserSeeder::class,
                        '--force' => true,
                    ]);
                }

                // 2. Seed Kreditorer
                if (class_exists(\Database\Seeders\KreditorSeeder::class)) {
                    Artisan::call('db:seed', [
                        '--class' => \Database\Seeders\KreditorSeeder::class,
                        '--force' => true,
                    ]);
                }

                // 3. Seed Sager
                if (class_exists(\Database\Seeders\SagerSeeder::class)) {
                    Artisan::call('db:seed', [
                        '--class' => \Database\Seeders\SagerSeeder::class,
                        '--force' => true,
                    ]);
                }

                // 4. Seed samlet DemoDataSeeder eller DatabaseSeeder
                if (class_exists(\Database\Seeders\DemoDataSeeder::class)) {
                    Artisan::call('db:seed', [
                        '--class' => \Database\Seeders\DemoDataSeeder::class,
                        '--force' => true,
                    ]);
                } elseif (class_exists(\Database\Seeders\DatabaseSeeder::class)) {
                    Artisan::call('db:seed', [
                        '--class' => \Database\Seeders\DatabaseSeeder::class,
                        '--force' => true,
                    ]);
                }

            } finally {
                Schema::enableForeignKeyConstraints();
            }

            app(SettingsService::class)->set('setup_completed', true);
            $this->showWizard = false; // 🟢 Lukker guiden i Livewire state
            
            return true;

        } catch (\Throwable $e) {
            Schema::enableForeignKeyConstraints();

            logger()->error('Fejl under seeding af demo-data: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            $this->dispatch('toast', [
                'message' => 'Fejl ved indlæsning af demo-data: ' . $e->getMessage(),
                'type'    => 'error'
            ]);

            return false;
        }
    }

    public function render()
    {
        return view('livewire.admin.onboarding-wizard');
    }
}