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
        // 🟢 Rens skabelonen for gamle \r\n og \n tegn, så de ikke skaber støj
        $template = $this->cleanTemplate($template);

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
            'dato'
        ];

        foreach ($sag->getFillable() as $field) {
            $value = data_get($sag, $field);

            if (in_array($field, $dateFields) && !empty($value)) {
                try {
                    $value = Carbon::parse($value)->format('d-m-Y');
                } catch (\Exception $e) {
                    // Bevar originalværdi hvis parsing fejler
                }
            }

            $out[$field] = $value;
        }

        return $out;
    }

    protected function relationFields(Sager $sag): array
    {
        $kreditor = method_exists($sag, 'kreditor') ? $sag->kreditor()->first() : null;
        $debitor = method_exists($sag, 'debitor') ? $sag->debitor()->first() : null;
        
        // 🟢 Hent by via debitorens postnummer-relation (hvis modellen hedder Postnr og har en 'by'- eller 'navn'-kolonne)
        $byNavn = '';
        if ($debitor) {
            // Hvis relationen er loaded eller kan hentes:
            $postnrModel = method_exists($debitor, 'postnummer') ? $debitor->postnummer()->first() : null;
            if ($postnrModel) {
                $byNavn = $postnrModel->by ?? $postnrModel->navn ?? $postnrModel->bynavn ?? '';
            }
        }

        $ktrRecord = null;
        if (method_exists($sag, 'ktr')) {
            $ktrRecord = $sag->ktr instanceof \Illuminate\Database\Eloquent\Collection 
                ? $sag->ktr->first() 
                : $sag->ktr()->first();
        }

        $ktrTekst = '';
        if ($ktrRecord) {
            if (is_object($ktrRecord)) {
                $ktrTekst = $ktrRecord->navn ?? $ktrRecord->beskrivelse ?? $ktrRecord->forkortelse ?? '';
            } elseif (is_string($ktrRecord)) {
                $ktrTekst = $ktrRecord;
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
            'debitor_by'         => $byNavn, // 🟢 Hentes nu via Postnr-relationen
            'debitor_email'      => $debitor?->email ?? $debitor?->email2 ?? '',
            
            'ktr'                => $ktrTekst,
        ];
    }

    protected function computedFields(Sager $sag): array
    {
        // 🟢 Gør 'aktiv' læsbar (fx "Aktiv" / "Inaktiv" eller en beskrivelse i stedet for 1/0)
        $aktivTekst = $sag->aktiv ? 'Aktiv' : 'Inaktiv';
        
        // Hvis 'aktiv' i virkeligheden er et tekstfelt eller en kolonne på sagen, bruges den direkte:
        if (isset($sag->aktiv) && is_string($sag->aktiv)) {
            $aktivTekst = $sag->aktiv;
        }

        return [
            'today' => now()->format('d-m-Y'),
            'aktiv' => $aktivTekst, // 🟢 Sender læsbar tekst med i stedet for rå kode
        ];
    }
}