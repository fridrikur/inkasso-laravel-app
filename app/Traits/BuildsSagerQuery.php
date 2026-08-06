<?php

namespace App\Traits;

use App\Models\Sager;

trait BuildsSagerQuery
{
    protected function baseSagerQuery()
    {
        return Sager::query()
            ->join('sager_debitor', 'sagers.id', '=', 'sager_debitor.sag_id')
            ->join('debitors', 'sager_debitor.debitor_id', '=', 'debitors.id')

            ->join('sager_kreditor', 'sagers.id', '=', 'sager_kreditor.sag_id')
            ->join('kreditors', 'sager_kreditor.kreditor_id', '=', 'kreditors.id')

            ->leftJoin('sag_activities', 'sagers.id', '=', 'sag_activities.sag_id')
            ->leftJoin('users as editors', 'sag_activities.user_id', '=', 'editors.id')

            ->select(
                'sagers.*',
                'debitors.navn as debitor_navn',
                'kreditors.navn as kreditor_navn',
                'editors.name as editor_name',
                'sag_activities.is_editing',
                'sag_activities.heartbeat_at'
            )
            ->distinct();
    }
}