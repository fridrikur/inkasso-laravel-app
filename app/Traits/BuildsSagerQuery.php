<?php

namespace App\Traits;

use App\Models\Sager;

trait BuildsSagerQuery
{
    protected function baseSagerQuery()
    {
        // 1. Start med en ren og hurtig query på Sager
        $query = Sager::query()->select('sagers.*');

        // 2. Tilføj søgning via subqueries (meget hurtigere end INNER JOINs)
        $query->when(property_exists($this, 'search') && !empty($this->search), function ($q) {
            $q->where(function ($sub) {
                $sub->where('sagers.id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('sagerdebitor', function ($debQuery) {
                        $debQuery->where('navn', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('sagerkreditor', function ($kredQuery) {
                        $kredQuery->where('navn', 'like', '%' . $this->search . '%');
                    });
            });
        });

        return $query;
    }
}