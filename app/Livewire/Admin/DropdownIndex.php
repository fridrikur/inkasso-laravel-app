<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use App\Models\Status;
use App\Models\KTR;
use App\Models\Afslutning;
use App\Models\Bemaerkning;
use App\Models\Udlaeg;
use App\Traits\HasCrudModal;

class DropdownIndex extends Component
{
    use HasCrudModal;

    public $activeTab = 'status';

    public $tekst = '';
    public $forkortelse = '';
    
    // Purge Cache modal (ved siden af HasCrudModal's showDeleteModal & showFormModal)
    public bool $showPurgeModal = false;

    protected $types = [
        'status'      => ['model' => Status::class, 'title' => 'Status', 'icon' => '🏷️'],
        'ktr'         => ['model' => KTR::class, 'title' => 'KTR Kode', 'icon' => '📌'],
        'afslutning'  => ['model' => Afslutning::class, 'title' => 'Afslutningstype', 'icon' => '🏁'],
        'bemaerkning' => ['model' => Bemaerkning::class, 'title' => 'Standard Bemærkning', 'icon' => '💬'],
        'udlaeg'      => ['model' => Udlaeg::class, 'title' => 'Udlægstype', 'icon' => '💰'],
    ];

    // 🟢 Påkrævet af HasCrudModal Trait
    public function resetForm(): void
    {
        $this->reset(['tekst', 'forkortelse']);
        $this->resetValidation();
    }

    // 🟢 Påkrævet af HasCrudModal Trait
    public function loadItemData($id): void
    {
        $model = $this->types[$this->activeTab]['model'];
        $item = $model::findOrFail($id);

        $this->tekst = $item->tekst;
        $this->forkortelse = $item->forkortelse ?? '';
    }

    public function setTab($tab)
    {
        if (array_key_exists($tab, $this->types)) {
            $this->activeTab = $tab;
            $this->closeFormModal();
        }
    }

    public function save()
    {
        $this->validate([
            'tekst' => 'required|string|max:255',
            'forkortelse' => 'nullable|string|max:20',
        ]);

        $model = $this->types[$this->activeTab]['model'];

        if ($this->editingId) {
            $item = $model::findOrFail($this->editingId);
            $item->update([
                'tekst' => $this->tekst,
                'forkortelse' => strtoupper($this->forkortelse),
            ]);
            $msg = 'Element opdateret!';
        } else {
            $model::create([
                'tekst' => $this->tekst,
                'forkortelse' => strtoupper($this->forkortelse),
            ]);
            $msg = 'Element oprettet!';
        }

        Cache::forget('select.' . $this->activeTab);
        $this->closeFormModal();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    // 🟢 Sletning udføres via Traitens $deletingId
    public function confirmDelete()
    {
        if ($this->deletingId) {
            $model = $this->types[$this->activeTab]['model'];
            $model::find($this->deletingId)?->delete();

            Cache::forget('select.' . $this->activeTab);

            $this->cancelDelete();
            $this->dispatch('toast', message: 'Element slettet!', type: 'success');
        }
    }

    public function confirmPurgeCache()
    {
        $this->showPurgeModal = true;
    }

    public function purgeCache()
    {
        Cache::forget('select.status');
        Cache::forget('select.ktr');
        Cache::forget('select.afslutning');
        Cache::forget('select.bemaerkning');
        Cache::forget('select.udlaeg');

        $this->showPurgeModal = false;

        $this->dispatch('toast', message: 'Alle dropdown-caches blev ryddet!', type: 'success');
    }

    public function render()
    {
        $currentConfig = $this->types[$this->activeTab];
        $model = $currentConfig['model'];

        return view('livewire.admin.dropdown-index', [
            'items' => $model::all(),
            'currentTitle' => $currentConfig['title'],
            'currentIcon' => $currentConfig['icon'],
        ]);
    }
}