{{-- ========================================= --}}
    {{-- MODAL: SLET BEKRÆFTELSE (Renset for dobbelt-render) --}}
    {{-- ========================================= --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            
            {{-- Ægte gennemsigtig, sløret baggrund (Viser kun tabellen live nedenunder) --}}
            <div 
                class="absolute inset-0 bg-slate-950/10 backdrop-blur-md transition-opacity duration-300"
                wire:click="$set('showDeleteModal', false)"
            ></div>

            {{-- Det opgraderede modalkort --}}
            <div class="relative z-10 transform overflow-hidden rounded-2xl bg-white p-6 text-left shadow-2xl transition-all w-full max-w-md border border-slate-200/50">
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-rose-50 text-rose-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-lg font-bold text-slate-900">Bekræft sletning</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Er du sikker på, at du vil slette denne sag? Den vil blive flyttet til papirkurven.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2.5">
                    <button
                        type="button"
                        wire:click="$set('showDeleteModal', false)"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition focus:outline-none"
                    >
                        Annuller
                    </button>
                    <button
                        type="button"
                        wire:click="deleteSag"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 transition focus:outline-none"
                    >
                        Ja, slet sag
                    </button>
                </div>
            </div>

        </div>
    @endif