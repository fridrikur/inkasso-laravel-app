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

        // Missing Debitor Relation (null-safe)
        if ($sag->debitor?->isEmpty() ?? true) {
            $issues[] = [
                'type' => 'critical',
                'message' => 'Sagen er ikke tilknyttet nogen debitor.',
            ];
            $score -= 35;
        }

        // Missing Kreditor Relation (null-safe)
        if ($sag->kreditor?->isEmpty() ?? true) {
            $issues[] = [
                'type' => 'critical',
                'message' => 'Sagen er ikke tilknyttet nogen kreditor.',
            ];
            $score -= 35;
        }

        /* =========================================================================
         * 2. OPERATIONAL & RELATIONSHIP WARNINGS (-10% each)
         * ========================================================================= */

        // Status Relation Checks (null-safe)
        $statusCollection = $sag->status;
        if ($statusCollection?->isEmpty() ?? true) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen har ingen aktiv status-markering.',
            ];
            $score -= 10;
        } elseif ($statusCollection->count() > 1) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen har flere samtidige statusser tilknyttet (skal konsolideres).',
            ];
            $score -= 10;
        }

        // Missing Sagsbehandler (null-safe)
        if ($sag->sagsbehandler?->isEmpty() ?? true) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen mangler en tildelt sagsbehandler.',
            ];
            $score -= 10;
        }

        // Closure Consistency Check (afslutning vs. $sag->afsluttet) (null-safe)
        if ($sag->afsluttet && ($sag->afslutning?->isEmpty() ?? true)) {
            $issues[] = [
                'type' => 'warning',
                'message' => 'Sagen er markeret som afsluttet, men mangler en afslutningsårsag.',
            ];
            $score -= 10;
        }

        // Inactive/Stale Case Check (null-safe)
        $hasNoDialogs = $sag->dialogs?->isEmpty() ?? true;
        $hasNoDokumenter = $sag->dokumenter?->isEmpty() ?? true;

        if (!$sag->afsluttet && $hasNoDialogs && $hasNoDokumenter) {
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
            'debitor',
            'kreditor',
            'sagsbehandler',
            'status',
            'afslutning',
            'dialogs',
            'dokumenter',
        ])
        ->get()
        ->map(fn ($sag) => $this->diagnose($sag))
        ->toArray();
    }
}