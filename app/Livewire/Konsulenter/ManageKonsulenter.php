<?php

namespace App\Livewire\Konsulenter;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Konsulenter;
use App\Models\HovedKonsulent;
use App\Models\SkjultKonsulent;
use App\Models\NotifikationsKonsulent;

use App\Services\KonsulentService;
use App\Services\ToastService;

use Illuminate\Support\Facades\DB;
use App\Traits\HasCrudModal; // 🟢 Vores genbrugelige Trait

class ManageKonsulenter extends Component
{
    use WithPagination;
    use HasCrudModal; // 🟢 Implementerer modal-tilstande

    private function service(): KonsulentService
    {
        return app(KonsulentService::class);
    }

    public string $search = '';
    public int $perPage = 10;
    public string $activeRoleTab = 'alle';

    // Modal felter
    public ?Konsulenter $activeKonsulent = null;
    public string $modalNavn = '';
    public string $modalEmail = '';
    public ?string $modalTlf = null;
    public ?string $modalMobil = null;

    public bool $modalIsHoved = false;
    public bool $modalIsSkjult = false;
    public bool $modalIsNotifikation = false;

    // 🟢 Påkrævede metoder fra HasCrudModal Traiten
    public function resetForm(): void
    {
        $this->reset([
            'activeKonsulent',
            'modalNavn',
            'modalEmail',
            'modalTlf',
            'modalMobil',
            'modalIsHoved',
            'modalIsSkjult',
            'modalIsNotifikation',
        ]);

        $this->resetValidation();
    }

    public function loadItemData($id): void
    {
        $k = Konsulenter::findOrFail($id);

        $this->activeKonsulent = $k;
        $this->modalNavn = $k->navn;
        $this->modalEmail = $k->email;
        $this->modalTlf = $k->tlf;
        $this->modalMobil = $k->mobil;

        $this->modalIsHoved = HovedKonsulent::current()?->id === $k->id;
        $this->modalIsSkjult = SkjultKonsulent::has($k);
        $this->modalIsNotifikation = NotifikationsKonsulent::has($k);
    }

    /*
    |--------------------------------------------------------------------------
    | Handlinger
    |--------------------------------------------------------------------------
    */

    public function updatedSearch() { $this->resetPage(); }
    public function updatedActiveRoleTab() { $this->resetPage(); }

    public function setRoleTab(string $role)
    {
        $this->activeRoleTab = $role;
        $this->resetPage();
    }

    public function save()
    {
        $this->validate([
            'modalNavn' => 'required|string|max:255',
            'modalEmail' => 'required|email|max:255',
        ]);

        DB::transaction(function () {
            $konsulent = $this->service()->save(
                $this->activeKonsulent,
                [
                    'navn'  => $this->modalNavn,
                    'email' => $this->modalEmail,
                    'tlf'   => $this->modalTlf,
                    'mobil' => $this->modalMobil,
                ]
            );

            $this->service()->syncRoles(
                $konsulent,
                [
                    'hoved' => $this->modalIsHoved,
                    'skjult' => $this->modalIsSkjult,
                    'notifikation' => $this->modalIsNotifikation,
                ]
            );
        });

        $this->closeFormModal();

        $this->dispatch(
            'notify',
            ...app(ToastService::class)->success('Konsulent gemt')
        );
    }

    // 🟢 Sletning tilpasset HasCrudModal ($deletingId)
    public function confirmDelete()
    {
        if (!$this->deletingId) return;

        $k = Konsulenter::findOrFail($this->deletingId);

        if ($k->sager()->exists()) {
            $this->addError('delete', 'Kan ikke slette konsulent med aktive sager.');
            $this->cancelDelete();
            return;
        }

        $this->service()->delete($k);
        $this->cancelDelete();

        $this->dispatch('notify', message: 'Konsulenten blev slettet.', type: 'success');
    }

    public function render()
    {
        $query = Konsulenter::query()
            ->withExists(['skjultRole', 'notifikationRole']);

        if ($this->activeRoleTab === 'hoved') {
            $query->where('id', HovedKonsulent::current()?->id);
        }

        if ($this->activeRoleTab === 'notif') {
            $query->whereIn('id', NotifikationsKonsulent::pluck('notifikations_konsulent_id'));
        }

        if ($this->activeRoleTab === 'skjult') {
            $query->whereIn('id', SkjultKonsulent::pluck('skjult_konsulent_id'));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('navn', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.konsulenter.manage-konsulenter', [
            'konsulenter' => $query->orderBy('navn')->paginate($this->perPage),
            'hovedKonsulent' => HovedKonsulent::current(),
            'notifikationCount' => NotifikationsKonsulent::count(),
            'skjultCount' => SkjultKonsulent::count(),
        ]);
    }
}