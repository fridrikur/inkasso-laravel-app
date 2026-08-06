<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class konsulentFieldsetting extends Model
{
    use HasFactory;

    protected $table = 'konsulent_fieldsettings';

    protected $fillable = [
        'field_name', 'alias', 'visible', 'required', 'readonly',
        'roles', 'field_type', 'description', 'legacy', 'section',
        'column', 'sort_order',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'required' => 'boolean',
        'readonly' => 'boolean',
        'roles' => 'array',
    ];
}
