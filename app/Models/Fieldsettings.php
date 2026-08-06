<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fieldsettings extends Model
{
    protected $fillable = [
        'field_name', 'alias', 'visible', 'required', 'readonly', 'roles', 
        'field_type', 'description', 'legacy', 'section', 'column', 'sort_order'
    ];

    protected $casts = [
        'visible' => 'boolean',
        'required' => 'boolean',
        'readonly' => 'boolean',
        'roles' => 'array',
    ];

    public function models(): BelongsToMany
    {
        return $this->belongsToMany(TabModel::class, 'fieldsettings_model', 'fieldsettings_id', 'tab_model_id');
    }
}
