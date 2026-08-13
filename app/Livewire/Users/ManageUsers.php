<?php

namespace App\Livewire\Users;

use App\Models\Kreditorer;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Attributes\Lazy;

#[Lazy]
class ManageUsers extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    #[Url]
    public string $role = 'Medarbejder';

    // -------------------------------------------------
    // Filters
    // -------------------------------------------------
    public string $search = '';
    public ?string $roleFilter = null;
    public int $perPage = 10;
    public string $kreditorSearch = '';
    public ?int $kreditor_id = null;
    
    // -------------------------------------------------
    // Modal state
    // -------------------------------------------------
    public bool $showUserModal = false;
    public ?int $activeUserId = null;

    // 🟢 Slettemodal tilstand
    public bool $showDeleteModal = false;
    public ?int $deleteUserId = null;
    public int $userHasSagerCount = 0;

    // 🟢 Tjek om der findes kreditorer i systemet
    public bool $hasKreditorer = true;

    // -------------------------------------------------
    // Cached counts / roles
    // -------------------------------------------------
    public int $totalUsers = 0;
    public int $adminCount = 0;
    public int $medarbejderCount = 0;
    public int $kreditorCount = 0;

    /** @var Collection<int, Role> */
    public Collection $roles;

    // -------------------------------------------------
    // Events
    // -------------------------------------------------
    #[On('user-updated')]
    public function userUpdated(): void
    {
        $this->refreshStats();
        $this->closeModal();
    }

    #[On('close-user-modal')]
    public function closeModal(): void
    {
        $this->showUserModal = false;
        $this->activeUserId = null;
    }

    public function openModal(?int $userId = null): void
    {
        // 🟢 ADVARSLE / BLOKERING NÅR BRUGEREN VIL OPRETTE EN KREDITOR-BRUGER UDEN EKSISTERENDE KREDITORER
        if (! $userId && $this->roleFilter === 'Kreditor' && ! $this->hasKreditorer) {
            $this->dispatch('toast', [
                'message' => 'Der findes ingen kreditorer i databasen! Opret venligst en kreditor først under Kreditoradministration.',
                'type'    => 'error'
            ]);
            return;
        }

        $this->activeUserId = $userId;
        $this->showUserModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->openModal($id);
    }

    // -------------------------------------------------
    // SoftDelete Handlinger
    // -------------------------------------------------
    // 🟢 Tjekker rettigheder før modalen åbnes
    // 🟢 1. ÅBNER SLETTEMODALEN (Med alle dine sikkerhedsregler)
    public function confirmDelete(int $id): void
{
    // 🛡️ REGEL 1: Bruger ID #1 må ALDRIG slettes eller udløse modal
    if ($id === 1) {
        $this->dispatch('toast', [
            'message' => 'Systemets primære administrator (Bruger #1) kan ikke deaktiveres.',
            'type'    => 'error'
        ]);
        return;
    }

    // 🛡️ REGEL 2: Beskyttelse mod at deaktivere sin egen aktuelt indloggede konto
    if ($id === auth()->id()) {
        $this->dispatch('toast', [
            'message' => 'Du kan ikke deaktivere din egen konto.',
            'type'    => 'error'
        ]);
        return;
    }

    $user = User::find($id);
    if (! $user) return;

    // 🛡️ REGEL 3: Brugere med Admin-rolle skal nedgraderes til Medarbejder før de kan deaktiveres
    if ($user->hasRole('Admin')) {
        $this->dispatch('toast', [
            'message' => 'Brugere med Admin-rollen kan ikke deaktiveres direkte. Skift først brugerens rolle til Medarbejder.',
            'type'    => 'error'
        ]);
        return;
    }

    $this->deleteUserId = $id;

    // Tæl historiske sager via konsulent-relationen
    $konsulent = \App\Models\Konsulenter::where('email', $user->email)->first();
    if ($konsulent) {
        $this->userHasSagerCount = \App\Models\Sager::whereHas('sagerkonsulent', function ($q) use ($konsulent) {
            $q->where('konsulenter.id', $konsulent->id);
        })->count();
    } else {
        $this->userHasSagerCount = 0;
    }

    $this->showDeleteModal = true;
}

    // 🟢 2. UDFØRER BEKRÆFTET DEAKTIVERING (Når der trykkes "Deaktiver" i modalen)
    public function confirmDeleteModal(): void
    {
        if (! $this->deleteUserId) return;

        // Ekstra dobbelt-tjek af sikkerhedsregler
        if ($this->deleteUserId === 1 || $this->deleteUserId === auth()->id()) {
            $this->cancelDelete();
            return;
        }

        $user = User::findOrFail($this->deleteUserId);

        if ($user->hasRole('Admin')) {
            $this->dispatch('toast', [
                'message' => 'En administrator kan ikke deaktiveres. Skift rollen først.',
                'type'    => 'error'
            ]);
            $this->cancelDelete();
            return;
        }

        $user->delete(); // SoftDelete

        $this->showDeleteModal = false;
        $this->deleteUserId = null;

        $this->refreshStats();
        $this->resetPage();

        $this->dispatch('toast', [
            'message' => 'Brugeren er blevet deaktiveret.',
            'type'    => 'success'
        ]);
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteUserId = null;
    }

    // -------------------------------------------------
    // Lifecycle
    // -------------------------------------------------
    public function mount(): void
    {
        $this->roles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->hasKreditorer = Kreditorer::exists();

        $this->refreshStats();

        if (request()->filled('role')) {
            $this->roleFilter = request()->string('role')->toString();
        }

        if (request()->filled('edit')) {
            $this->activeUserId = (int) request('edit');
            $this->showUserModal = true;
        }
    }

    // -------------------------------------------------
    // Filters
    // -------------------------------------------------
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
        $this->kreditor_id = null;
    }

    public function updatedKreditorSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKreditorId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'roleFilter', 'kreditorSearch', 'kreditor_id']);
        $this->perPage = 10;
        $this->resetPage();
    }

    protected function refreshStats(): void
    {
        $this->hasKreditorer = Kreditorer::exists();

        $counts = User::query()
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', User::class);
            })
            ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->selectRaw("
                COUNT(DISTINCT users.id) as total_users,
                COUNT(DISTINCT CASE WHEN roles.name = 'Admin' THEN users.id END) as admin_count,
                COUNT(DISTINCT CASE WHEN roles.name = 'Medarbejder' THEN users.id END) as medarbejder_count,
                COUNT(DISTINCT CASE WHEN roles.name = 'Kreditor' THEN users.id END) as kreditor_count
            ")
            ->first();

        $this->totalUsers = (int) ($counts->total_users ?? 0);
        $this->adminCount = (int) ($counts->admin_count ?? 0);
        $this->medarbejderCount = (int) ($counts->medarbejder_count ?? 0);
        $this->kreditorCount = (int) ($counts->kreditor_count ?? 0);
    }

    public function placeholder()
    {
        return <<<'HTML'
            <x-ui-loader type="brugere" />
        HTML;
    }

    public function setRoleFilter(?string $role = null): void
    {
        $this->roleFilter = $role;
        $this->kreditor_id = null;
        $this->resetPage();
    }

    // -------------------------------------------------
    // Render
    // -------------------------------------------------
    public function render(): View
    {
        $users = User::query()
            ->select('users.id', 'users.name', 'users.email')
            ->with([
                'roles:id,name',
                'kreditorer:id,navn',
            ])
            ->when(
                filled(trim($this->search)),
                function ($query) {
                    $search = trim($this->search);

                    $query->where(function ($q) use ($search) {
                        $q->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%");
                    });
                }
            )
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->roleFilter);
                });
            })
            ->when($this->kreditor_id, function ($query) {
                $query->whereHas('kreditorer', function ($q) {
                    $q->whereKey($this->kreditor_id);
                });
            })
            ->orderBy('users.name')
            ->paginate($this->perPage);

        $kreditors = collect();

        if ($this->roleFilter === 'Kreditor' && $this->hasKreditorer) {
            $kreditors = Kreditorer::query()
                ->select('id', 'navn')
                ->when(
                    filled(trim($this->kreditorSearch)),
                    function ($query) {
                        $query->where(
                            'navn',
                            'like',
                            '%' . trim($this->kreditorSearch) . '%'
                        );
                    }
                )
                ->orderBy('navn')
                ->limit(30)
                ->get();
        }

        return view('livewire.users.manage-users', [
            'users' => $users,
            'kreditors' => $kreditors,
        ]);
    }
}