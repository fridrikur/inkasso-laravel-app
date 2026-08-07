<div>
@if($showFormModal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100">

        <button
            type="button"
            wire:click="closeFormModal"
            class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition"
        >
            &times;
        </button>

        {{-- 🟢 BRUGER $editingId FRA HASCRUDMODAL --}}
        <h2 class="text-lg font-bold text-slate-900 mb-4">
            {{ $editingId ? 'Rediger kreditor' : 'Opret kreditor' }}
        </h2>

        <form wire:submit.prevent="save" class="space-y-4">
            
            {{-- NAVN --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Navn</label>
                <input
                    type="text"
                    wire:model="navn"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                >
                @error('navn')
                    <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- LOTUS ID --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Lotus ID</label>
                <input
                    type="number"
                    wire:model.live="lotusID"
                    placeholder="f.eks. {{ $this->suggestedLotusId }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                >

                @if(!empty($lotusID))
                    @if(in_array($lotusID, $usedLotusIds))
                        <div class="text-rose-600 text-xs font-semibold mt-1">
                            ⚠ LotusID findes allerede i systemet
                        </div>
                    @else
                        <div class="text-emerald-600 text-xs font-semibold mt-1">
                            ✓ LotusID er ledigt
                        </div>
                    @endif
                @endif

                <div class="text-indigo-600 text-xs mt-2">
                    Forslag: <strong>{{ $this->suggestedLotusId }}</strong>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button
                    type="button"
                    wire:click="closeFormModal"
                    class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer"
                >
                    Annuller
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition cursor-pointer"
                >
                    {{ $editingId ? 'Gem ændringer' : 'Opret kreditor' }}
                </button>
            </div>

        </form>

    </div>
</div>
@endif
</div>