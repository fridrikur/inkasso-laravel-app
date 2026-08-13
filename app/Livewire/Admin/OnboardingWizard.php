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

    // 🚀 TRIN 0: Kør migrationer og seeders
    public function executeSystemInstallation(): void
    {
        try {
            // 1. Kør alle database-migrationer
            Artisan::call('migrate', ['--force' => true]);

            // 2. Opret standardroller og Admin-bruger
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\UserSeeder',
                '--force' => true,
            ]);

            $this->checkDatabaseState();

            $this->dispatch('toast', [
                'message' => 'Systemet og databasen blev installeret med succes!',
                'type'    => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Fejl under migrering: ' . $e->getMessage(),
                'type'    => 'error'
            ]);
        }
    }

    public function startFresh(): void
    {
        app(SettingsService::class)->set('setup_completed', true);
        $this->showWizard = false;

        $this->dispatch('toast', [
            'message' => 'Ren installation startet! Databasen er klar.',
            'type'    => 'success'
        ]);
    }

    public function goToImport(): void
    {
        app(SettingsService::class)->set('setup_completed', true);
        $this->redirect(route('sager.import.log'));
    }

    public function installDemoData(): void
    {
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\DemoDataSeeder',
                '--force' => true,
            ]);

            app(SettingsService::class)->set('setup_completed', true);
            $this->showWizard = false;

            $this->dispatch('toast', [
                'message' => 'Testdata (Brugere, Kreditorer og Sager) er installeret!',
                'type'    => 'success'
            ]);

            $this->redirect(route('dashboard'));

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'message' => 'Fejl ved indlæsning af demo-data: ' . $e->getMessage(),
                'type'    => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.onboarding-wizard');
    }
}