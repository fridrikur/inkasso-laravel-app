<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SagFieldSetting extends Model
{
    protected $fillable = [
        'allowed_fields'
    ];

    protected $casts = [
        'allowed_fields' => 'array'
    ];
}