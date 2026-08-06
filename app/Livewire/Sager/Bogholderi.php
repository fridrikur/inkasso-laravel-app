<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Models\Konsulenter;
use App\Traits\HasSagDialog;

class Bogholderi extends Component
{
    use HasSagDialog;

    public Sager $sag;
    public $konsulent_id;

    protected function getDialogType(): string
    {
        return 'bogholderi';
    }

    public function mount(Sager $sag)
    {
        $this->sag = $sag;

        $konsulenter = $sag->sagerkonsulent ?? collect();

        $hoved = $konsulenter->first(function ($k) {
            return method_exists($k, 'isHovedKonsulent') ? $k->isHovedKonsulent() : false;
        });

        $firstAssigned = $konsulenter->first();

        $this->konsulent_id = $hoved?->id ?? $firstAssigned?->id;
    }

    public function save()
    {
        $this->validate([
            'tekst'        => 'required|string',
            'konsulent_id' => 'required|exists:konsulenters,id',
        ]);

        $this->sendMessage(
            senderId: (int) $this->konsulent_id,
            senderType: 'konsulent'
        );
    }

    public function render()
    {
        return view('livewire.sager.bogholderi', [
            'dialogMessages' => $this->getDialogMessages(),
            'konsulenter'    => Konsulenter::orderBy('navn')->get(),
        ]);
    }
}