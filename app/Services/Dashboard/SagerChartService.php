<?php

namespace App\Services\Dashboard;

use App\Models\Sager;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SagerChartService
{
    public function getSagerUdvikling(): array
    {
        return Cache::remember(
            'dashboard.admin.sager.udvikling',
            now()->addMinutes(15),
            function () {

                $months = collect(range(11, 0))
                    ->map(fn ($i) =>
                        now()->subMonths($i)->format('Y-m')
                    );


                $rows = Sager::query()

                    ->join(
                        'sager_kreditor',
                        'sagers.id',
                        '=',
                        'sager_kreditor.sag_id'
                    )

                    ->join(
                        'kreditors',
                        'sager_kreditor.kreditor_id',
                        '=',
                        'kreditors.id'
                    )

                    ->whereBetween(
                        'sagers.created_at',
                        [
                            now()
                                ->subMonths(11)
                                ->startOfMonth(),

                            now()->endOfMonth()
                        ]
                    )

                    ->selectRaw("
                        DATE_FORMAT(modtaget, '%Y-%m') as month,
                        kreditors.navn as kreditor_navn,
                        COUNT(*) as total
                    ")
                    ->groupBy('month', 'kreditor_navn')

                    ->get();



                $datasets = [];


                foreach ($rows->groupBy('kreditor_navn') as $navn => $items) {

                    $datasets[] = [

                        'label' => $navn,


                        'data' => $months
                            ->map(function ($month) use ($items) {

                                return $items
                                    ->where(
                                        'month',
                                        $month
                                    )
                                    ->sum('total');

                            })
                            ->values()
                            ->toArray()

                    ];

                }



                return [

                    'labels' => $months
                        ->map(fn ($month) =>
                            Carbon::createFromFormat(
                                'Y-m',
                                $month
                            )
                            ->translatedFormat('M Y')
                        )
                        ->toArray(),


                    'datasets'=>$datasets

                ];

            }
        );
    }



    public function clearCache(): void
    {
        Cache::forget(
            'dashboard.admin.sager.udvikling'
        );
    }
}