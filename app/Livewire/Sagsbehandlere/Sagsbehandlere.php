<?php

namespace App\Livewire\Sagsbehandlere;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sagsbehandler;
use App\Models\Kreditorer;
use App\Livewire\forms\SagsbehandlerForm;
use Illuminate\Support\Facades\DB;

class Sagsbehandlere extends Component
{
    use WithPagination;

    // Livewire 3 will auto-wire this form because it's typed
    public SagsbehandlerForm $form;

    // 🟢 Gør $kreditor nullable ELLER initier den i mount()
    public ?Kreditorer $kreditor = null;

    public $search = '';
    public $perPage = 10;

    public bool $showModal = false;
    public ?Sagsbehandler $activeSagsbehandler = null;

    /**
     * 🟢 Livewire Mount Lifecycle
     * Modtager enten en eksisterende Kreditor model fra routeren / forældre-komponenten,
     * eller finder en fallback, hvis den ikke er videregivet.
     */
    public function mount(?Kreditorer $kreditor = null, ?int $kreditorId = null)
    {
        if ($kreditor && $kreditor->exists) {
            $this->kreditor = $kreditor;
        } elseif ($kreditorId) {
            $this->kreditor = Kreditorer::findOrFail($kreditorId);
        } else {
            // Fallback hvis komponenten tilgås direkte uden valgt kreditor:
            // f.eks. hent første kreditor eller brug session
            $this->kreditor = Kreditorer::first() ?? new Kreditorer();
        }
    }

    // --- reset pagination when search/perpage changes
    public function updatedSearch() { $this->resetPage(); }
    public function updatedPerPage() { $this->resetPage(); }

    public function openModal(?int $id = null)
    {
        if ($id) {
            $s = Sagsbehandler::findOrFail($id);
            $this->activeSagsbehandler = $s;

            $this->form->sagsbehandler = $s;
            $this->form->navn  = $s->navn;
            $this->form->email = $s->email;
            $this->form->tlf   = $s->tlf;
            $this->form->mobil = $s->mobil;

            $this->form->is_hoved = $this->kreditor->exists 
                ? $s->hovedsagsbehandler()->where('kreditor_id', $this->kreditor->id)->exists()
                : false;
        } else {
            $this->activeSagsbehandler = null;

            $this->form->sagsbehandler = null;
            $this->form->navn  = '';
            $this->form->email = '';
            $this->form->tlf   = null;
            $this->form->mobil = null;
            $this->form->is_hoved = false;
            $this->resetValidation();
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->activeSagsbehandler = null;
        $this->form->sagsbehandler = null;
        $this->form->navn = '';
        $this->form->email = '';
        $this->form->tlf = null;
        $this->form->mobil = null;
        $this->form->is_hoved = false;
        $this->resetValidation();
    }

    public function save()
    {
        $sags = $this->form->save();

        if (! $this->activeSagsbehandler && $this->kreditor->exists) {
            $sags->kreditor()->attach($this->kreditor->id);
        }

        if ($this->kreditor->exists) {
            if ($this->form->is_hoved) {
                DB::table('kreditor_hoved_sagsbehandler')->where('kreditor_id', $this->kreditor->id)->delete();
                DB::table('kreditor_hoved_sagsbehandler')->insert([
                    'kreditor_id' => $this->kreditor->id,
                    'sagsbehandler_id' => $sags->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('kreditor_hoved_sagsbehandler')
                    ->where('kreditor_id', $this->kreditor->id)
                    ->where('sagsbehandler_id', $sags->id)
                    ->delete();
            }
        }

        session()->flash('message', 'Sagsbehandler gemt!');
        $this->closeModal();
    }

    public function setHoved(int $sagsbehandlerId)
    {
        if ($this->kreditor->exists) {
            $this->kreditor->hovedsagsbehandler()->sync([$sagsbehandlerId]);
            $this->kreditor->load('hovedsagsbehandler');
            $this->dispatch('toast', ['message' => 'Hovedsagsbehandler sat.']);
        }
    }

    public function unsetHoved(int $sagsbehandlerId)
    {
        if ($this->kreditor->exists) {
            $this->kreditor->hovedsagsbehandler()->detach($sagsbehandlerId);
            $this->kreditor->load('hovedsagsbehandler');
            $this->dispatch('toast', ['message' => 'Hovedsagsbehandler fjernet.']);
        }
    }

    public function render()
    {
        // Hvis der er en aktiv kreditor, filtreres på denne
        $query = Sagsbehandler::query();

        if ($this->kreditor && $this->kreditor->exists) {
            $query->whereHas('kreditorer', fn($q) =>
                $q->where('kreditor_id', $this->kreditor->id)
            );
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('navn', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('liveWire.sagsbehandlere.index', [
            'sagsbehandlere' => $query->paginate($this->perPage),
        ]);
    }
}