@props([
    'show' => false,
    'title' => 'Slet emne?',
    'message' => 'Denne handling kan ikke fortrydes.'
])

@if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Mørk sløret baggrund --}}
        <div wire:click="cancelDelete" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            {{-- Modalkasse baseret på konsulent-designet --}}
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100">

                <h2 class="text-lg font-bold text-slate-900" id="modal-title">
                    {{ $title }}
                </h2>

                <p class="mt-2 text-sm text-slate-600">
                    {{ $message }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button 
                        type="button" 
                        wire:click="cancelDelete" 
                        class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                    >
                        Annuller
                    </button>

                    <button 
                        type="button" 
                        wire:click="confirmDelete" 
                        class="rounded-lg bg-red-600 hover:bg-red-700 px-4 py-2 text-xs font-semibold text-white transition shadow-sm cursor-pointer"
                    >
                        Slet
                    </button>
                </div>

            </div>
        </div>
    </div>
@endif