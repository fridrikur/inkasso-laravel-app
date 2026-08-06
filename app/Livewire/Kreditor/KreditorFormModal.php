<?php

namespace App\Livewire\Kreditor;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Kreditorer;

class KreditorFormModal extends Component
{
    public ?Kreditorer $kreditor = null;
    public string $navn = '';
    public string $lotusID = '';
    public bool $showModal = false;
    public $usedLotusIds = [];

    protected $rules = [
        'navn' => 'required|string|max:255',
    ];

    #[On('open-kreditor-modal')]
    public function openModal(?int $kreditorId = null)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->kreditor = $kreditorId
            ? Kreditorer::findOrFail($kreditorId)
            : null;

        $this->navn = $this->kreditor?->navn ?? '';
        $this->lotusID = $this->kreditor?->lotusID ?? '';

        $this->showModal = true;
    }

    public function mount()
    {
        $this->usedLotusIds = \App\Models\Kreditorer::pluck('lotusID')->toArray();
    }

    public function getSuggestedLotusIdProperty()
    {
        return (\App\Models\Kreditorer::max('lotusID') ?? 0) + 1;
    }

    public function save()
    {
        $this->validate();

        if ($this->kreditor) {
            $this->kreditor->update([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);
        } else {
            $this->kreditor = Kreditorer::create([
                'navn' => $this->navn,
                'lotusID' => $this->lotusID,
            ]);
        }

        $this->showModal = false;

        // Dispatch browser event compatible with Alpine
        $this->dispatch('kreditor-saved', [
            'kreditorId' => $this->kreditor->id,
            'navn' => $this->kreditor->navn,
        ]);
    }

    public function closeModal()
    {
        $this->reset(['kreditor', 'navn', 'lotusID', 'showModal']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('liveWire.kreditor.kreditor-form-modal');
    }
}
