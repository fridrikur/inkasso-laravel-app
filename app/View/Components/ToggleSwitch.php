<?php 
namespace App\View\Components;

use Illuminate\View\Component;

class ToggleSwitch extends Component
{
    public string $name;
    public bool $checked;

    public function __construct(string $name, bool $checked = false)
    {
        $this->name = $name;
        $this->checked = $checked;
    }

    public function render()
    {
        return view('components.toggle-switch');
    }
}
