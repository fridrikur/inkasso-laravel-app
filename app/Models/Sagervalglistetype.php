<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sagervalglistetype extends Model
{
    public $table = 'sagervalglistetyper';

    protected $fillable = [
        'navn',
      ];
    public function sagervalglistetype()
    {
        return $this->belongsToMany(Sagervalglistetype::class, 'sagervalgliste_typer', 'sagervalgliste_id','type_id');
    }
}