<?php

namespace App\Console\Commands;

use App\Models\Dialog;
use App\Models\LegacyDialogImport;
use App\Models\Sager;
use App\Models\Sagsbehandler;
use App\Models\Konsulenter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyDialogs extends Command
{
    protected $signature = 'dialogs:import';
    protected $description = 'Import legacy dialogs safely';

    public function handle()
    {
        $imports = LegacyDialogImport::where('processed', false)->get();

        foreach ($imports as $legacy) {

            DB::beginTransaction();

            try {

                $sag = Sager::find($legacy->legacy_sag_id);
                if (!$sag) {
                    $this->error("Missing sag: {$legacy->legacy_sag_id}");
                    continue;
                }

                $sagsbehandler = Sagsbehandler::where('navn', $legacy->username)->first();

                if (!$sagsbehandler) {
                    $this->error("Missing sagsbehandler: {$legacy->username}");
                    continue;
                }

                // Choose a default konsulent (example: first attached to sag)
                $konsulent = $sag->konsulenter()->first();

                Dialog::create([
                    'sag_id' => $sag->id,
                    'kreditor_id' => null,
                    'konsulent_id' => $konsulent->id,
                    'sagsbehandler_id' => $sagsbehandler->id,
                    'type' => $legacy->type,
                    'tekst' => $legacy->tekst,
                    'dato' => $legacy->dato,
                    'created_at' => $legacy->dato,
                    'updated_at' => $legacy->dato,
                ]);

                $legacy->update(['processed' => true]);

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error($e->getMessage());
            }
        }

        $this->info('Import completed.');
    }
}