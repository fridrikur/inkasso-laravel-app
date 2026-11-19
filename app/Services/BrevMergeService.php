<?php

namespace App\Services;

use App\Models\Sager;
use Carbon\Carbon;

class BrevMergeService
{
    public function merge(?string $template, Sager $sag): string
    {
        return $this->mergeWithMeta($template, $sag)['text'];
    }

    public function mergeWithMeta(?string $template, Sager $sag): array
    {
        $template = $this->cleanTemplate((string) ($template ?? ''));

        $tokens = $this->resolveTokens($sag);

        foreach ($tokens as $key => $value) {
            $replacement = (string) ($value ?? '');

            $template = str_replace('{' . $key . '}', $replacement, $template);

            $pattern = '/\{(?:<[^>]+>|\s)*' . preg_quote($key, '/') . '(?:<[^>]+>|\s)*\}/u';
            $replaced = preg_replace($pattern, $replacement, $template);
            if ($replaced !== null) {
                $template = $replaced;
            }
        }

        return [
            'text' => $template,
            'missing' => $this->findMissingTokens($template),
        ];
    }
    
    protected function cleanTemplate(string $template): string
    {
        if (empty($template)) {
            return '';
        }

        // 1. Oversæt/fjern rå tekst-escape-sekvenser (såsom \r og \n) til rigtige tegn
        $template = stripcslashes($template);

        // 2. Konverter rigtige linjeskift til <br> tags, så de vises korrekt i HTML-editoren
        $template = nl2br($template);

        return $template;
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

        $dateFields = [
            'afsluttet',
            'faktureret',
            'betalt',
            'fakturadato',
            'modtaget',
            'senesterapport',
            'opgivet',
            'dato',
        ];

        $amountFields = [
            'hovedstol',
            'renter',
            'gebyr',
            'ialt',
            'startgebyr',
            'restgaeld_dkg',
            'restgaeld_kreditor',
            'indbetalt',
            'n_mdlydelse',
        ];

        // Kombiner fillable og faktiske attributter fra databasen, så intet udelades
        $allFields = array_unique(array_merge(
            $sag->getFillable(),
            array_keys($sag->getAttributes())
        ));

        foreach ($allFields as $field) {
            $value = data_get($sag, $field);

            if (in_array($field, $dateFields) && !empty($value)) {
                try {
                    $value = Carbon::parse($value)->format('d-m-Y');
                } catch (\Exception $e) {
                    // Bevar originalværdi hvis parsing fejler
                }
            } elseif (in_array($field, $amountFields) && $value !== null && $value !== '') {
                $numeric = is_numeric($value)
                    ? (float) $value
                    : (float) str_replace(['.', ','], ['', '.'], (string) $value);

                $value = number_format($numeric, 2, ',', '.');
            }

            $out[$field] = $value;
        }

        return $out;
    }

    protected function relationFields(Sager $sag): array
    {
        $kreditor = method_exists($sag, 'kreditor') ? $sag->kreditor()->first() : null;
        $debitor  = method_exists($sag, 'debitor') ? $sag->debitor()->first() : null;

        // Hent by via debitorens postnummer-relation
        $byNavn = '';
        if ($debitor) {
            $postnrModel = method_exists($debitor, 'postnummer') ? $debitor->postnummer()->first() : null;
            if ($postnrModel) {
                $byNavn = $postnrModel->by ?? $postnrModel->navn ?? $postnrModel->bynavn ?? '';
            }
        }

        // Hent kontrakttype(r) via belongsToMany-relationen ktr()
        $ktrTekst = '';
        if (method_exists($sag, 'ktr')) {
            $ktrCollection = $sag->relationLoaded('ktr')
                ? $sag->ktr
                : $sag->ktr()->get();

            if ($ktrCollection && $ktrCollection->isNotEmpty()) {
                $ktrTekst = $ktrCollection
                    ->map(function ($item) {
                        return $item->tekst
                            ?? $item->forkortelse
                            ?? $item->navn
                            ?? $item->ktr
                            ?? $item->titel
                            ?? $item->type
                            ?? $item->kontrakttype
                            ?? $item->beskrivelse
                            ?? '';
                    })
                    ->filter()
                    ->implode(', ');
            }
        }

        if (empty($ktrTekst) && isset($sag->ktr) && is_string($sag->ktr)) {
            $ktrTekst = $sag->ktr;
        }

        return [
            'firmanavn'          => $kreditor?->navn ?? $kreditor?->firmanavn ?? '',

            // Debitor felter koblet til Debitorer-modellen og dens postnummer-relation
            'debitor_navn'       => $debitor?->navn ?? '',
            'debitor_adresse'    => $debitor?->adresse ?? '',
            'debitor_postnr'     => $debitor?->postnr ?? '',
            'debitor_postnummer' => $debitor?->postnr ?? '',
            'debitor_by'         => $byNavn,
            'debitor_email'      => $debitor?->email ?? $debitor?->email2 ?? '',

            // Kontrakttype (understøtter både {ktr} og {kontrakttype})
            'ktr'                => $ktrTekst,
            'kontrakttype'       => $ktrTekst,
        ];
    }
    
    protected function parseAmount(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = trim((string) $value);
        if ($str === '') {
            return null;
        }

        // Håndter både dansk format ("1.234,00") og punktum-decimaler ("1234.00")
        if (str_contains($str, ',')) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        }

        return is_numeric($str) ? (float) $str : null;
    }

    protected function computedFields(Sager $sag): array
    {
        $aktivTekst = $sag->aktiv ? 'Aktiv' : 'Inaktiv';

        if (isset($sag->aktiv) && is_string($sag->aktiv)) {
            $aktivTekst = $sag->aktiv;
        }

        // 1. Prøv først den gemte 'ialt'-værdi på sagen
        $ialtFloat = $this->parseAmount($sag->ialt ?? $sag->getAttribute('ialt') ?? null);

        // 2. Fallback: beregn D = A + B + C (hovedstol + renter + gebyr), hvis ialt ikke er gemt
        if ($ialtFloat === null) {
            $hovedstol = $this->parseAmount($sag->hovedstol ?? null) ?? 0.0;
            $renter    = $this->parseAmount($sag->renter ?? null) ?? 0.0;
            $gebyr     = $this->parseAmount($sag->gebyr ?? null) ?? 0.0;

            if ($hovedstol > 0 || $renter > 0 || $gebyr > 0) {
                $ialtFloat = $hovedstol + $renter + $gebyr;
            }
        }

        $ialtFormatted = $ialtFloat !== null
            ? number_format($ialtFloat, 2, ',', '.')
            : '';

        return [
            'today' => now()->format('d-m-Y'),
            'aktiv' => $aktivTekst,
            'ialt'  => $ialtFormatted,
        ];
    }
}