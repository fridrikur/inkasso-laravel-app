<?php

namespace App\View\Components;

use App\Services\PerformanceMonitor;
use Illuminate\View\Component;
use Illuminate\View\View;

class PerformanceBadge extends Component
{
    public array $metrics;

    public function __construct(PerformanceMonitor $monitor)
    {
        $this->metrics = $monitor->getMetrics();
    }

    public function render(): View
    {
        return view('components.performance-badge');
    }
}