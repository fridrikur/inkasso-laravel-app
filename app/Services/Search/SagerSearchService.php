<?php

namespace App\Services\Search;

use App\Models\Sager;
use Illuminate\Database\Eloquent\Builder;

class SagerSearchService
{
    public function query(array $filters): Builder
    {
        $query = Sager::query()
            ->with([
                'sagerdebitor',
                'sagerkreditor',
                'sagerStatus',
                'sagerAfslutning',
                'sagersagsbehandler',
                'sagerkonsulent',
            ]);

        return $this->apply($query, $filters);
    }

    public function apply(Builder $query, array $filters): Builder
    {
        $this->applyTextFilters($query, $filters);
        $this->applyRelationFilters($query, $filters);
        $this->applyStatusFilters($query, $filters);
        $this->applyDateFilters($query, $filters);
        $this->applyFinancialFilters($query, $filters);

        return $query;
    }

    public array $filters = [

        // Global
        'search' => '',

        // Case
        'sagsnr' => '',
        'stelnr' => '',

        // Parties
        'debitor' => '',
        'kreditor_id' => null,
        'konsulent_id' => null,

        // Status
        'status' => 'all',
        'status_ids' => [],
        'afslutning_id' => null,

        // Economy
        'betalt' => '',
        'restgaeld_min' => null,
        'restgaeld_max' => null,

        // Dates
        'modtaget_from' => null,
        'modtaget_to' => null,
        'afsluttet_from' => null,
        'afsluttet_to' => null,
        
    ];

    /*
    |--------------------------------------------------------------------------
    | Text filters
    |--------------------------------------------------------------------------
    */

    protected function applyTextFilters(Builder $query, array $filters): void
    {
        $query

            ->when(
                filled($filters['search'] ?? null),
                function ($q) use ($filters) {

                    $search = trim($filters['search']);

                    $q->where(function ($query) use ($search) {

                        $query
                            ->where('sagsnr', 'like', "%{$search}%")
                            ->orWhere('stelnr', 'like', "%{$search}%")

                            ->orWhereHas('sagerdebitor', function ($debitor) use ($search) {
                                $debitor->where('navn', 'like', "%{$search}%");
                            })

                            ->orWhereHas('sagerkreditor', function ($kreditor) use ($search) {
                                $kreditor->where('navn', 'like', "%{$search}%");
                            });

                    });

                }
            )

            ->when(
                filled($filters['sagsnr'] ?? null),
                fn ($q) => $q->where(
                    'sagsnr',
                    'like',
                    '%' . $filters['sagsnr'] . '%'
                )
            )

            ->when(
                filled($filters['stelnr'] ?? null),
                fn ($q) => $q->where(
                    'stelnr',
                    'like',
                    '%' . $filters['stelnr'] . '%'
                )
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Relation filters
    |--------------------------------------------------------------------------
    */

    protected function applyRelationFilters(Builder $query, array $filters): void
    {
        $query

            ->when(
                filled($filters['debitor'] ?? null),
                fn ($q) => $q->whereHas(
                    'sagerdebitor',
                    fn ($d) => $d->where(
                        'navn',
                        'like',
                        '%' . $filters['debitor'] . '%'
                    )
                )
            )

            ->when(
                filled($filters['kreditor_id'] ?? null),
                fn ($q) => $q->whereHas(
                    'sagerkreditor',
                    fn ($k) => $k->where(
                        'kreditor_id',
                        $filters['kreditor_id']
                    )
                )
            )

            ->when(
                filled($filters['sagsbehandler_id'] ?? null),
                fn ($q) => $q->whereHas(
                    'sagersagsbehandler',
                    fn ($s) => $s->where(
                        'user_id',
                        $filters['sagsbehandler_id']
                    )
                )
            )

            ->when(
                filled($filters['konsulent_id'] ?? null),
                fn ($q) => $q->whereHas(
                    'sagerkonsulent',
                    fn ($k) => $k->whereKey($filters['konsulent_id'])
                )
            )

            ->when(
                filled($filters['restgaeld_min'] ?? null),
                fn ($q) => $q->where(function ($q) use ($filters) {

                    $q->where('restgaeld_kreditor', '>=', $filters['restgaeld_min'])
                    ->orWhere('restgaeld_dkg', '>=', $filters['restgaeld_min']);

                })
            )

            ->when(
                filled($filters['restgaeld_max'] ?? null),
                fn ($q) => $q->where(function ($q) use ($filters) {

                    $q->where('restgaeld_kreditor', '<=', $filters['restgaeld_max'])
                    ->orWhere('restgaeld_dkg', '<=', $filters['restgaeld_max']);

                })
            )

            ->when(
                filled($filters['afslutning_id'] ?? null),
                fn ($q) => $q->whereHas(
                    'sagerAfslutning',
                    fn ($a) => $a->whereKey($filters['afslutning_id'])
                )
            );
        }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    protected function applyStatusFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['status_ids'])) {

            $query->whereHas('sagerStatus', function ($status) use ($filters) {

                $status->whereIn(
                    'sager_status.status_id',
                    $filters['status_ids']
                );

            });

        }

        match ($filters['status'] ?? 'all') {
            'active' => $query->whereNull('afsluttet'),
            'closed' => $query->whereNotNull('afsluttet'),
            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Dates
    |--------------------------------------------------------------------------
    */

    protected function applyDateFilters(Builder $query, array $filters): void
    {
        $query

            ->when(
                filled($filters['modtaget_to'] ?? null),
                fn ($q) => $q->where(
                    'modtaget',
                    '<=',
                    $filters['modtaget_to'].' 23:59:59'
                )
            )

            ->when(
                filled($filters['modtaget_from'] ?? null),
                fn ($q) => $q->where(
                    'modtaget',
                    '>=',
                    $filters['modtaget_from'].' 00:00:00'
                )
            )

            ->when(
                filled($filters['afsluttet_from'] ?? null),
                fn ($q) => $q->where(
                    'afsluttet',
                    '>=',
                    $filters['afsluttet_from'].' 00:00:00'
                )
            )

            ->when(
                filled($filters['afsluttet_to'] ?? null),
                fn ($q) => $q->where(
                    'afsluttet',
                    '<=',
                    $filters['afsluttet_to'].' 23:59:59'
                )
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Financial
    |--------------------------------------------------------------------------
    */

    protected function applyFinancialFilters(Builder $query, array $filters): void
    {
        /*
        |--------------------------------------------------------------------------
        | ØKONOMISK STATUS
        |--------------------------------------------------------------------------
        | betalt / restance / restgaeld
        */
        $query->when(
            filled($filters['oekonomisk_status'] ?? null),
            function ($q) use ($filters) {

                match ($filters['oekonomisk_status']) {

                    'betalt' =>
                        $q->whereNotNull('betalt'),

                    'restance' =>
                        $q->whereNull('betalt'),

                    'restgaeld' =>
                        $q->where(function ($sub) {
                            $sub->whereNotNull('restgaeld_dkg')
                                ->orWhereNotNull('restgaeld_kreditor');
                        }),

                    default => null
                };
            }
        );

        /*
        |--------------------------------------------------------------------------
        | RESTGÆLD TEXT FILTER (NOT RANGE)
        |--------------------------------------------------------------------------
        */
        $query->when(
            filled($filters['restgaeld'] ?? null),
            function ($q) use ($filters) {

                $value = $filters['restgaeld'];

                $q->where(function ($sub) use ($value) {
                    $sub->where('restgaeld_dkg', 'like', "%{$value}%")
                        ->orWhere('restgaeld_kreditor', 'like', "%{$value}%");
                });
            }
        );
    }

    public function paginate(
    array $filters,
    int $perPage = 20,
    string $sortField = 'modtaget',
    string $sortDirection = 'desc'
    )
    {
        return $this
            ->query($filters)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
}