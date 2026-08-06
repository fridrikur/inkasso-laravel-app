<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kreditorer;

class ShowKreditorSager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public Kreditorer $kreditor;
    public string $kreditornavn;
    public string $search = '';

    public function mount(Kreditorer $kreditor): void
    {
        $this->kreditor = $kreditor;
        $this->kreditornavn = $kreditor->navn;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = trim($this->search);

        $query = $this->kreditor
            ->sager()
            ->with([
                'sagerdebitor.postnummer',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // Search directly on the sag
                    $q->where('sagsnr', 'like', '%' . $search . '%')
                        ->orWhere('hovedstol', 'like', '%' . $search . '%')

                        // Search related debitor fields
                        ->orWhereHas('sagerdebitor', function ($q2) use ($search) {
                            $q2->where('navn', 'like', '%' . $search . '%')
                                ->orWhere('adresse', 'like', '%' . $search . '%')
                                ->orWhere('postnr', 'like', '%' . $search . '%')

                                // Search by city via postnummer relation
                                ->orWhereHas('postnummer', function ($q3) use ($search) {
                                    $q3->where('by', 'like', '%' . $search . '%');
                                });
                        });
                });
            });

        return view('livewire.kreditorer.sager', [
            'sager' => $query
                ->latest('id')
                ->paginate(15),

            'total' => (clone $query)->count(),
        ]);
    }
}