<?php

namespace App\Services;

use App\Models\Kreditorer;
use Illuminate\Support\Facades\DB;

class KreditorDeletionService
{
    public function deleteKreditor(Kreditorer $kreditor): void
    {
        DB::transaction(function () use ($kreditor) {

            /*
             * Delete import sessions.
             * import_session_sager is automatically cleaned up
             * because it uses cascadeOnDelete().
             */
            $kreditor->importSessions()->delete();

            /*
             * Remove user assignments
             */
            $kreditor->users()->detach();

            /*
             * Remove sagsbehandler assignments
             */
            $kreditor->hovedsagsbehandler()->detach();
            $kreditor->sagsbehandlere()->detach();

            /*
             * Remove creditor specific fields
             */
            $kreditor->sagerFields()->delete();

            /*
             * Remove any remaining case relations.
             * Normally there shouldn't be any if transfer was used.
             */
            $kreditor->sager()->detach();

            /*
             * Finally delete the creditor.
             */
            $kreditor->delete();
        });
    }
}