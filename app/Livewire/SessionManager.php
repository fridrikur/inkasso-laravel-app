<?php

namespace App\Livewire;

use Livewire\Component;

class SessionManager extends Component
{
    public $showWarning = false;
    public $countdown = 30;

    protected $listeners = ['userActive' => 'resetTimer'];

    public function mount()
    {
        $this->dispatch('startSessionTimer');
    }

    public function resetTimer()
    {
        session(['last_activity' => time()]);
        $this->showWarning = false;
        $this->countdown = 30;
    }

    public function tick()
    {
        $this->countdown--;

        if ($this->countdown <= 0) {
            return redirect()->route('login', ['timeout' => 1]);
        }
    }

    public function showWarning()
    {
        $this->showWarning = true;
    }

    public function extendSession()
    {
        session(['last_activity' => time()]);
        $this->showWarning = false;
        $this->countdown = 30;
    }

    public function render()
    {
        return view('livewire.session-manager');
    }
}