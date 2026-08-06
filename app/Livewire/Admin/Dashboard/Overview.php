<?php 

class Overview extends Component
{
    public array $recordsByKreditor = [];

    public bool $loadUnhandled = false;
    public bool $loadIncoming = false;
    public bool $loadUnread = false;
    public bool $loadEditing = false;

    public function mount()
    {
        $this->recordsByKreditor = app(AdminDashboardService::class)
            ->getKreditorStats();
    }

    public function loadUnhandledTable() { $this->loadUnhandled = true; }
    public function loadIncomingTable() { $this->loadIncoming = true; }
    public function loadUnreadTable() { $this->loadUnread = true; }
    public function loadEditingTable() { $this->loadEditing = true; }

    public function render()
    {
        return view('livewire.admin.dashboard.overview');
    }
}