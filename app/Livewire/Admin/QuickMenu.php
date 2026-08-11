<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Kreditorer;

class QuickMenu extends Component
{
    public bool $showQuickMenu = false;
    public string $quickMenuScreen = 'main';

    #[On('open-quick-menu')]
    public function openMenu()
    {
        $this->showQuickMenu = true;
        $this->quickMenuScreen = 'main';
    }

    public function closeQuickMenu()
    {
        $this->showQuickMenu = false;
    }

    public function openImportSagerMenu()
    {
        $this->quickMenuScreen = 'import-sager';
    }

    /**
     * 🟢 Opdateret skånsom videreførelse til Import Form
     */
    public function goToImportSager($identifier)
    {
        // 1. Forsøg først at slå op på id, dernæst lotusID
        $kreditor = Kreditorer::where('id', $identifier)
            ->orWhere('lotusID', $identifier)
            ->first();

        // 2. Hvis kreditoren ikke findes i databasen, vis en pæn besked frem for at crashe
        if (! $kreditor) {
            session()->flash('error', 'Kreditoren kunne ikke findes i databasen.');
            return;
        }

        $this->closeQuickMenu();

        // 3. Sender selve Kreditorer-modellen videre til ruten
        return redirect()->route('sager.import.form', [
            'kreditor' => $kreditor->id
        ]);
    }

    public function goToCreateKreditor()
    {
        return redirect()->route('kreditorer.create');
    }

    public function goToCreateBrev()
    {
        return redirect()->route('sager.breve.opret');
    }

    public function goToFindSag()
    {
        return redirect()->route('sager.search');
    }

    public function goToGdprScan()
    {
        return redirect()->route('gdpr.sager.retention');
    }

    public function goToCreateUser()
    {
        return redirect()->route('users.create');
    }

    public function goToCreateKonsulent()
    {
        return redirect()->route('konsulenter.create');
    }

    public function goToSystemSettings()
    {
        return redirect()->route('admin.system-settings.index');
    }

    public function render()
    {
        return view('livewire.admin.quick-menu', [
            'kreditors' => $this->showQuickMenu 
                ? Kreditorer::withCount('sager')->get() 
                : collect(),
        ]);
    }
}