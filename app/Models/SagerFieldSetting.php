<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SagerFieldSetting extends Model
{
    protected $table = 'sager_fieldsettings';

    protected $fillable = [
        'field_name',
        'alias',
        'visible',
        'required',
        'readonly',
        'roles',
        'field_type',
        'description',
        'legacy',
        'section',
        'column',
        'sort_order',
    ];

    protected $casts = [
        'visible' => 'boolean',
        'required' => 'boolean',
        'readonly' => 'boolean',
        'roles' => 'array',
    ];

    public function isFinancial(): bool
    {
        return in_array($this->field_name, [
            'hovedstol','renter','gebyr','startgebyr','indbetalt',
            'ialt','restgaeld','restgaeld_kreditor','restgaeld_dkg'
        ]);
    }
}
