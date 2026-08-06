<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PurgeOldSager extends Command
{
    protected $signature = 'gdpr:purge-sager';
    protected $description = 'Anonymize or delete sager older than 5 years';

    public function handle()
    {
        Sager::query()
            ->whereNotNull('afsluttet')
            ->where('afsluttet', '<', now()->subYears(5))
            ->chunkById(100, function ($sager) {

                foreach ($sager as $sag) {
                    $this->info("Processing sag {$sag->id}");

                    $sag->anonymizeRelations();
                    $sag->anonymize();
                }

            });

        $this->info('GDPR cleanup completed.');
    }
}