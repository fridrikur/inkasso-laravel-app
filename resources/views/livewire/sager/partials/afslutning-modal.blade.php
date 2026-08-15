<div 
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4"
    x-data
    @keydown.escape.window="$wire.set('showAfsluttetDateReminder', false)"
>
    <div class="bg-white rounded-3xl shadow-2xl p-6 w-full max-w-md border border-slate-100 space-y-4">
        
        @if($reminderType === 'date')
            {{-- SCENARIE 1: MANGLER DATO --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                    📅
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Mangler afslutningsdato</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Du har valgt en afslutningsårsag, men feltet <strong>Afsluttet (dato)</strong> er tomt.
                    </p>
                </div>
            </div>

            <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                Vil du udfylde feltet <strong>Afsluttet</strong> med dags dato ({{ now()->format('d-m-Y') }})?
            </p>

            <div class="flex justify-end gap-2 pt-2">
                <button 
                    type="button" 
                    wire:click="$set('showAfsluttetDateReminder', false)"
                    class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition cursor-pointer"
                >
                    Spring over (Esc)
                </button>

                <button 
                    type="button" 
                    wire:click="applyTodayAfsluttetDate"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                >
                    Udfyld dags dato
                </button>
            </div>

        @else
            {{-- SCENARIE 2: MANGLER ÅRSAG --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
                    🏷️
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Mangler afslutningsårsag</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Du har angivet en afslutningsdato, men mangler at vælge en årsag i dropdown-menuen <strong>Afslutning</strong>.
                    </p>
                </div>
            </div>

            <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                Husk at vælge en afslutningsårsag i feltet <strong>Afslutning</strong> før du gemmer sagen.
            </p>

            <div class="flex justify-end gap-2 pt-2">
                <button 
                    type="button" 
                    wire:click="$set('showAfsluttetDateReminder', false)"
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                >
                    Forstået (Esc)
                </button>
            </div>
        @endif
    </div>
</div>