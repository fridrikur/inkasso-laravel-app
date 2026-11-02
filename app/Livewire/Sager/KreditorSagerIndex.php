<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use App\Models\Dokument;

class KreditorSagerIndex extends Component
{
    use WithPagination;

    use WithFileUploads; // Gør det muligt at uploade filer med Livewire
    
    public $search = '';

    // Slide-over drawer state
    public $selectedSagId = null;
    
    public $showDrawer = false;

    public $activeSag = null; // Den aktive sag

    public $newDokument; // Binder til inputfeltet i formen

    #[Url(keep: true)]
    public $statusFilter = 'all'; 

    public function mount($status = null, $filter = null)
    {
        $param = $status ?? $filter ?? request('status') ?? request('filter');

        if ($param) {
            if ($param === 'afsluttede' || $param === 'closed') {
                $this->statusFilter = 'closed';
            } elseif ($param === 'aktive' || $param === 'active') {
                $this->statusFilter = 'active';
            } elseif ($param === 'pending' || $param === 'afventer') {
                $this->statusFilter = 'pending';
            } else {
                $this->statusFilter = 'all';
            }
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    // --- DRAWER ACTIONS ---

public function openSag($sagId)
    {
        $this->activeSag = null;

        $this->activeSag = Sager::with([
            'debitor.postnummer', 
            'konsulent', 
            'sagsbehandler', 
            'ktr',
            'udlaeg',
            'dialogs.messages.sender',
            'dokumenter' // Sørg for at hente dokumenter med her
        ])->find($sagId);

        $this->showDrawer = true;
    }

    public function closeSag()
    {
        $this->showDrawer = false;
        $this->activeSag = null;
    }

    public function hydrate()
    {
        if ($this->activeSag && $this->showDrawer) {
            $this->activeSag = Sager::with([
                'debitor.postnummer', 
                'konsulent', 
                'sagsbehandler', 
                'ktr',
                'udlaeg',
                'dialogs.messages.sender',
                'dokumenter'
            ])->find($this->activeSag->id);
        }
    }

    public function uploadDokument()
    {
        $this->validate([
            'newDokument' => 'required|file|max:10240', // Maks 10MB
        ]);

        $file = $this->newDokument;
        $path = $file->store('dokumenter/' . $this->activeSag->id, 'public');

        Dokument::create([
            'sag_id'        => $this->activeSag->id,
            'file_name'     => $file->getClientOriginalName(),
            'file_path'     => $path,
            'file_size'     => $file->getSize(),
            'uploaded_date' => now(),
        ]);

        // Genindlæs sagen så listerne opdateres med det samme
        $this->openSag($this->activeSag->id);

        $this->reset('newDokument');

        $this->dispatch('toast', [
            'message' => 'Dokumentet blev uploadet succesfuldt!',
            'type'    => 'success'
        ]);
    }

    public function deleteDokument($dokumentId)
    {
        $dokument = Dokument::find($dokumentId);

        if ($dokument && $dokument->sag_id === $this->activeSag->id) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($dokument->file_path);
            $dokument->delete();

            // Genindlæs sagen
            $this->openSag($this->activeSag->id);

            $this->dispatch('toast', [
                'message' => 'Dokumentet blev slettet.',
                'type'    => 'success'
            ]);
        }
    }
    
    // Hent antallet af dokumenter til tælleren på fanen
    public function getDokumenterCountProperty()
    {
        return $this->sag->dokumenter()->count();
    }
    // --- RENDER ---

    public function render()
    {
        $user = auth()->user();
        $kreditor = $user->kreditorer()->firstOrFail();

        $search = trim($this->search ?? '');

        $afslutningsTyper = \App\Models\afslutning::all();
        $bemaerkningTyper = \App\Models\bemaerkning::all();

        $allKreditorSager = Sager::query()
            ->whereHas('kreditor', function ($q) use ($kreditor) {
                $q->where('kreditor_id', $kreditor->id);
            })
            ->with(['konsulent', 'afslutning', 'bemaerkning'])
            ->get();

        $countAll = $allKreditorSager->count();
        $countActive = $allKreditorSager->whereNull('afsluttet')->count();
        $countPending = $allKreditorSager->filter(fn($s) => is_null($s->afsluttet) && $s->konsulent->isEmpty())->count();
        $countClosed = $allKreditorSager->whereNotNull('afsluttet')->count();

        $countsAfslutning = [];
        foreach ($afslutningsTyper as $type) {
            $countsAfslutning[$type->id] = $allKreditorSager->filter(fn($s) => $s->afslutning->contains('id', $type->id))->count();
        }

        $countsBemaerkning = [];
        foreach ($bemaerkningTyper as $bem) {
            $countsBemaerkning[$bem->id] = $allKreditorSager->filter(fn($s) => $s->bemaerkning->contains('id', $bem->id))->count();
        }

        $query = Sager::query()
            ->whereHas('kreditor', function ($q) use ($kreditor) {
                $q->where('kreditor_id', $kreditor->id);
            })
            ->when($this->statusFilter === 'active', fn($q) => $q->whereNull('afsluttet'))
            ->when($this->statusFilter === 'closed', fn($q) => $q->whereNotNull('afsluttet'))
            ->when($this->statusFilter === 'pending', fn($q) => $q->whereNull('afsluttet')->whereDoesntHave('konsulent'))
            ->when(str_starts_with($this->statusFilter ?? '', 'afslutning_'), function ($q) {
                $id = str_replace('afslutning_', '', $this->statusFilter);
                $q->whereHas('afslutning', fn($q2) => $q2->where('afslutning.id', $id));
            })
            ->when(str_starts_with($this->statusFilter ?? '', 'bemaerkning_'), function ($q) {
                $id = str_replace('bemaerkning_', '', $this->statusFilter);
                $q->whereHas('bemaerkning', fn($q2) => $q2->where('bemaerkning.id', $id));
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sagsnr', 'like', '%' . $search . '%')
                      ->orWhere('hovedstol', 'like', '%' . $search . '%')
                      ->orWhereHas('debitor', function ($q2) use ($search) {
                          $q2->where('navn', 'like', '%' . $search . '%')
                             ->orWhere('adresse', 'like', '%' . $search . '%')
                             ->orWhereHas('postnummer', fn($q3) => $q3->where('by', 'like', '%' . $search . '%'))  
                             ->orWhere('postnr', 'like', '%' . $search . '%');
                      });
                });
            });

        $sager = $query
            ->with(['debitor', 'debitor.postnummer', 'konsulent', 'ktr', 'bemaerkning', 'afslutning'])
            ->latest()
            ->paginate(15);

        return view('livewire.sager.kreditor-sager-index', [
            'sager'             => $sager,
            'suggestion'        => null,
            'afslutningsTyper'  => $afslutningsTyper,
            'bemaerkningTyper'  => $bemaerkningTyper,
            'countAll'          => $countAll,
            'countActive'       => $countActive,
            'countPending'      => $countPending,
            'countClosed'       => $countClosed,
            'countsAfslutning'  => $countsAfslutning,
            'countsBemaerkning' => $countsBemaerkning,
        ]);
    }
}