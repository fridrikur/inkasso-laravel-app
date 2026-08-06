<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GdprScan extends Model
{
    protected $fillable = [
        'expired',
        'expiring',
        'user_id',
    ];

    protected $casts = [
        'expired' => 'array',
        'expiring' => 'array',
    ];
}