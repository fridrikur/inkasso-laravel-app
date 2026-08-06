<?php

namespace App\Livewire\Sager;

use App\Models\Sager;
use App\Models\Status;
use Livewire\Component;
use Livewire\WithPagination;

class StatusPage extends Component
{
    use WithPagination;

    public Status $status;

    public function mount(Status $status)
    {
        $this->status = $status;
    }

    public function render()
    {
        $sager = Sager::query()
            ->with([
                'sagerkreditor',
                'sagerdebitor',
            ])
            ->whereHas('sagerStatus', function ($q) {
                $q->where('status.id', $this->status->id);
            })
            ->orderByDesc('modtaget')
            ->paginate(25);

        // Since this page is only for one status, you can group it by the status name
        $groupedResults = [
            $this->status->tekst => [
                'items' => $sager,
                'total' => $sager->total(),
                'status' => $this->status,
            ],
        ];

        return view('livewire.sager.status-page', compact('groupedResults', 'sager'));
    }
}