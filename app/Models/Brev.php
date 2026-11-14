<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Brev extends Model
{
    protected $table = 'breve';

    protected $fillable = [
        'titel',
        'brevpos',
        'emne',
        'tekst',
        'beskyttet',
        'brevtype',
        'margin_top',
        'margin_left',
        'margin_right',
    ];

    protected function tekst(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value 
                ? str_replace(['\r\n', '\r', '\n', "\r\n", "\r", "\n"], '', $value) 
                : null,
        );
    }
}