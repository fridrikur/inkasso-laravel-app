<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Postnr extends Model
{
    protected $table = 'postnr';

    public static function getByFromPostnr(int $postnr): ?string
    {
        return Cache::remember("postnr_{$postnr}", now()->addDays(30), function () use ($postnr) {
            return DB::table('postnr')->where('postnr', $postnr)->value('by');
        });
    }
}