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
    public bool $isHoved = false;

    public bool $showDeleteModal = false;
    public ?int $deletingId = null;

    protected $listeners = [
        'open-sagsbehandler-create' => 'create',
        'open-sagsbehandler-edit'   => 'edit',
        'open-sagsbehandler-delete' => 'confirmDeleteModal',
    ];    
    
    // 🟢 Accepterer kreditorId direkte som variabel
    public function create($kreditorId = null)
    {
        $this->reset(['navn', 'email', 'tlf', 'mobil', 'isHoved', 'editingId']);
        
        // Håndter hvis Livewire sender det som array eller standard variabel
        $this->kreditorId = is_array($kreditorId) ? ($kreditorId['kreditorId'] ?? null) : $kreditorId;
        $this->editingId = null;
        $this->showModal = true;
    }

    // 🟢 Accepterer både id og kreditorId direkte som separate variable
    public function edit($id, $kreditorId = null)
    {
        $this->reset(['navn', 'email', 'tlf', 'mobil', 'isHoved', 'editingId', 'kreditorId']);
        
        // Sikr at id og kreditorId fanges uanset om de kommer fladt eller i et array
        $sagsbehandlerId = is_array($id) ? ($id['id'] ?? null) : $id;
        if (is_array($id) && isset($id['kreditorId'])) {
            $this->kreditorId = $id['kreditorId'];
        } else {
            $this->kreditorId = $kreditorId;
        }

        $this->editingId = (int) $sagsbehandlerId;
        $sagsbehandler = Sagsbehandler::findOrFail($this->editingId);
        
        $this->navn  = $sagsbehandler->navn;
        $this->email = $sagsbehandler->email ?? '';
        $this->tlf   = $sagsbehandler->tlf ?? '';
        $this->mobil = $sagsbehandler->mobil ?? '';

        // Tjek om sagsbehandleren er hovedsagsbehandler
        if ($this->kreditorId) {
            $kreditor = Kreditorer::find($this->kreditorId);
            if ($kreditor && method_exists($kreditor, 'hovedsagsbehandler')) {
                $this->isHoved = $kreditor->hovedsagsbehandler()->where('sagsbehandlers.id', $sagsbehandler->id)->exists();
            }
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['navn', 'email', 'tlf', 'mobil', 'isHoved', 'editingId', 'kreditorId']);
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
                $msg = 'Sagsbehandler oprettet.';
            }

            // Knyt til kreditor og hovedsagsbehandler
            if ($this->kreditorId) {
                $kreditor = Kreditorer::find($this->kreditorId);
                if ($kreditor) {
                    $kreditor->sagsbehandlere()->syncWithoutDetaching([$sagsbehandler->id]);

                    if (method_exists($kreditor, 'hovedsagsbehandler')) {
                        if ($this->isHoved) {
                            $kreditor->hovedsagsbehandler()->sync([$sagsbehandler->id]);
                        } else {
                            $isCurrentHoved = $kreditor->hovedsagsbehandler()->where('sagsbehandlers.id', $sagsbehandler->id)->exists();
                            if ($isCurrentHoved) {
                                $kreditor->hovedsagsbehandler()->detach($sagsbehandler->id);
                            }
                        }
                    }
                }
            }

            $this->dispatch('toast', ['message' => $msg, 'type' => 'success']);
            $this->closeModal();
            $this->dispatch('kreditor-updated');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                $this->addError('navn', 'Der findes allerede en sagsbehandler med dette navn.');
                $this->dispatch('toast', ['message' => 'Der findes allerede en sagsbehandler med dette navn.', 'type' => 'error']);
            } else {
                $this->dispatch('toast', ['message' => 'Der opstod en databasefejl. Prøv igen.', 'type' => 'error']);
            }
        }
    }

    public function confirmDeleteModal($id = null)
    {
        $this->deletingId = is_array($id) ? ($id['id'] ?? null) : $id;
        
        if (!empty($this->deletingId)) {
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
                if ($this->kreditorId) {
                    $kreditor = Kreditorer::find($this->kreditorId);
                    $kreditor?->sagsbehandlere()->detach($this->deletingId);
                    
                    if (method_exists($kreditor, 'hovedsagsbehandler')) {
                        $kreditor->hovedsagsbehandler()->detach($this->deletingId);
                    }
                } else {
                    $sagsbehandler->kreditorer()->detach();
                }
            }

            $this->dispatch('toast', ['message' => 'Sagsbehandleren er fjernet fra kreditoren.', 'type' => 'success']);
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->dispatch('kreditor-updated');
    }

    public function render()
    {
        return view('livewire.kreditorer.sagsbehandler-form-modal');
    }
}