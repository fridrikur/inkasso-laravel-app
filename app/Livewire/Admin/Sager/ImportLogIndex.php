<?php

namespace App\Livewire\Admin\Sager; // Tilpas namespace til din fil

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ImportSession;
use App\Models\Kreditorer;

class ImportLogIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $kreditorFilter = '';
    public string $statusFilter = ''; // 🟢 TILFØJ DENNE LINJE
    public int $perPage = 15;

    public function updatingSearch() { $this->resetPage(); }
    public function updatingKreditorFilter() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function render()
    {
        $query = ImportSession::query()
        ->with('kreditor')
        ->orderBy('id', 'desc');

        // Søgning
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('file_path', 'like', '%' . $this->search . '%')
                  ->orWhereHas('kreditor', function ($k) {
                      $k->where('navn', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Kreditor filter
        if (!empty($this->kreditorFilter)) {
            $query->where('kreditor_id', $this->kreditorFilter);
        }

        // Status faner filter
        if ($this->statusFilter === 'completed') {
            $query->where('status', 'completed')->where('failed', 0);
        } elseif ($this->statusFilter === 'failed') {
            $query->where(function ($q) {
                $q->where('status', 'failed')
                  ->orWhere('failed', '>', 0);
            });
        } elseif ($this->statusFilter === 'rolled_back') {
            $query->where('status', 'rolled_back');
        }

        return view('sager.import.import-log-index', [
            'sessions' => $query->paginate($this->perPage),
            'kreditorer' => Kreditorer::select('id', 'navn')->orderBy('navn')->get(),
        ]);
    }
}