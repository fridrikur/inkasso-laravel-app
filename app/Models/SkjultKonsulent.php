<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkjultKonsulent extends Model
{
    protected $table = 'skjult_konsulent';

    protected $fillable = [
        'skjult_konsulent_id',
    ];


    public function konsulent(): BelongsTo
    {
        return $this->belongsTo(
            Konsulenter::class,
            'skjult_konsulent_id'
        );
    }


    public static function has(Konsulenter $k): bool
    {
        return static::where(
            'skjult_konsulent_id',
            $k->id
        )->exists();
    }


    public static function add(Konsulenter $k): void
    {
        static::firstOrCreate([
            'skjult_konsulent_id'=>$k->id
        ]);
    }


    public static function remove(Konsulenter $k): void
    {
        static::where(
            'skjult_konsulent_id',
            $k->id
        )->delete();
    }
}