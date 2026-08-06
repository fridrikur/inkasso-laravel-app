<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Debitorer extends Model
{
    use HasFactory;
    public $table = 'debitors';
    protected $fillable = [
        'debitorid',
        'navn',
        'co',
        'adresse',
        'postnr',
        'email',
        'tlf',
        'mobil',
        'adropl',
        'pnr',
        'kontakt_bemaerkning',
    ];
      
    public function sager(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Sager::class,
            'sager_debitor',
            'debitor_id',
            'sag_id'
        );
    }

    public function postnummer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Postnr::class, 'postnr', 'postnr');
    }
}