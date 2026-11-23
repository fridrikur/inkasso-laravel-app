<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BrevType extends Model
{
    protected $table = 'brev_typer';

    protected $fillable = [
        'navn',
        'sorteringsorden',
        'aktiv',
    ];

    protected $casts = [
        'sorteringsorden' => 'integer',
        'aktiv' => 'boolean',
    ];

    public function breve(): HasMany
    {
        return $this->hasMany(Brev::class, 'brevtype_id');
    }
}