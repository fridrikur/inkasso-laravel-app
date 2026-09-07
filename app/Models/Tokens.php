<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tokens extends Model
{
    public $table = 'tokens';
    
    protected $fillable = [
        'token',
    ];
      
    public function sagers()
    {
        return $this->belongsToMany(Sager::class, 'sager_tokens', 'token_id', 'sag_id');
    }
}