<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sagsbehandler extends Model
{
  protected $table = 'sagsbehandlers';

  protected $fillable = [
        'navn',
        'email',
        'tlf',
        'mobil',
      ];
    
    // In the Sagsbehandlere model
  public function kreditorer(): BelongsToMany
    {
        return $this->belongsToMany(
            Kreditorer::class,
            'kreditor_sagsbehandler',
            'sagsbehandler_id',
            'kreditor_id'
        );
    }
  public function sagersagsbehandler(): BelongsToMany
  {
      return $this->belongsToMany(Sager::class, 'sager_sagsbehandler', 'sagsbehandler_id', 'sag_id');
  }
  public function hovedsagsbehandler(): BelongsToMany
  {
      return $this->belongsToMany(Kreditorer::class, 'kreditor_hoved_sagsbehandler', 'sagsbehandler_id', 'kreditor_id');
  }
  public static function forKreditor($kreditorId): array
  {
    return static::select('sagsbehandlers.id', 'sagsbehandlers.navn')
        ->join('kreditor_sagsbehandler', 'sagsbehandlers.id', '=', 'kreditor_sagsbehandler.sagsbehandler_id')
        ->where('kreditor_sagsbehandler.kreditor_id', $kreditorId)
        ->pluck('sagsbehandlers.navn', 'sagsbehandlers.id') // <- executes the query
        ->toArray();
  }

  // Scope to get the hovedsagsbehandler for a given kreditor
    public function scopeHovedSagsbehandlerForKreditor(
        Builder $query,
        int $kreditorId
    ): Builder
    {
        return $query->whereHas('hovedsagsbehandler', function ($q) use ($kreditorId) {
            $q->where('kreditor_id', $kreditorId);
        });
    }
}