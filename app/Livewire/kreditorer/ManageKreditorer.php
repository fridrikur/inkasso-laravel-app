<?php

namespace App\Livewire\Kreditorer;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use App\Models\Kreditorer;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use App\Services\KreditorManagementService;

#[Lazy]
class ManageKreditorer extends Component
{
    #[On('kreditor-saved')]
    public function refreshList(array $payload) {}

    // Sletning & Overførsel State
    public ?Kreditorer $kreditorToDelete = null;
    public ?int $transferToKreditorId = null;
    public bool $showDeleteModal = false;
    public string $securityCode = '';
    public $transferTargets = [];

    // Standalone Overførsel (Valgfri direkte knap)
    public ?Kreditorer $kreditorToTransferFrom = null;
    public bool $showStandaloneTransferModal = false;

    public function opretnykreditor()
    {
        $this->dispatch('open-kreditor-modal');
    }

    /**
     * Åbner Slette-modalen (Klarer både 0 sager og >0 sager)
     */
    public function requestDelete($id)
    {
        $this->reset(['securityCode', 'transferToKreditorId']);

        $this->kreditorToDelete = Kreditorer::withCount('sager')->findOrFail($id);
        
        $this->transferTargets = Kreditorer::whereKeyNot($id)
            ->orderBy('navn')
            ->get();

        $this->showDeleteModal = true;
    }

    /**
     * Gennemfører sletning (og eventuel overførsel) med sikkerhedskode-validering
     */
    public function confirmDelete(KreditorManagementService $service)
    {
        if (!$this->kreditorToDelete) return;

        // 🟢 TJEK DOKUMENTERET SAGSANTAL DIREKTE PÅ DATERELATIONEN
        $hasSager = $this->kreditorToDelete->sager()->count() > 0;

        if ($hasSager) {
            // 1. Valider sikkerhedskode
            $expectedCode = SystemSetting::get('global_unlock_code');
            if (!$expectedCode || !Hash::check($this->securityCode, $expectedCode)) {
                $this->addError('securityCode', 'Forkert sikkerhedskode.');
                return;
            }

            // 2. Valider modtager-kreditor
            if (!$this->transferToKreditorId) {
                $this->addError('transferToKreditorId', 'Vælg venligst en modtager-kreditor til sagerne.');
                return;
            }

            $targetKreditor = Kreditorer::findOrFail($this->transferToKreditorId);
            $service->transferSagerAndDelete($this->kreditorToDelete, $targetKreditor);
        } else {
            // 0 sager: Slet direkte uden sikkerhedskode
            $service->delete($this->kreditorToDelete);
        }

        $this->cancelDelete();
        $this->dispatch('toast', message: 'Kreditor blev slettet.', type: 'success');
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->securityCode = '';
        $this->transferToKreditorId = null;
        $this->kreditorToDelete = null;
    }

    // Standalone overførsel uden sletning
    public function openTransferModal($id)
    {
        $this->reset(['transferToKreditorId']);
        $this->kreditorToTransferFrom = Kreditorer::withCount('sager')->findOrFail($id);
        
        $this->transferTargets = Kreditorer::whereKeyNot($id)
            ->orderBy('navn')
            ->get();

        $this->showStandaloneTransferModal = true;
    }

    public function executeStandaloneTransfer(KreditorManagementService $service)
    {
        if (!$this->kreditorToTransferFrom || !$this->transferToKreditorId) {
            $this->addError('transferToKreditorId', 'Vælg venligst en modtager-kreditor.');
            return;
        }

        $targetKreditor = Kreditorer::findOrFail($this->transferToKreditorId);
        $service->transferSager($this->kreditorToTransferFrom, $targetKreditor);

        $this->closeTransferModal();
        $this->dispatch('toast', message: 'Sager blev overført til ' . $targetKreditor->navn . '.', type: 'success');
    }

    public function closeTransferModal()
    {
        $this->showStandaloneTransferModal = false;
        $this->kreditorToTransferFrom = null;
        $this->transferToKreditorId = null;
    }

    public function render()
    {
        return view('livewire.kreditorer.manage-kreditorer', [
            'kreditorer' => Kreditorer::withCount('sager')
                ->orderBy('navn')
                ->get(),
        ]);
    }
}