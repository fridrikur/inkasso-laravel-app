<div class="space-y-6">

    <div wire:init="loadUnhandledTable" class="bg-white rounded-xl shadow">

        @include('livewire.admin.dashboard.tables.unhandled')       
    </div>

    <div wire:init="loadIncomingTable" class="bg-white rounded-xl shadow">

        @include('livewire.admin.dashboard.tables.incoming')       
    </div>

    <div wire:init="loadUnreadTable" class="bg-white rounded-xl shadow">

        @include('livewire.admin.dashboard.tables.unread')

    </div>

    <div wire:init="loadEditingTable" class="bg-white rounded-xl shadow">

        @include('livewire.admin.dashboard.tables.editing')

    </div>

</div>