<?php

namespace App\Livewire\Kreditor;

use App\Models\Sagsbehandler;
use App\Models\Kreditorer;
use Livewire\Component;

class SagsbehandlerFormModal extends Component
{
    public bool $showModal = false;
    public ?int $kreditorId = null;
    public ?int $editingId = null;

    public string $navn = '';
    public string $email = '';
    public string $tlf = '';
    public string $mobil = '';
    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected $listeners = [
        'open-sagsbehandler-create' => 'create',
        'open-sagsbehandler-edit'   => 'edit',
        'open-sagsbehandler-delete' => 'confirmDeleteModal', // <--- Lytter på slet fra table-actions
    ];    
    
    public function create($kreditorId = null)
    {
        $this->reset(['navn', 'email', 'tlf', 'mobil', 'editingId']);
        $this->kreditorId = $kreditorId;
        $this->editingId = null; // Tvinges til null ved oprettelse
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->reset(['navn', 'email', 'tlf', 'mobil', 'editingId', 'kreditorId']);
        
        $this->editingId = (int) $id;
        $sagsbehandler = Sagsbehandler::findOrFail($this->editingId);
        
        $this->navn  = $sagsbehandler->navn;
        $this->email = $sagsbehandler->email ?? '';
        $this->tlf   = $sagsbehandler->tlf ?? '';
        $this->mobil = $sagsbehandler->mobil ?? '';

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['navn', 'email', 'tlf', 'mobil', 'editingId', 'kreditorId']);
    }

    public function save()
    {
        $this->validate([
            'navn'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'tlf'   => 'nullable|string|max:50',
            'mobil' => 'nullable|string|max:50',
        ]);

        $data = [
            'navn'  => $this->navn,
            'email' => $this->email !== '' ? $this->email : null,
            'tlf'   => $this->tlf !== '' ? $this->tlf : null,
            'mobil' => $this->mobil !== '' ? $this->mobil : null,
        ];

        try {
            if ($this->editingId) {
                $sagsbehandler = Sagsbehandler::findOrFail($this->editingId);
                $sagsbehandler->update($data);
                $msg = 'Sagsbehandler opdateret.';
            } else {
                $sagsbehandler = Sagsbehandler::create($data);

                if ($this->kreditorId) {
                    $kreditor = Kreditorer::find($this->kreditorId);
                    $kreditor?->sagsbehandlere()->syncWithoutDetaching([$sagsbehandler->id]);
                }
                $msg = 'Sagsbehandler oprettet og tilknyttet.';
            }

            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
            $this->closeModal();
            $this->dispatch('kreditor-updated');

        } catch (\Illuminate\Database\QueryException $e) {
            // Tjek om fejlen er en duplikat-indtastning (MySQL fejlode 1062)
            if ($e->errorInfo[1] === 1062) {
                $this->addError('navn', 'Der findes allerede en sagsbehandler med dette navn.');
                $this->dispatch('toast', ['message' => 'Der findes allerede en sagsbehandler med dette navn.', 'type' => 'error']);
            } else {
                // Generel databasefejl
                $this->dispatch('toast', ['message' => 'Der opstod en databasefejl. Prøv igen.', 'type' => 'error']);
            }
        }
    }

    public function confirmDeleteModal($id = null)
    {
        // Hvis $id kommer som et array fra et dispatch, udlæser vi 'id' ellers tager vi værdien direkte
        $this->deletingId = is_array($id) ? ($id['id'] ?? null) : $id;
        
        // Hvis kreditorId ikke er sat, kan vi hente det fra den sagsbehandler der skal slettes, 
        // eller lade den finde det fra den aktuelle side.
        if (!empty($this->deletingId)) {
            $sagsbehandler = Sagsbehandler::find($this->deletingId);
            // Hvis sagsbehandleren har en kreditor-relation, kan vi finde den herfra
            // Ellers bruger vi bare den kreditor der er aktiv i Livewire (hvis den er sendt med)
            if (is_array($id) && isset($id['kreditorId'])) {
                $this->kreditorId = $id['kreditorId'];
            }
        }

        $this->showDeleteModal = true;
    }

    public function deleteSagsbehandler()
    {
        if ($this->deletingId) {
            $sagsbehandler = Sagsbehandler::find($this->deletingId);
            
            if ($sagsbehandler) {
                // Hvis kreditorId er kendt, sletter vi specifikt for den kreditor
                if ($this->kreditorId) {
                    $kreditor = Kreditorer::find($this->kreditorId);
                    $kreditor?->sagsbehandlere()->detach($this->deletingId);
                } else {
                    // Fallback: Fjerner tilknytningen fra alle kreditorer hvis kreditorId mangler
                    $sagsbehandler->kreditorer()->detach();
                }
            }

            $this->dispatch('toast', ['message' => 'Sagsbehandleren er fjernet fra kreditoren.', 'type' => 'success']);
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        
        // Genindlæs hovedkomponenten
        $this->dispatch('kreditor-updated');
    }

    public function render()
    {
        return view('livewire.kreditorer.sagsbehandler-form-modal');
    }
}