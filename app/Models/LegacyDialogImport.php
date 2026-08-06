<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyDialogImport extends Model
{
    protected $table = 'legacy_dialog_imports';

    protected $fillable = [
        'legacy_id',
        'legacy_sag_id',
        'tekst',
        'dato',
        'username',
        'type',
        'processed',
    ];

    protected $casts = [
        'dato' => 'datetime',
        'processed' => 'boolean',
    ];
}