{{-- YDERSTE WRAPPER MED DYNAMISK SIDEBAGGRUND --}}
<div 
    style="background-color: var(--theme-sag-editor-wrapper-bg);"
    class="rounded-3xl p-6 sm:p-8 space-y-6 transition-colors duration-200 border border-slate-200/60 shadow-xs"
>

    {{-- 1. SAG EDITOR HEADER (MED DYNAMISK HEADER-FARVE) --}}
    <div 
        style="background-color: var(--theme-sag-editor-header);"
        class="rounded-2xl p-6 text-white shadow-sm transition-colors duration-200 flex items-center justify-between"
    >
        <div>
            <span class="text-xs font-bold uppercase tracking-wider opacity-80">Sagsbehandling</span>
            <h1 class="text-xl font-bold tracking-tight mt-0.5">
                {{ $sag->exists ? 'Redigér Sag #' . $sag->sagsnr : 'Opret Ny Sag' }}
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-xl bg-white/10 backdrop-blur-md text-xs font-bold">
                Status: {{ $sag->status?->navn ?? 'Kladde' }}
            </span>
        </div>
    </div>

    {{-- VISES NÅR DATOEN 'AFSLUTTET' ER UDFYLDT (ENTEN I FORM ELLER DATABASE) --}}
    @if(!empty($form->afsluttet) || ($sag->exists && $sag->afsluttet))
        <div class="mb-4 p-4 rounded-2xl border bg-slate-100/90 border-slate-200 text-slate-700 text-xs font-medium flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-base shrink-0">
                    🏁
                </div>
                <div>
                    <span class="font-bold text-slate-900 block sm:inline">Denne sag er markeret som afsluttet</span>
                    <span class="text-slate-500">
                        pr. <strong class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($form->afsluttet ?? $sag->afsluttet)->format('d-m-Y') }}</strong>
                    </span>
                    
                    {{-- VISER ÅRSAG HVIS DEN ER VALGT --}}
                    @if(!empty($form->afslutning) && isset($selectOptions['afslutning'][$form->afslutning]))
                        <span class="hidden md:inline text-slate-400"> • </span>
                        <span class="block md:inline text-slate-600 mt-0.5 md:mt-0">
                            Årsag: <span class="italic font-semibold text-slate-800">{{ $selectOptions['afslutning'][$form->afslutning] }}</span>
                        </span>
                    @endif
                </div>
            </div>

            <span class="inline-flex items-center justify-center self-start sm:self-center text-[10px] bg-slate-200/80 text-slate-700 px-3 py-1 rounded-lg font-bold uppercase tracking-wider border border-slate-300/60 shrink-0">
                Afsluttet sag
            </span>
        </div>
    @endif

    {{-- 2. HOVED-FORMULAR MED DYNAMISK KORT-BAGGRUND --}}
    <div 
        style="background-color: var(--theme-sag-editor-bg);"
        class="rounded-2xl border border-slate-200/80 p-6 shadow-sm transition-colors duration-200"
    >
        {{-- TAB STAMDATA INDHOLD --}}
        <form wire:submit.prevent="save">
            
            @include('livewire.sager.partials.form-fields')

            <div class="flex justify-end mt-6 pt-4 border-t border-slate-100">
                <button 
                    type="submit"
                    style="background-color: var(--theme-primary);"
                    class="px-6 py-2.5 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer hover:opacity-90"
                >
                    Gem Sag
                </button>
            </div>
        </form>
    </div>

    {{-- MODAL: PÅMINDELSE OM AFSLUTNINGSDATO ELLER -ÅRSAG --}}
    @if($showAfsluttetDateReminder)
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
    @endif

</div>