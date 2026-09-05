<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferTableData extends Command
{
    protected $signature = 'db:transfer-table';
    protected $description = 'Overfører data fra tabel i gammel database til den nye';

    public function handle()
    {
        $tableName = 'dialog';

        $this->info("Starter overførsel af tabellen: {$tableName}...");

        // Hent alle rækker fra kilde-databasen
        $rows = DB::connection('mysql_source')->table($tableName)->get();

        if ($rows->isEmpty()) {
            $this->warn("Ingen data fundet i kildetabellen.");
            return;
        }

        // Tøm eventuelt modtager-tabellen først (valgfrit)
        // DB::table($tableName)->truncate();

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            // Konverter objekt til array for indsatser
            $data = (array) $row;

            // Indsæt i standard (ny) database - opretter hvis den ikke findes på ID
            DB::table($tableName)->updateOrInsert(
                ['id' => $data['id']], // Juster primærnøglen hvis nødvendigt
                $data
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Tabellen er succesfuldt overført!");
    }
}