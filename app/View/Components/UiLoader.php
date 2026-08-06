<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UiLoader extends Component
{
    public string $title;
    public string $subtitle;
    public string $icon;
    public string $variant; // 'spinner' eller 'progress'
    public ?int $count;

    public function __construct(
        public string $type = 'generic',
        ?string $title = null,
        ?string $subtitle = null,
        ?string $variant = null,
        ?int $count = null
    ) {
        $this->count = $count;

        $formattedCount = $this->count !== null 
            ? number_format($this->count, 0, ',', '.') 
            : null;

        $config = match ($this->type) {
            'sager' => [
                'title' => 'Indlæser sager',
                'subtitle' => $formattedCount 
                    ? "Henter og opdaterer {$formattedCount} sager, parter og status..." 
                    : 'Henter og opdaterer sagsdata, parter og status...',
                'icon' => 'folder',
                'variant' => 'progress',
            ],
            'kreditorer' => [
                'title' => 'Indlæser kreditorer',
                'subtitle' => $formattedCount 
                    ? "Henter {$formattedCount} kreditorer og sagsantal..." 
                    : 'Henter kreditoroversigt og sagsantal...',
                'icon' => 'building',
                'variant' => 'spinner',
            ],
            'brugere' => [
                'title' => 'Indlæser brugere',
                'subtitle' => $formattedCount 
                    ? "Henter {$formattedCount} brugere og rettigheder..." 
                    : 'Henter brugerliste og rettigheder...',
                'icon' => 'users',
                'variant' => 'spinner',
            ],
            'konsulenter' => [
                'title' => 'Indlæser konsulenter',
                'subtitle' => $formattedCount 
                    ? "Henter {$formattedCount} konsulenter og arbejdsbelastning..." 
                    : 'Henter konsulentdata og arbejdsbelastning...',
                'icon' => 'user-check',
                'variant' => 'spinner',
            ],
            default => [
                'title' => $title ?? 'Indlæser data',
                'subtitle' => $subtitle ?? ($formattedCount ? "Henter {$formattedCount} poster..." : 'Henter de seneste oplysninger...'),
                'icon' => 'default',
                'variant' => 'spinner',
            ],
        };

        $this->title = $config['title'];
        $this->subtitle = $config['subtitle'];
        $this->icon = $config['icon'];
        $this->variant = $variant ?? $config['variant'];
    }

    public function render(): View
    {
        return view('components.ui-loader');
    }
}