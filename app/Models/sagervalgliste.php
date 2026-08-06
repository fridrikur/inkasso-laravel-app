<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sagervalgliste extends Model
{
    protected $fillable = [
        'navn',
        'forkortelse',
      ];

    public function sagervalgliste()
    {
      return $this->belongsToMany(Sagervalgliste::class, 'sager_valgliste', 'sagervalgliste_id', 'sag_id');
    }
    public function sagervalglistetype()
    {
        return $this->belongsToMany(Sagervalglistetype::class, 'sagervalgliste_typer', 'sagervalgliste_id','type_id');
    } 
}