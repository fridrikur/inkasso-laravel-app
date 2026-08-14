<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
}