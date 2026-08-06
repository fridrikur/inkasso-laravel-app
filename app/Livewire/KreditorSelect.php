<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kreditorer;

class KreditorSelect extends Component
{
    public string $kreditornr = '';        // LotusID input
    public ?int $selectedKreditor = null;  // Kreditor ID
    public array $options = [];

    public string $dispatchEventName = 'kreditor-changed';

    public function mount(array $options = [], ?int $selectedKreditor = null)
    {
        $this->options = $options ?: Kreditorer::pluck('navn', 'id')->toArray();

        if ($selectedKreditor) {
            $this->selectedKreditor = $selectedKreditor;
            $kreditor = Kreditorer::find($selectedKreditor);
            $this->kreditornr = $kreditor?->lotusID ?? '';
        }
    }

    /** 🔁 When user types LotusID */
    public function updatedKreditornr($value)
    {
        $kreditor = Kreditorer::where('lotusID', trim($value))->first();
        $this->selectedKreditor = $kreditor?->id ?? null;

        $this->dispatch($this->dispatchEventName, [
            'kreditorId' => $this->selectedKreditor,
        ]);
    }

    /** 🔁 When user selects a Kreditor from dropdown */
    public function updatedSelectedKreditor($value)
    {
        $kreditor = Kreditorer::find($value);
        dd('Triggered', $kreditor?->toArray());
        
        $this->kreditornr = $kreditor?->lotusID ?? '';

        $this->dispatch($this->dispatchEventName, [
            'kreditorId' => $value,
        ]);
    }

    
    public function render()
    {
        return view('liveWire.kreditor-select');
    }
}
