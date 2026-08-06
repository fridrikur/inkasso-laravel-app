<?php

namespace App\Services;

use App\Models\Konsulenter;
use App\Models\Kreditorer;
use App\Models\Sager;
use App\Models\Sagsbehandler;

use Illuminate\Support\Facades\DB;

class TransferService
{
    /*
    |--------------------------------------------------------------------------
    | Konsulent
    |--------------------------------------------------------------------------
    */

    private function transferPivot(
        string $table,
        string $column,
        int $from,
        int $to
    ): int {

        return DB::table($table)
            ->where($column, $from)
            ->update([
                $column => $to,
            ]);

    }

    public function transferKonsulent(
        Konsulenter $from,
        Konsulenter $to
    ): int {

        return $this->transferPivot(
            'sager_konsulent',
            'konsulent_id',
            $from->id,
            $to->id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Sagsbehandler
    |--------------------------------------------------------------------------
    */

    public function transferSagsbehandler(
        Sagsbehandler $from,
        Sagsbehandler $to
    ): int {

            return $this->transferPivot(
            'sager_sagsbehandler',
            'sagsbehandler_id',
            $from->id,
            $to->id
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Kreditor
    |--------------------------------------------------------------------------
    */

    public function transferKreditor(
        Kreditorer $from,
        Kreditorer $to
    ): int {

        return $this->transferPivot(
            'sager_kreditor',
            'kreditor_id',
            $from->id,
            $to->id
        );

    }
}