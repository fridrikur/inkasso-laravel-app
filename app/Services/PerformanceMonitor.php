<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PerformanceMonitor
{
    protected float $startTime;
    protected int $queryCount = 0;
    protected float $queryTime = 0;

    public function start(): void
    {
        $this->startTime = microtime(true);

        DB::listen(function ($query) {
            $this->queryCount++;
            $this->queryTime += $query->time; // time kommer i millisekunder fra Laravel DB listener
        });
    }

    public function getMetrics(): array
    {
        $totalTime = round((microtime(true) - $this->startTime) * 1000, 2);

        return [
            'total_time' => $totalTime,
            'query_count' => $this->queryCount,
            'query_time' => round($this->queryTime, 2),
        ];
    }
}