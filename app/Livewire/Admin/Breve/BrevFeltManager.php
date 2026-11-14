<?php

namespace App\Livewire\Admin\Breve;

use App\Models\BrevFeltIndstilling;
use Livewire\Component;

class BrevFeltManager extends Component
{
    public function mount()
    {
        // Sørg for at alle nye kolonne-felter automatisk oprettes i tabellen, når siden indlæses
        BrevFeltIndstilling::synkroniserFelter();
    }

    public function toggleFelt($id)
    {
        $felt = BrevFeltIndstilling::findOrFail($id);
        $felt->aktiv = !$felt->aktiv;
        $felt->save();

        $this->dispatch('toast', message: 'Feltets status blev opdateret.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.breve.brev-felt-manager', [
            'felter' => BrevFeltIndstilling::orderBy('kategori')->orderBy('visnings_navn')->get()->groupBy('kategori')
        ]);
    }
}