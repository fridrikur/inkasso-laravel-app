<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrevFilter extends Model
{
    protected $table = 'breve_filter';
    
    protected $fillable = [
        'brevID',
        'adresse',
        'dato',
        'navn',
        'sagsnr',
        'emne',
        'skjulalle',
        'visalle',
    ];

    public function brev()
    {
        return $this->belongsTo(Brev::class, 'brevID', 'id');
    }
}