<?php

namespace App\Services\Gdpr;

use App\Models\Sager;
use Illuminate\Support\Collection;

class SagerGdprService
{
    /**
     * Anonymiserer et array eller samling af sags-ID'er i en sikker transaktion.
     */
    public function anonymizeMany(array $ids): int
    {
        $sager = Sager::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($sager as $sag) {
            $sag->anonymize();
            $count++;
        }

        return $count;
    }

    /**
     * Henter statistik over samlede GDPR-tilstande.
     */
    public function getSummaryStats(): array
    {
        return [
            'expired_count' => Sager::gdprExpired()->count(),
            'expiring_soon_count' => Sager::gdprExpiringSoon()->count(),
        ];
    }
}