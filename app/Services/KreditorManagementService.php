<?php

namespace App\Services;

use App\Models\Kreditorer;
use App\Models\User;
use App\Models\Sagsbehandler;
use Illuminate\Support\Facades\DB;

class KreditorManagementService
{
    /**
     * Delete a creditor and all creditor-specific data.
     *
     * Assumes all cases have already been transferred (or none exist).
     */
    public function delete(Kreditorer $kreditor): void
    {
        $kreditorId = $kreditor->id;

        DB::transaction(function () use ($kreditor, $kreditorId) {

            /*
            |--------------------------------------------------------------------------
            | Safety check
            |--------------------------------------------------------------------------
            */
            if ($kreditor->sager()->exists()) {
                throw new \RuntimeException(
                    'Kreditor has active cases. Transfer them before deleting.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Import Sessions
            |--------------------------------------------------------------------------
            */
            if (method_exists($kreditor, 'importSessions')) {
                $kreditor->importSessions()->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            $userIds = $kreditor->users()->pluck('users.id');
            $kreditor->users()->detach();

            if ($userIds->isNotEmpty()) {
                User::whereIn('id', $userIds)->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Sagsbehandlere
            |--------------------------------------------------------------------------
            */
            $sagsIds = $kreditor->sagsbehandlere()->pluck('sagsbehandlers.id');

            if (method_exists($kreditor, 'hovedsagsbehandler')) {
                $relation = $kreditor->hovedsagsbehandler();

                if (method_exists($relation, 'detach')) {
                    $relation->detach();
                } elseif (method_exists($relation, 'dissociate')) {
                    $relation->dissociate();
                    $kreditor->save();
                }
            }

            $kreditor->sagsbehandlere()->detach();

            if ($sagsIds->isNotEmpty()) {
                Sagsbehandler::whereIn('id', $sagsIds)->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Custom fields
            |--------------------------------------------------------------------------
            */
            if (method_exists($kreditor, 'sagerFields')) {
                $kreditor->sagerFields()->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Detach remaining case relations if any
            |--------------------------------------------------------------------------
            */
            $kreditor->sager()->detach();
            /*
            |--------------------------------------------------------------------------
            | Delete
            |--------------------------------------------------------------------------
            */

            // Omgå Observers og Model Events der forsøger at slå den slettede kreditor op
            Kreditorer::withoutEvents(function () use ($kreditor) {
                if (method_exists($kreditor, 'trashed') && $kreditor->trashed()) {
                    $kreditor->forceDelete();
                } else {
                    $kreditor->delete();
                }
            });
        });
    }
}