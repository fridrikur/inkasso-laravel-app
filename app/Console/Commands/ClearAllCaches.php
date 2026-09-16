<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAllCaches extends Command
{
    // Det er dette navn, du skal skrive i terminalen bagefter
    protected $signature = 'cache:reset-all';

    protected $description = 'Rydder view, cache, routes og config på én gang';

    public function handle()
    {
        $this->info('Rydder cacher...');

        Artisan::call('view:clear');
        $this->line('✔️ View cache ryddet.');

        Artisan::call('cache:clear');
        $this->line('✔️ Application cache ryddet.');

        Artisan::call('route:clear');
        $this->line('✔️ Route cache ryddet.');

        Artisan::call('config:clear');
        $this->line('✔️ Config cache ryddet.');

        $this->info('Alt er tømt med succes!');
        
        return Command::SUCCESS;
    }
}