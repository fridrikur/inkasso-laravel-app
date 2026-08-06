<?php

namespace App\Livewire\Kreditorer;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy; // 1. Importer Lazy
use App\Models\Kreditorer;     // Rettet lille 'm' til stort 'M'
use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\KreditorDeletionService;

#[Lazy] // 2. Fortæl Livewire at komponenten skal loades asynkront
class ShowKreditorer extends Component
{
    #[On('kreditor-saved')]
    public function refreshList(array $payload)
    {
        // $payload['kreditorId'], $payload['navn']
    }

    public ?Kreditorer $kreditorToDelete = null;
    public ?int $transferToKreditorId = null;

    public bool $showDeleteModal = false;
    public bool $showTransferMode = false;

    public string $securityCode = '';

    public $transferTargets = [];

    public function opretnykreditor()
    {
        return redirect()->to('/kreditorer/create');
    }

    public function requestDelete($id)
    {
        $this->reset([
            'securityCode',
            'transferToKreditorId',
            'showTransferMode',
        ]);

        $this->kreditorToDelete = Kreditorer::withCount('sager')->findOrFail($id);
        
        $this->transferTargets = Kreditorer::whereKeyNot($id)
            ->orderBy('navn')
            ->get();

        $this->showDeleteModal = true;
    }

    public function confirmDelete(KreditorDeletionService $service)
    {
        if (!$this->kreditorToDelete) {
            return;
        }

        $expected = SystemSetting::get('global_unlock_code');

        if (!$expected || !Hash::check($this->securityCode, $expected)) {
            $this->addError('securityCode', 'Forkert sikkerhedskode.');
            return;
        }

        if ($this->kreditorToDelete->sager()->exists()) {
            $this->addError('securityCode', 'Kreditor har stadig sager.');
            return;
        }

        $service->deleteKreditor($this->kreditorToDelete);

        $this->reset([
            'showDeleteModal',
            'kreditorToDelete',
            'securityCode',
        ]);

        $this->dispatch('toast', [
            'message' => 'Kreditor slettet.',
            'type' => 'success',
        ]);
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->securityCode = '';
        $this->kreditorToDelete = null;
    }

    public function enableTransferMode()
    {
        $this->showTransferMode = true;
    }

    public function transferSager(KreditorDeletionService $service)
    {
        if (!$this->kreditorToDelete || !$this->transferToKreditorId) {
            return;
        }

        if ($this->kreditorToDelete->id === $this->transferToKreditorId) {
            $this->addError('transferToKreditorId', 'Du kan ikke vælge samme kreditor.');
            return;
        }

        DB::transaction(function () use ($service) {
            $old = $this->kreditorToDelete;
            $new = Kreditorer::findOrFail($this->transferToKreditorId);

            $sagIds = $old->sager()->pluck('sagers.id');

            $new->sager()->syncWithoutDetaching($sagIds);
            $old->sager()->detach();

            $service->deleteKreditor($old);
        });

        $this->dispatch('toast', [
            'message' => 'Sager overført og kreditor slettet.',
            'type' => 'success',
        ]);

        $this->showTransferMode = false;
    }
    
    public function placeholder()
    {
        return <<<'HTML'
            <x-ui-loader type="kreditorer" />
        HTML;
    }

    public function render()
    {
        return view('livewire.kreditorer.show-kreditorer', [
            'kreditorer' => Kreditorer::withCount('sager')
                ->orderBy('navn')
                ->get(),
        ]);
    }
}