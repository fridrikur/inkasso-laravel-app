<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    public $table = 'metas';
    
    protected $fillable = [
        'navn',
      ];
}