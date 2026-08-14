<?php

return [
    App\Providers\AppServiceProvider::class,
    // App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    Livewire\LivewireServiceProvider::class, // <-- Skal være denne præcise klasse
];