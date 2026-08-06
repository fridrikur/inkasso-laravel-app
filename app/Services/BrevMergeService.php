<?php

namespace App\Services;

use App\Models\Sager;

class BrevMergeService
{
    public function merge(string $template, Sager $sag): string
    {
        return $this->mergeWithMeta($template, $sag)['text'];
    }

    /**
     * Merge + detect unresolved tokens
     */
    public function mergeWithMeta(string $template, Sager $sag): array
    {
        $tokens = $this->resolveTokens($sag);

        foreach ($tokens as $key => $value) {
            $template = str_replace(
                '{' . $key . '}',
                (string) ($value ?? ''),
                $template
            );
        }

        return [
            'text' => $template,
            'missing' => $this->findMissingTokens($template),
        ];
    }

    protected function findMissingTokens(string $text): array
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $text, $matches);
        return array_unique($matches[1] ?? []);
    }

    protected function resolveTokens(Sager $sag): array
    {
        return array_merge(
            $this->sagerFields($sag),
            $this->relationFields($sag),
            $this->computedFields($sag),
        );
    }

    protected function sagerFields(Sager $sag): array
    {
        $out = [];
        foreach ($sag->getFillable() as $field) {
            $out[$field] = data_get($sag, $field);
        }
        return $out;
    }

    protected function relationFields(Sager $sag): array
    {
        return [
            'firmanavn' => $sag->sagerkreditor->first()?->firmanavn,
            'debitor_navn' => $sag->sagerdebitor->first()?->navn,
            'ktr' => $sag->sagerKtr->first()?->navn,
        ];
    }

    protected function computedFields(Sager $sag): array
    {
        return [
            'today' => now()->format('d-m-Y'),
            'aktiv' => $sag->aktiv ? 'Aktiv' : 'Afsluttet',
        ];
    }
    
}
