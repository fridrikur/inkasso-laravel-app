<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function sager()
    {
        return $this->belongsToMany(
            Sager::class,
            'import_session_sager',
            'import_session_id',
            'sag_id'
        )->withTimestamps();
    }
}
