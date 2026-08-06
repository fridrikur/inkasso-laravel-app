<?php
namespace App\Livewire\Sager;

use Livewire\Component;
use App\Models\Sager;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class KreditorSagerIndex extends Component
{
    use WithPagination;
    public $search = '';
    public $debitor;
    
    public function render()
    {
        $user = auth()->user();
        $kreditor = $user->kreditorer()->firstOrFail();

        $search = trim($this->search ?? '');

        $query = Sager::query()

            // Only show sager belonging to this kreditor
            ->whereHas('sagerkreditor', function ($q) use ($kreditor) {
                $q->where('kreditor_id', $kreditor->id);
            })

            // Search
            ->when($search !== '', function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    // Search directly on sag fields
                    $q->where('sagsnr', 'like', '%' . $search . '%')
                    ->orWhere('hovedstol', 'like', '%' . $search . '%')

                    // Search on debitor fields
                    ->orWhereHas('sagerdebitor', function ($q2) use ($search) {

                        $q2->where('navn', 'like', '%' . $search . '%')
                            ->orWhere('adresse', 'like', '%' . $search . '%')

                            // Prioritize town/city search
                            ->orWhereHas('postnummer', function ($q3) use ($search) {
                                $q3->where('by', 'like', '%' . $search . '%');
                            })

                            // Still allow searching by postcode
                            ->orWhere('postnr', 'like', '%' . $search . '%');
                    });
                });
            });

        $sager = $query
            ->with([
                'sagerdebitor',
                'sagerdebitor.postnummer',
            ])
            ->latest()
            ->paginate(15);

        // Fuzzy suggestion if no results
        $suggestion = null;

        if ($search !== '' && $sager->isEmpty()) {

            $names = Sager::whereHas('sagerkreditor', function ($q) use ($kreditor) {
                    $q->where('kreditor_id', $kreditor->id);
                })
                ->with('sagerdebitor.postnummer')
                ->limit(100)
                ->get()
                ->flatMap(function ($sag) {
                    return $sag->sagerdebitor->flatMap(function ($debitor) {
                        return array_filter([
                            $debitor->navn,
                            $debitor->adresse,
                            $debitor->postnr,
                            optional($debitor->postnummer)->by,
                        ]);
                    });
                })
                ->unique()
                ->values();

            $closest = null;
            $shortest = -1;

            foreach ($names as $name) {

                $distance = levenshtein(
                    mb_strtolower($search),
                    mb_strtolower($name)
                );

                if ($distance === 0) {
                    $closest = $name;
                    break;
                }

                if ($shortest < 0 || $distance < $shortest) {
                    $closest = $name;
                    $shortest = $distance;
                }
            }

            if ($closest !== null && $shortest <= 5) {
                $suggestion = $closest;
            }
        }

        return view('livewire.sager.kreditor-sager-index', [
            'sager' => $sager,
            'suggestion' => $suggestion,
        ]);
    }
    
    public function renderOLD()
    {
        $kreditor = auth()->user()->kreditorer()->first();

        $sager = Sager::whereHas('sagerkreditor', function ($q) use ($kreditor) {
            $q->where('kreditor_id', $kreditor->id);
        })
        ->where(function ($q) {
            $q->where('sagsnr', 'like', "%{$this->search}%")
              ->orWhere('debitor_navn', 'like', "%{$this->search}%");
        })
        ->latest()
        ->paginate(15);

        return view('livewire.sager.kreditor-sag-index', compact('sager'));
    }
}