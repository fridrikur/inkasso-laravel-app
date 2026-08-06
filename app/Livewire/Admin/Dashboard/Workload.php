<?php

class Workload extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard.workload', [
            'konsulentStats' => Konsulenter::withCount('sager')->pluck('sager_count', 'navn'),
            'sagsbehandlerStats' => Sagsbehandler::withCount('sagersagsbehandler')->pluck('sagersagsbehandler_count', 'navn'),
        ]);
    }
}