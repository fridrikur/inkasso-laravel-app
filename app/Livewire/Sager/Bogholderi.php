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

    public function mount(Sager $sag): void
    {
        $this->sag = $sag;

        $konsulenter = $sag->sagerkonsulent ?? collect();

        $hoved = $konsulenter->first(function ($k) {
            return method_exists($k, 'isHovedKonsulent') ? $k->isHovedKonsulent() : false;
        });

        $firstAssigned = $konsulenter->first();

        $this->konsulent_id = $hoved?->id ?? $firstAssigned?->id;
    }

    public function save(): void
    {
        $this->validate([
            'tekst' => 'required|string|min:1',
        ]);

        // 🟢 Gemmes direkte med den indloggede brugers (auth) ID
        $this->sendMessage(
            senderId: auth()->id()
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