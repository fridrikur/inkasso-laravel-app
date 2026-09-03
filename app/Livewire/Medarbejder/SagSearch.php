<?php

namespace App\Livewire\Medarbejder;

use Livewire\Component;
use App\Models\Sager;

class SagSearch extends Component
{
    public $search = '';

    public function getSearchResultsProperty()
    {
        if (empty(trim($this->search))) {
            return collect(); // Returner tom kollektion indtil der søges
        }

        $searchTerm = '%' . trim($this->search) . '%';

        return Sager::with(['sagerdebitor', 'sagerkreditor'])
            ->where(function($query) use ($searchTerm) {
                $query->where('sagsnr', 'like', $searchTerm)
                      ->orWhereHas('sagerdebitor', function($q) use ($searchTerm) {
                          $q->where('navn', 'like', $searchTerm);
                      })
                      ->orWhereHas('sagerkreditor', function($q) use ($searchTerm) {
                          $q->where('navn', 'like', $searchTerm);
                      });
            })
            ->take(50) // Begræns resultater for ydeevne
            ->get();
    }

    public function render()
    {
        return view('livewire.medarbejder.sag-search');
    }
}