<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class NotifikationsKonsulent extends Model
{

    protected $table = 'notifikations_konsulent';


    protected $fillable = [
        'notifikations_konsulent_id',
    ];

    public function konsulent(): BelongsTo
    {
        return $this->belongsTo(
            Konsulenter::class,
            'notifikations_konsulent_id'
        );
    }

    public static function has(Konsulenter $k): bool
    {
        return static::where(
            'notifikations_konsulent_id',
            $k->id
        )->exists();
    }


    public static function add(Konsulenter $k): void
    {
        static::firstOrCreate([
            'notifikations_konsulent_id'=>$k->id
        ]);
    }


    public static function remove(Konsulenter $k): void
    {
        static::where(
            'notifikations_konsulent_id',
            $k->id
        )->delete();
    }

}