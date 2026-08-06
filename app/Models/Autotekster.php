<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autotekster extends Model
{

    public $table = 'autotekst';
    
    protected $fillable = [
        'tekst',
        'dato',
      ];
}