{{-- YDERSTE WRAPPER MED DYNAMISK SIDEBAGGRUND --}}
<div 
    style="background-color: var(--theme-sag-editor-wrapper-bg);"
    class="relative rounded-3xl p-6 sm:p-8 space-y-6 transition-colors duration-200 border border-slate-200/60 shadow-xs"
>
    {{-- SPINNER OVERLAY --}}
    <div 
        wire:loading.flex
        wire:target="save, setTab, formatOnBlur, form"
        class="absolute inset-0 bg-white/75 backdrop-blur-sm z-50 items-center justify-center rounded-3xl transition-all"
        style="display: none;"
    >
        <div class="bg-white px-5 py-3 rounded-2xl shadow-2xl border border-slate-100 flex items-center gap-3 text-slate-800 text-xs font-bold">
            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Indlæser sagen...</span>
        </div>
    </div>

    {{-- 1. SAG EDITOR HEADER --}}
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

    {{-- AFSLUTTET BANNER --}}
    @if(!empty($form->afsluttet) || ($sag->exists && $sag->afsluttet))
        @include('livewire.sager.partials.afsluttet-banner')
    @endif

    {{-- 1.5 TAB NAVIGATION (BRUGER SAGTABS) --}}
    @livewire('sager.sag-tabs', ['sag' => $sag, 'activeTab' => $activeTab], key($sag?->id ?? 'new'))

    {{-- 2. HOVED-INDHOLD (DYNAMISK BASERET PÅ TAB) --}}
    <div 
        style="background-color: var(--theme-sag-editor-bg);"
        class="rounded-2xl border border-slate-200/80 p-6 shadow-sm transition-colors duration-200"
    >
        @if($activeTab === 'stamdata')
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

        @elseif($activeTab === 'breve')
            {{-- 🟢 Her kaldes din rigtige brev-komponent med den aktuelle sag --}}
            @livewire('sager.merge-brev', ['sag' => $sag], key('merge-brev-'.$sag->id))

        @elseif($activeTab === 'klientinformation')
            @livewire('sager.klientinformation', ['sag' => $sag], key('klientinfo-'.$sag->id))

        @elseif($activeTab === 'historik')
            @livewire('sager.historik', ['sag' => $sag], key('historik-'.$sag->id))

        @elseif($activeTab === 'bogholderi')
            @livewire('sager.bogholderi', ['sag' => $sag], key('bogholderi-'.$sag->id))
        @endif
    </div>

    {{-- MODAL: PÅMINDELSE OM AFSLUTNINGSDATO ELLER -ÅRSAG --}}
    @if($showAfsluttetDateReminder)
        @include('livewire.sager.partials.afslutning-modal')
    @endif

</div>