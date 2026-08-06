<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Brev;

class BrevOrder extends Component
{
    public string $activeTab = 'A-D';

    public array $tabs = [
        'A-D' => ['A', 'B', 'C', 'D'],
        'E-H' => ['E', 'F', 'G', 'H'],
        'I-L' => ['I', 'J', 'K', 'L'],
        'M-P' => ['M', 'N', 'O', 'P'],
        'Q-T' => ['Q', 'R', 'S', 'T'],
        'U-Z' => ['U', 'V', 'W', 'X', 'Y', 'Z'],
    ];

    protected $listeners = ['updateOrder'];

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Brev::where('id', $id)->update([
                'brevpos' => $index + 1,
            ]);
        }
        // 🔔 Notify browser
        $this->dispatch('toast', 
            type: 'success',
            message: 'Brevenes rækkefølge er gemt'
        );
    }

    public function getBreveProperty()
    {
        $letters = $this->tabs[$this->activeTab];

        return Brev::query()
            ->where(function ($q) use ($letters) {
                foreach ($letters as $letter) {
                    $q->orWhere('titel', 'LIKE', $letter . '%');
                }
            })
            ->orderBy('brevpos')
            ->orderBy('titel')
            ->get();
    }

    public function render()
    {
        return view('liveWire.admin.brev-order');
    }
}
