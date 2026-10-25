<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SagLock;
use App\Models\SagEditRequest;

class ActiveLocksManager extends Component
{
    protected $listeners = ['refreshLocks' => '$refresh'];

    public function getPendingRequestsProperty()
    {
        // 1. Hent ID'er på de sager, som den nuværende bruger har låst (via SagLock)
        $myLockedSagIds = SagLock::where('user_id', auth()->id())->pluck('sag_id');

        // 2. Hent ubesvarede (pending) anmodninger på disse sager fra kolleger
        return SagEditRequest::whereIn('sag_id', $myLockedSagIds)
            ->where('status', 'pending')
            ->with(['requester', 'sag']) // Kræver relationer på SagEditRequest-modellen
            ->get();
    }

    public function acceptTakeover($requestId)
    {
        $request = SagEditRequest::with('sag')->find($requestId);
        
        if ($request && $request->isPending()) {
            // Tjek at den nuværende bruger har låsen på sagen
            $lock = SagLock::where('sag_id', $request->sag_id)
                ->where('user_id', auth()->id())
                ->first();

            if ($lock) {
                // 1. Overfør låsen til kollegaen
                $lock->update([
                    'user_id' => $request->requested_by,
                    'locked_at' => now(),
                ]);

                // 2. Opdater anmodningen til accepted
                $request->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                    // 'responded_by' => auth()->id(), // Tilføj denne hvis kolonnen findes i din migration
                ]);
                
                session()->flash('success', 'Sagen er overdraget til kollegaen.');
            }
        }
    }

    public function rejectTakeover($requestId)
    {
        $request = SagEditRequest::find($requestId);
        
        if ($request) {
            // Bekræft at brugeren ejer låsen
            $hasLock = SagLock::where('sag_id', $request->sag_id)
                ->where('user_id', auth()->id())
                ->exists();

            if ($hasLock) {
                $request->update(['status' => 'rejected']);
            }
        }
    }

    public function render()
    {
        // Tjek om brugeren overhovedet har nogle aktive låse (til JavaScript beforeunload-advarsel)
        $hasActiveLocks = SagLock::where('user_id', auth()->id())->exists();

        return view('livewire.active-locks-manager', [
            'hasActiveLocks' => $hasActiveLocks
        ]);
    }
}