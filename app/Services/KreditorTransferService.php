<?php

namespace App\Services;

use App\Models\Kreditorer;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class KreditorTransferService
{
    /**
     * Transfer all cases from one creditor to another.
     *
     * Returns the number of newly transferred cases.
     */
    public function transferSager(
        Kreditorer $from,
        Kreditorer $to
    ): int {
        if ($from->id === $to->id) {
            throw new RuntimeException(
                'Kilde- og modtager-kreditor må ikke være den samme.'
            );
        }

        return DB::transaction(function () use ($from, $to) {

            $sagIds = DB::table('sager_kreditor')
                ->where('kreditor_id', $from->id)
                ->pluck('sag_id');

            $transferred = 0;

            foreach ($sagIds as $sagId) {

                /*
                 * Check whether the target already owns this case.
                 */
                $exists = DB::table('sager_kreditor')
                    ->where('sag_id', $sagId)
                    ->where('kreditor_id', $to->id)
                    ->exists();

                if (!$exists) {

                    DB::table('sager_kreditor')->insert([
                        'sag_id' => $sagId,
                        'kreditor_id' => $to->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $transferred++;
                }

                /*
                 * Remove the case from the old creditor.
                 */
                DB::table('sager_kreditor')
                    ->where('sag_id', $sagId)
                    ->where('kreditor_id', $from->id)
                    ->delete();
            }

            return $transferred;
        });
    }
}