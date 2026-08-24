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

    // Filters
    public string $search = '';
    public ?string $roleFilter = null;
    public int $perPage = 10;
    public string $kreditorSearch = '';
    public ?int $kreditor_id = null;
    
    // Modals & State
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;
    public int $userHasSagerCount = 0;
    public bool $hasKreditorer = true;

    // Stats
    public int $totalUsers = 0;
    public int $adminCount = 0;
    public int $medarbejderCount = 0;
    public int $kreditorCount = 0;

    /** @var Collection<int, Role> */
    public Collection $roles;

    public function mount(): void
    {
        $this->roles = Role::query()->orderBy('name')->get(['id', 'name']);
        $this->hasKreditorer = Kreditorer::exists();
        $this->refreshStats();

        if (request()->filled('role')) {
            $this->roleFilter = request()->string('role')->toString();
        }

        if (request()->filled('edit')) {
            $this->openModal((int) request('edit'));
        }
    }

    // Modal Håndtering for Opret / Rediger
    public function openModal(?int $userId = null): void
    {
        if (! $userId && $this->roleFilter === 'Kreditor' && ! $this->hasKreditorer) {
            $this->dispatch('toast', [
                'message' => 'Der findes ingen kreditorer i databasen! Opret venligst en kreditor først.',
                'type'    => 'error'
            ]);
            return;
        }

        $this->editingId = $userId;
        $this->showFormModal = true;
    }

    public function closeModals(): void
    {
        $this->showFormModal = false;
        $this->showDeleteModal = false;
        $this->editingId = null;
        $this->deletingId = null;
        $this->userHasSagerCount = 0;
    }

    #[On('user-updated')]
    public function userUpdated(): void
    {
        $this->refreshStats();
        $this->closeModals();
    }

    // 🟢 Slette-håndtering (Uden konflikter)
    public function confirmDelete(int $id): void
    {
        if ($id === 1) {
            $this->dispatch('toast', [
                'message' => 'Systemets primære administrator (Bruger #1) kan ikke deaktiveres.',
                'type'    => 'error'
            ]);
            return;
        }

        if ($id === auth()->id()) {
            $this->dispatch('toast', [
                'message' => 'Du kan ikke deaktivere din egen konto.',
                'type'    => 'error'
            ]);
            return;
        }

        $user = User::find($id);
        if (! $user) return;

        if ($user->hasRole('Admin')) {
            $this->dispatch('toast', [
                'message' => 'Brugere med Admin-rollen kan ikke deaktiveres direkte. Skift først rollen til Medarbejder.',
                'type'    => 'error'
            ]);
            return;
        }

        $this->deletingId = $id;

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

    public function deleteUser(): void
    {
        if (! $this->deletingId) return;

        if ($this->deletingId === 1 || $this->deletingId === auth()->id()) {
            $this->closeModals();
            return;
        }

        $user = User::findOrFail($this->deletingId);

        if ($user->hasRole('Admin')) {
            $this->dispatch('toast', [
                'message' => 'En administrator kan ikke deaktiveres.',
                'type'    => 'error'
            ]);
            $this->closeModals();
            return;
        }

        $user->delete(); // SoftDelete

        $this->closeModals();
        $this->refreshStats();
        $this->resetPage();

        $this->dispatch('toast', [
            'message' => 'Brugeren er blevet deaktiveret.',
            'type'    => 'success'
        ]);
    }

    // Filter og øvrige metoder
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }
    public function updatedRoleFilter(): void { $this->resetPage(); $this->kreditor_id = null; }
    public function updatedKreditorSearch(): void { $this->resetPage(); }
    public function updatedKreditorId(): void { $this->resetPage(); }

    public function setRoleFilter(?string $role = null): void
    {
        $this->roleFilter = $role;
        $this->kreditor_id = null;
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

    public function render(): View
    {
        $users = User::query()
            ->select('users.id', 'users.name', 'users.email')
            ->with(['roles:id,name', 'kreditorer:id,navn'])
            ->when(filled(trim($this->search)), function ($query) {
                $search = trim($this->search);
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', "%{$search}%")
                      ->orWhere('users.email', 'like', "%{$search}%");
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('roles', fn($q) => $q->where('name', $this->roleFilter));
            })
            ->when($this->kreditor_id, function ($query) {
                $query->whereHas('kreditorer', fn($q) => $q->whereKey($this->kreditor_id));
            })
            ->orderBy('users.name')
            ->paginate($this->perPage);

        $kreditors = collect();
        if ($this->roleFilter === 'Kreditor' && $this->hasKreditorer) {
            $kreditors = Kreditorer::query()
                ->select('id', 'navn')
                ->when(filled(trim($this->kreditorSearch)), function ($query) {
                    $query->where('navn', 'like', '%' . trim($this->kreditorSearch) . '%');
                })
                ->orderBy('navn')
                ->limit(30)
                ->get();
        }

        return view('livewire.users.manage-users', [
            'users' => $users,
            'kreditors' => $kreditors,
        ]);
    }

    public function closeModal(): void
    {
        $this->closeModals();
    }

    public function closeFormModal(): void
    {
        $this->closeModals();
    }
}