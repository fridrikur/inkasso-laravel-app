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

        return Sager::with(['debitor', 'kreditor'])
            ->where(function($query) use ($searchTerm) {
                $query->where('sagsnr', 'like', $searchTerm)
                      ->orWhereHas('debitor', function($q) use ($searchTerm) {
                          $q->where('navn', 'like', $searchTerm);
                      })
                      ->orWhereHas('kreditor', function($q) use ($searchTerm) {
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