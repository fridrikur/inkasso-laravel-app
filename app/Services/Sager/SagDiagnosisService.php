<?php

namespace App\Services\Sager;

use App\Models\Sager;

class SagDiagnosisService
{
    public function diagnose(Sager $sag): array
    {
        $issues = [];
        $score = 100;

        /* =========================================================================
         * 1. CRITICAL STRUCTURAL CHECKS (-35% each)
         * ========================================================================= */

        // Missing Case Number
        if (empty($sag->sagsnr)) {
            $issues[] = [
                'type' => 'critical',
                'message' => 'Sagen mangler et sagsnummer.',
            ];
            $score -= 35;
        }

        // Missing Debitor Relation
        if ($sag->sagerdebitor->isEmpty()) {
            $issues[] = [
                'type' => 'critical',
                'message' => 'Sagen er ikke tilknyttet nogen debitor.',
            ];
            $score -= 35;
        }

        // Missing Kreditor Relation
        if ($sag->sagerkreditor->isEmpty()) {
            $issues[] = [
                'type' => 'critical',
                'message' => 'Sagen er ikke tilknyttet nogen kreditor.',
            ];
            $score -= 35;
        }

        /* =========================================================================
         * 2. OPERATIONAL & RELATIONSHIP WARNINGS (-10% each)
         * ========================================================================= */

        // Status Relation Checks
        if ($sag->sagerStatus->isEmpty()) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen har ingen aktiv status-markering.',
            ];
            $score -= 10;
        } elseif ($sag->sagerStatus->count() > 1) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen har flere samtidige statusser tilknyttet (skal konsolideres).',
            ];
            $score -= 10;
        }

        // Missing Sagsbehandler
        if ($sag->sagersagsbehandler->isEmpty()) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen mangler en tildelt sagsbehandler.',
            ];
            $score -= 10;
        }

        // Closure Consistency Check (sagerAfslutning vs. $sag->afsluttet)
        if ($sag->afsluttet && $sag->sagerAfslutning->isEmpty()) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen er markeret som afsluttet, men mangler en afslutningsårsag.',
            ];
            $score -= 10;
        }

        // Inactive/Stale Case Check
        if (!$sag->afsluttet && $sag->dialogs->isEmpty() && $sag->dokumenter->isEmpty()) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Aktiv sag uden registrerede dialoger eller dokumenter.',
            ];
            $score -= 5;
        }

        // Ensure score doesn't drop below 0
        $score = max(0, $score);

        return [
            'sag' => $sag,
            'score' => $score,
            'healthy' => $score >= 90,
            'issues' => $issues,
        ];
    }

    public function scan(): array
    {
        return Sager::with([
            'sagerdebitor',
            'sagerkreditor',
            'sagersagsbehandler',
            'sagerStatus',
            'sagerAfslutning',
            'dialogs',
            'dokumenter',
        ])
        ->get()
        ->map(fn ($sag) => $this->diagnose($sag))
        ->toArray();
    }
}