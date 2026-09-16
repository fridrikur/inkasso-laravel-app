<?php

class Workload extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard.workload', [
            'konsulentStats' => Konsulenter::withCount('sager')->pluck('sager_count', 'navn'),
            'sagsbehandlerStats' => Sagsbehandler::withCount('sagsbehandler')->pluck('sagsbehandler_count', 'navn'),
        ]);
    }
}