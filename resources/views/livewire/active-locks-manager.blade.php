<div wire:poll.5s>
    <!-- Overtagelses Modal -->
    @if($this->pendingRequests->isNotEmpty())
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-md p-4">
            <div class="w-full max-w-lg bg-white rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-xl font-bold shrink-0">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Anmodning om overtagelse</h3>
                        <p class="text-xs text-slate-500">Kollegaer ønsker at overtage sager, du har åbne.</p>
                    </div>
                </div>

                <!-- Liste over alle udestående anmodninger -->
                <div class="space-y-3 py-2 max-h-72 overflow-y-auto pr-1">
                    @foreach($this->pendingRequests as $req)
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3" wire:key="global-request-{{ $req->id }}">
                            <div>
                                <span class="text-xs font-bold text-slate-800">{{ $req->requester?->name ?? 'Kollega' }}</span>
                                <p class="text-[11px] text-slate-500">Vil overtage sagen:</p>
                                <!-- Viser den specifikke sag-titel / ID -->
                                <p class="text-xs font-semibold text-indigo-600 mt-0.5">
                                    {{ $req->sag?->titel ?? 'Sag #' . $req->sag_id }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 self-end sm:self-center">
                                <button 
                                    type="button" 
                                    wire:click="rejectTakeover({{ $req->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition cursor-pointer"
                                >
                                    Afvis
                                </button>
                                <button 
                                    type="button" 
                                    wire:click="acceptTakeover({{ $req->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition cursor-pointer"
                                >
                                    Accepter
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- JavaScript advarsel ved lukning af vindue / logud med aktive låse -->
    @if($hasActiveLocks)
        <script>window.hasActiveSagLocks = true;</script>
    @else
        <script>window.hasActiveSagLocks = false;</script>
    @endif

</div>