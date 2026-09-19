<?php

namespace App\Livewire\Admin;

use App\Models\Sager;
use App\Models\Dokument;
use Livewire\Component;
use Livewire\WithPagination;

class DokumenterPortal extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        if (!$user->hasAnyRole(['Admin', 'Medarbejder'])) {
            abort(403);
        }

        $sager = Sager::query()
            // 1. Skal ALTID have mindst ét dokument (og filtrér på filtype hvis valgt)
            ->whereHas('dokumenter', function($query) {
                if ($this->filterType) {
                    $query->where('file_name', 'like', '%.' . $this->filterType);
                }
            })
            // 2. Hvis der søges, skal matchet enten være i dokumentnavnet ELLER sagsnummeret
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->whereHas('dokumenter', function($docQuery) {
                        $docQuery->where('file_name', 'like', '%' . $this->search . '%');
                        if ($this->filterType) {
                            $docQuery->where('file_name', 'like', '%.' . $this->filterType);
                        }
                    })
                    ->orWhere('sagsnr', 'like', '%' . $this->search . '%');
                });
            })
            // 3. Eager-load dokumenterne og kreditor
            ->with(['dokumenter' => function($query) {
                if ($this->search) {
                    // Hvis der søges på filnavn, kan du vælge at vise matchende filer
                    $query->where('file_name', 'like', '%' . $this->search . '%');
                }
                if ($this->filterType) {
                    $query->where('file_name', 'like', '%.' . $this->filterType);
                }
            }, 'kreditor'])
            ->latest()
            ->paginate(15);

        $totalDokumenterCount = Dokument::count();
        $totalStorageSize = Dokument::sum('file_size');

        return view('livewire.admin.dokumenter-portal', [
            'sager' => $sager,
            'totalDokumenterCount' => $totalDokumenterCount,
            'totalStorageSize' => $totalStorageSize,
        ]);
    }
}