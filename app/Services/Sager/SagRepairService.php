<?php

namespace App\Services\Sager;

use App\Models\Sager;
use App\Models\Status;
use App\Models\Konsulenter;
use App\Models\Sagsbehandler;
use App\Models\Afslutning;
use Illuminate\Support\Facades\DB;

class SagRepairService
{
    public function repair(Sager $sag): array
    {
        $steps = [];

        DB::transaction(function () use ($sag, &$steps) {

            // TRIN 1: DUBLETTJEK PIVOT-TABELLER
            $pivotTables = [
                'sager_debitor' => 'Debitor-relationer',
                'sager_kreditor' => 'Kreditor-relationer',
                'sager_konsulent' => 'Konsulent-relationer',
                'sager_sagsbehandler' => 'Sagsbehandler-relationer',
                'sager_status' => 'Status-historik',
            ];

            $cleanedTables = [];
            foreach ($pivotTables as $table => $label) {
                $column = str_replace('sager_', '', $table) . '_id';
                $deletedCount = $this->deduplicatePivot($table, 'sag_id', $column, $sag->id);
                if ($deletedCount > 0) {
                    $cleanedTables[] = "{$label} ({$deletedCount} dublet(ter))";
                }
            }

            $steps[] = [
                'step' => 'duplicates',
                'label' => 'Renser pivot-dubletter',
                'status' => count($cleanedTables) > 0 ? 'repaired' : 'ok',
                'detail' => count($cleanedTables) > 0 
                    ? 'Fjernede dubletter i: ' . implode(', ', $cleanedTables)
                    : 'Ingen dublerede relationer fundet.'
            ];

            // TRIN 2: STATUS TJEK
            $statusCount = $sag->sagerStatus()->count();
            if ($statusCount === 0) {
                $defaultStatus = Status::first();
                if ($defaultStatus) {
                    $sag->sagerStatus()->attach($defaultStatus->id);
                }
                $steps[] = [
                    'step' => 'status',
                    'label' => 'Verificerer sagens status',
                    'status' => 'repaired',
                    'detail' => "Tilknyttede standardstatus: '{$defaultStatus?->navn}'"
                ];
            } elseif ($statusCount > 1) {
                $latestStatus = $sag->sagerStatus()->orderBy('sager_status.id', 'desc')->first();
                if ($latestStatus) {
                    $sag->sagerStatus()->sync([$latestStatus->id]);
                }
                $steps[] = [
                    'step' => 'status',
                    'label' => 'Verificerer sagens status',
                    'status' => 'repaired',
                    'detail' => "Løste statuskonflikt. Bevarede nyeste status: '{$latestStatus?->navn}'"
                ];
            } else {
                $steps[] = [
                    'step' => 'status',
                    'label' => 'Verificerer sagens status',
                    'status' => 'ok',
                    'detail' => 'Aktiv status er korrekt tilknyttet.'
                ];
            }

            // TRIN 3: SAGSBEHANDLER OG KONSULENT
            $handlerRepaired = false;
            if ($sag->sagersagsbehandler()->count() === 0) {
                $defaultSagsbehandler = Sagsbehandler::first();
                if ($defaultSagsbehandler) {
                    $sag->sagersagsbehandler()->attach($defaultSagsbehandler->id);
                    $handlerRepaired = true;
                }
            }

            if ($sag->sagerkonsulent()->count() === 0) {
                $defaultKonsulent = Konsulenter::first();
                if ($defaultKonsulent) {
                    $sag->sagerkonsulent()->attach($defaultKonsulent->id);
                    $handlerRepaired = true;
                }
            }

            $steps[] = [
                'step' => 'handlers',
                'label' => 'Tjekker behandlere og konsulenter',
                'status' => $handlerRepaired ? 'repaired' : 'ok',
                'detail' => $handlerRepaired 
                    ? 'Tildelte automatisk manglede sagsbehandler/konsulent.' 
                    : 'Sagsbehandler og konsulent er korrekt angivet.'
            ];

            // TRIN 4: AFSLUTNINGSÅRSAG
            if ($sag->afsluttet && $sag->sagerAfslutning()->count() === 0) {
                $defaultAfslutning = afslutning::first();
                if ($defaultAfslutning) {
                    $sag->sagerAfslutning()->attach($defaultAfslutning->id);
                }
                $steps[] = [
                    'step' => 'closure',
                    'label' => 'Tjekker afslutningsårsag',
                    'status' => 'repaired',
                    'detail' => "Tilknyttede standard årsag for afsluttet sag: '{$defaultAfslutning?->navn}'"
                ];
            } else {
                $steps[] = [
                    'step' => 'closure',
                    'label' => 'Tjekker afslutningsårsag',
                    'status' => 'ok',
                    'detail' => $sag->afsluttet ? 'Afslutningsårsag fundet.' : 'Sag er aktiv (ikke afsluttet).'
                ];
            }

            // TRIN 5: ØKONOMISK GENBEREGNING
            $oldIalt = $sag->ialt;
            $calculatedIalt = $this->money($sag->hovedstol) + $this->money($sag->renter) + $this->money($sag->gebyr);

            if ((float)$oldIalt !== (float)$calculatedIalt) {
                $sag->ialt = $calculatedIalt;
                $sag->save();
                $steps[] = [
                    'step' => 'finance',
                    'label' => 'Genberegner økonomi',
                    'status' => 'repaired',
                    'detail' => "Korrigerede totalbeløb fra {$oldIalt} kr. til {$calculatedIalt} kr."
                ];
            } else {
                $steps[] = [
                    'step' => 'finance',
                    'label' => 'Genberegner økonomi',
                    'status' => 'ok',
                    'detail' => 'Økonomiske felter stemmer overens (Hovedstol + Renter + Gebyr).'
                ];
            }
        });

        return $steps;
    }

    protected function deduplicatePivot(string $table, string $sagColumn, string $relationColumn, int $sagId): int
    {
        $rows = DB::table($table)->where($sagColumn, $sagId)->get();
        $seen = [];
        $deleted = 0;

        foreach ($rows as $row) {
            $key = $row->$relationColumn;
            if (in_array($key, $seen)) {
                DB::table($table)->where($sagColumn, $sagId)->where($relationColumn, $key)->limit(1)->delete();
                $deleted++;
            } else {
                $seen[] = $key;
            }
        }

        return $deleted;
    }

    protected function money($value): float
    {
        if (blank($value)) return 0.0;
        if (is_numeric($value)) return (float) $value;
        $value = str_replace('.', '', (string) $value);
        $value = str_replace(',', '.', $value);
        return (float) $value;
    }
}