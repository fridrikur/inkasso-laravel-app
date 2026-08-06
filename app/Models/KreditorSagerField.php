<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KreditorSagerField extends Model
{
    protected $fillable = [
        'kreditor_id', 'field_name', 'visible', 'required', 'editable', 'default_value'
    ];

    public function kreditor()
    {
        return $this->belongsTo(Kreditorer::class);
    }
}
