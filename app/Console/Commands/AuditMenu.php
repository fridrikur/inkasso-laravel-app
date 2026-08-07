<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AuditMenu extends Command
{
    protected $signature = 'audit:menu';
    protected $description = 'Interaktiv hurtigmenu til audit og oprydning af kodebase og database.';

    public function handle(): int
    {
        while (true) {
            $this->newLine();
            $this->line('<fg=cyan;options=bold>====================================================</>');
            $this->line('<fg=cyan;options=bold>        🛠️  PROJECT AUDIT & CLEANUP MENU            </>');
            $this->line('<fg=cyan;options=bold>====================================================</>');
            $this->newLine();

            $choice = $this->choice(
                'Vælg den oprydningsanalyse du ønsker at køre:',
                [
                    '1' => '📦 Eloquent Modeller (model:check-usage)',
                    '2' => '⚡ Livewire Komponenter (component:check-usage)',
                    '3' => '🎨 Blade Views (view:check-usage)',
                    '4' => '🗄️ Database Tabeller & Migrations (migration:check-usage)',
                    '5' => '🚀 Kør ALLE 4 tjek i rækkefølge',
                    '0' => '❌ Afslut menu',
                ],
                '0'
            );

            $this->newLine();

            switch ($choice) {
                case '1':
                case '📦 Eloquent Modeller (model:check-usage)':
                    $this->call('model:check-usage');
                    break;

                case '2':
                case '⚡ Livewire Komponenter (component:check-usage)':
                    $this->call('component:check-usage');
                    break;

                case '3':
                case '🎨 Blade Views (view:check-usage)':
                    $this->call('view:check-usage');
                    break;

                case '4':
                case '🗄️ Database Tabeller & Migrations (migration:check-usage)':
                    $this->call('migration:check-usage');
                    break;

                case '5':
                case '🚀 Kør ALLE 4 tjek i rækkefølge':
                    $this->info('--- 1/4: Analyserer Eloquent Modeller ---');
                    $this->call('model:check-usage');
                    $this->newLine(2);

                    $this->info('--- 2/4: Analyserer Livewire Komponenter ---');
                    $this->call('component:check-usage');
                    $this->newLine(2);

                    $this->info('--- 3/4: Analyserer Blade Views ---');
                    $this->call('view:check-usage');
                    $this->newLine(2);

                    $this->info('--- 4/4: Analyserer Database & Migrations ---');
                    $this->call('migration:check-usage');
                    break;

                case '0':
                case '❌ Afslut menu':
                    $this->info('Afslutter audit-menu. God arbejdslyst! 👋');
                    return self::SUCCESS;
            }

            if (!$this->confirm('Vil du vende tilbage til hovedmenuen?', true)) {
                $this->info('Farvel! 👋');
                return self::SUCCESS;
            }
        }
    }
}