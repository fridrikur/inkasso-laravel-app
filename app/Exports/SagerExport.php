<?php

namespace App\Exports;

use App\Models\Sager;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SagerExport implements FromView
{
    public function __construct(
        public array $filters,
        public string $sortField,
        public string $sortDirection,
        public array $columns = []
    ) {}

    public function view(): View
    {
        $sager = Sager::query()
            ->with([
                'sagerdebitor',
                'sagerkreditor',
                'sagerStatus',
                'sagerAfslutning',
                'sagersagsbehandler',
                'sagerkonsulent',
            ])
            ->filter($this->filters)
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();

        return view('exports.sager', [
            'sager' => $sager,
            'columns' => $this->columns,
        ]);
    }
}