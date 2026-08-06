<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tokens extends Model
{
    public $table = 'tokens';
    
    protected $fillable = [
        'token',
    ];
      
    public function sagertokens()
    {
        return $this->belongsToMany(Tokens::class, 'sager_tokens', 'sag_id', 'token_id');
    }
}