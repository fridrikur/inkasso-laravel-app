<?php

class System extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard.system', [
            'gdprExpired' => app(SagerGdprService::class)->expiredQuery()->count(),
            'gdprExpiring' => app(SagerGdprService::class)->expiringQuery()->count(),
        ]);
    }
}