<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kreditorer extends Model
{
    use HasFactory;
    public $table = 'kreditors';
    
    protected $fillable = [
        'navn',
        'lotusID',
    ];
      
    public function sager(): BelongsToMany
    {
        return $this->belongsToMany(Sager::class, 'sager_kreditor', 'kreditor_id', 'sag_id');
    }
    public function hovedsagsbehandlere(): BelongsToMany
    {
      return $this->belongsToMany(Sagsbehandler::class, 'kreditor_hoved_sagsbehandler', 'kreditor_id', 'sagsbehandler_id');
    }
    public function sagsbehandlere(): BelongsToMany
    {
        return $this->belongsToMany(
            Sagsbehandler::class,
            'kreditor_sagsbehandler',
            'kreditor_id',
            'sagsbehandler_id'
        );
    }
    public function sagerFields(): HasMany
    {
        return $this->hasMany(KreditorSagerField::class, 'kreditor_id');
    }
    // Return only allowed fields for the kreditor
    
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'kreditor_user',
            'kreditor_id',
            'user_id'
        );
    }
    public function importSessions(): HasMany
    {
        return $this->hasMany(ImportSession::class, 'kreditor_id');
    }
}