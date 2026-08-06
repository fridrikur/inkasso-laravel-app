<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportMapping extends Model
{
    protected $fillable = [
        'name',
        'kreditor_id',
        'mapping',
    ];

    protected $casts = [
        'mapping' => 'array',
    ];
}
