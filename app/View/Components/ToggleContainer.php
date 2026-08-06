<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ToggleContainer extends Component
{
    public string $label;

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function render()
    {
        return view('components.toggle-container');
    }
}
