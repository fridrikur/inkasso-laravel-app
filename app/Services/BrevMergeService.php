<?php

namespace App\Services;

use App\Models\Sager;
use Carbon\Carbon;

class BrevMergeService
{
    public function merge(string $template, Sager $sag): string
    {
        return $this->mergeWithMeta($template, $sag)['text'];
    }

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
        
        // Liste over felter der skal formateres som datoer (hvis de ikke er tomme)
        $dateFields = [
            'afsluttet', 
            'faktureret', 
            'betalt', 
            'fakturadato', 
            'modtaget', 
            'senesterapport', 
            'opgivet', 
            'dato'
        ];

        foreach ($sag->getFillable() as $field) {
            $value = data_get($sag, $field);

            // Hvis det er et datofelt og værdien findes, formater til d-m-Y
            if (in_array($field, $dateFields) && !empty($value)) {
                try {
                    $value = Carbon::parse($value)->format('d-m-Y');
                } catch (\Exception $e) {
                    // Bevar originalværdi hvis parsing fejer
                }
            }

            $out[$field] = $value;
        }

        return $out;
    }

    protected function relationFields(Sager $sag): array
    {
        $kreditor = $sag->kreditor()->first();
        $debitor = $sag->debitor()->first();

        return [
            'firmanavn'    => $kreditor?->navn ?? $kreditor?->firmanavn ?? '',
            'debitor_navn' => $debitor?->navn ?? '',
            'ktr'          => $sag->ktr()->first()?->navn ?? '',
        ];
    }

    protected function computedFields(Sager $sag): array
    {
        return [
            // Hvis du vil have en dedikeret {today} token til dags dato:
            'today' => now()->format('d-m-Y'),
        ];
    }
}