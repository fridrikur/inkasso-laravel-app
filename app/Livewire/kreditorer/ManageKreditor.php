<?php

namespace App\Livewire\Kreditorer;

use Livewire\Component;
use App\Models\Kreditorer;
use App\Models\Sagsbehandler;
use App\Models\User;
use App\Models\SystemSetting;
use App\Services\KreditorManagementService;
use App\Services\KreditorTransferService;
use App\Services\ToastService; // 🟢
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ManageKreditor extends Component
{
    public ?int $kreditorId = null;
    public ?Kreditorer $kreditor = null;

    public string $securityCode = '';
    public ?int $transferToKreditorId = null;

    public ?Collection $transferTargets = null;

    protected KreditorManagementService $management;
    protected KreditorTransferService $transfer;
    protected ToastService $toast;

    public bool $showUserModal = false;
    public ?User $activeUser = null;
    public string $userName = '';
    public string $userEmail = '';
    public ?string $userPassword = null;

    public bool $showSagsModal = false;
    public ?Sagsbehandler $activeSagsbehandler = null;
    public string $modalNavn = '';
    public ?string $modalEmail = null;
    public ?string $modalTlf = null;
    public ?string $modalMobil = null;

    public bool $showDeleteModal = false;

    public function boot(
        KreditorManagementService $management,
        KreditorTransferService $transfer,
        ToastService $toast
    ): void {
        $this->management = $management;
        $this->transfer = $transfer;
        $this->toast = $toast;
    }

    public function mount(Kreditorer $kreditor): void
    {
        $this->kreditorId = $kreditor->id;
        $this->loadKreditorData();
    }

    public function loadKreditorData()
    {
        if (!$this->kreditorId) {
            return;
        }

        $this->kreditor = Kreditorer::query()
            ->with([
                'users:id,name,email',
                'sagsbehandlere:id,navn,email,tlf,mobil',
                'sager' => fn($q) => $q->with('sagerdebitor')->latest()->take(10),
            ])
            ->withCount('sager')
            ->find($this->kreditorId);
    }

    public function requestDelete()
    {
        $this->resetValidation();
        $this->securityCode = '';
        $this->transferToKreditorId = null;

        if ($this->kreditorId) {
            $this->transferTargets = Kreditorer::query()
                ->whereKeyNot($this->kreditorId)
                ->orderBy('navn')
                ->get();
        }

        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        $this->resetValidation();

        if (!$this->kreditor) {
            return $this->redirect(route('kreditorer.index'));
        }

        if ($this->kreditor->sager()->exists()) {
            $code = SystemSetting::get('global_unlock_code');

            if (!$code || !Hash::check($this->securityCode, $code)) {
                $this->addError('securityCode', 'Forkert sikkerhedskode.');
                return;
            }

            if (!$this->transferToKreditorId) {
                $this->addError('transferToKreditorId', 'Vælg en modtager.');
                return;
            }

            $target = Kreditorer::findOrFail($this->transferToKreditorId);
            $this->transfer->transferAndDelete($this->kreditor, $target);
        } else {
            $this->management->delete($this->kreditor);
        }

        session()->flash('toast', $this->toast->success('Kreditor slettet.'));

        return $this->redirect(route('kreditorer.index'));
    }

    public function closeModals()
    {
        $this->showUserModal = false;
        $this->showSagsModal = false;
        $this->showDeleteModal = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.kreditorer.manage-kreditor');
    }
}