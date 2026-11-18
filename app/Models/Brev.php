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
        'margin_left',
        'margin_right',
        'linjeafstand',
        'navn_top_margin',    // Ændret fra margin_top (default: 5.45 cm)
        'dato_margin_top',    // Ændret fra right_margin_top
        'emne_top_margin',    // Ændret fra emne_top
        'body_margin_top',
        'navn_font_size',
        'emne_font_size',
        'body_font_size',
        'font_family',
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