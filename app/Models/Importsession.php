<?php
// ImportSession.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 🟢 Tilføjet denne linje
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ImportSession extends Model
{
    protected $fillable = [
        'kreditor_id',
        'file_path',
        'status',
        'inserted',
        'failed',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function sager(): BelongsToMany
    {
        return $this->belongsToMany(
            Sager::class,
            'import_session_sager',
            'import_session_id',
            'sag_id'
        )->withTimestamps();
    }

    /**
     * Relationen til Kreditor-modellen
     */
    public function kreditor(): BelongsTo
    {
        return $this->belongsTo(Kreditorer::class, 'kreditor_id');
    }
}