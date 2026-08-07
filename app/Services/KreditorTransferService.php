<?php

namespace App\Services;

use App\Models\Kreditorer;
use Illuminate\Support\Facades\DB;

class KreditorTransferService
{
    public function __construct(
        protected KreditorManagementService $management
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer cases
    |--------------------------------------------------------------------------
    */

    public function transferSager(
        Kreditorer $from,
        Kreditorer $to
    ): void {

        if ($from->is($to)) {
            throw new \InvalidArgumentException(
                'Cannot transfer cases to the same creditor.'
            );
        }

        DB::transaction(function () use ($from, $to) {

            $sagIds = $from->sager()->pluck('sagers.id');

            if ($sagIds->isEmpty()) {
                return;
            }

            $to->sager()->syncWithoutDetaching($sagIds);
            $from->sager()->detach($sagIds);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer and delete
    |--------------------------------------------------------------------------
    */

    public function transferAndDelete(
        Kreditorer $from,
        Kreditorer $to
    ): void {

        DB::transaction(function () use ($from, $to) {

            $this->transferSager($from, $to);

            $this->management->delete($from);

        });
    }
}