<?php

namespace App\Livewire;

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
    public int $userHasSagerCount = 0; // 🟢 Tæller tilknyttede sager

    // -------------------------------------------------
    // Cached counts / roles
    // -------------------------------------------------
    /** @var Collection<int, Role> */
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
        $this->activeUserId = $userId;
        $this->showUserModal = true;
    }

    // 🟢 Alias så <x-table-actions> virker automatisk med samme metodenavn
    public function openEditModal(int $id): void
    {
        $this->openModal($id);
    }

    // -------------------------------------------------
    // SoftDelete Handlinger
    // -------------------------------------------------
    public function confirmDelete(int $id): void
    {
        if ($id === auth()->id()) {
            $this->dispatch('toast', message: 'Du kan ikke slette din egen konto.', type: 'error');
            return;
        }

        $user = User::find($id);

        if (!$user) return;

        $this->deleteUserId = $id;

        // 🟢 1. Find den tilsvarende Konsulent via brugerens e-mail
        $konsulent = \App\Models\Konsulenter::where('email', $user->email)->first();

        // 🟢 2. Tæl hvor mange sager konsulenten er knyttet til via pivot-relationen sagerkonsulent
        if ($konsulent) {
            // Vi tæller sager via pivot-tabellen (sager_konsulent)
            $this->userHasSagerCount = \App\Models\Sager::whereHas('sagerkonsulent', function ($q) use ($konsulent) {
                $q->where('konsulenter.id', $konsulent->id);
            })->count();
        } else {
            $this->userHasSagerCount = 0;
        }

        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteUserId = null;
    }

    public function confirmDeleteModal(): void
    {
        if (!$this->deleteUserId) return;

        $user = User::findOrFail($this->deleteUserId);
        $user->delete(); // SoftDelete (kræver 'use SoftDeletes' i User-modellen)

        $this->showDeleteModal = false;
        $this->deleteUserId = null;

        $this->refreshStats();
        $this->resetPage();

        $this->dispatch('notify', message: 'Brugeren er blevet deaktiveret.', type: 'success');
    }

    // -------------------------------------------------
    // Lifecycle
    // -------------------------------------------------
    public function mount(): void
    {
        $this->roles = Role::query()
            ->orderBy('name')
            ->get(['id', 'name']);

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

        if ($this->roleFilter === 'Kreditor') {
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