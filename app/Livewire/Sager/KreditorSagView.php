<?php

namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use App\Livewire\Forms\SagForm;
use App\Models\Dialog;

class KreditorSagView extends Component
{
    public ?Sager $sag = null;
    public SagForm $form;

    public $isEditMode = false;
    public $debitor;

    public $klientinformationUnread = 0;

    protected $listeners = ['klientinformationUpdated' => 'refreshBadge'];

    public function mount(Sager $sag)
{
    $this->sag = $sag->load([
        'sagerkreditor',
        'sagersagsbehandler',
        'sagerdebitor',
        'dokumenter'
    ]);

    $this->isEditMode = true;

    $this->refreshBadge();

    $this->debitor = $this->sag->sagerdebitor->first();
}

    public function refreshBadge()
    {
        $dialog = Dialog::where('sag_id', $this->sag->id)
            ->where('type', 'klientinformation')
            ->first();

        if (!$dialog) {
            $this->klientinformationUnread = 0;
            return;
        }

        $this->klientinformationUnread = $dialog->unreadForUser(auth()->user());
        $this->dispatch('klientinformationUpdated')->to(KreditorSagView::class);
    }

    public function getDokumenterCountProperty()
    {
        if (!$this->sag) return 0;

        return $this->sag->dokumenter()->count();
    }

    // Format numeric fields as Danish decimals
    public function formatNumber($value): string
    {
        if ($value === null || $value === '') return '0,00';

        $normalized = str_replace('.', '', $value);
        $normalized = str_replace(',', '.', $normalized);
        $number = (float) $normalized;

        return number_format($number, 2, ',', '.');
    }

    public function render()
    {
        return view('livewire.sager.kreditor-sag-view');
    }
}