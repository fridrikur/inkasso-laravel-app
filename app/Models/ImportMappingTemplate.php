<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportMappingTemplate extends Model
{
    protected $fillable = [
        'kreditor_id',
        'navn',
        'mapping',
        'is_default',
    ];

    protected $casts = [
        'mapping' => 'array',
        'is_default' => 'boolean',
    ];

    public function kreditor()
    {
        return $this->belongsTo(Kreditorer::class, 'kreditor_id');
    }
}