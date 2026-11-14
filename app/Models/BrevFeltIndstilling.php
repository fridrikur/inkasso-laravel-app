<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BrevFeltIndstilling extends Model
{
    protected $table = 'brev_felt_indstillinger';

    protected $fillable = [
        'felt_navn',
        'visnings_navn',
        'kategori',
        'aktiv',
    ];

    protected $casts = [
        'aktiv' => 'boolean',
    ];

    /**
     * Seed/Synkroniser automatisk alle felter fra Sager og Debitorer modellerne,
     * så admin ikke skal oprette dem manuelt én efter én.
     */
    public static function synkroniserFelter(): void
    {
        $sagerFelter = (new Sager())->getFillable();
        
        $debitorFelter = [];
        if (class_exists(Debitorer::class)) {
            $debitorFelter = (new Debitorer())->getFillable();
            $debitorFelter = array_map(fn($f) => str_starts_with($f, 'debitor_') ? $f : 'debitor_' . $f, $debitorFelter);
        }

        $systemFelter = [
            'today', 'aktiv', 'firmanavn', 'debitor_navn', 'debitor_adresse', 
            'debitor_postnr', 'debitor_postnummer', 'debitor_by', 'debitor_email', 
            'ktr', 'dato', 'sagsnr', 'restgaeld_dkg', 'restgaeld_kreditor'
        ];

        $alleFelter = array_unique(array_merge($systemFelter, $sagerFelter, $debitorFelter));

        foreach ($alleFelter as $felt) {
            self::firstOrCreate(
                ['felt_navn' => $felt],
                [
                    'visnings_navn' => ucfirst(str_replace('_', ' ', $felt)),
                    'kategori' => str_starts_with($felt, 'debitor') ? 'Debitor' : (in_array($felt, ['today', 'aktiv', 'firmanavn']) ? 'System' : 'Sag'),
                    'aktiv' => true // Standard aktiv
                ]
            );
        }
    }
}