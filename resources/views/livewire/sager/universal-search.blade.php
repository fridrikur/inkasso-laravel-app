<div class="min-h-screen bg-slate-900/5 py-8">

    <div class="max-w-6xl mx-auto px-6 space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
            
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Avanceret sagsøgning
                </h1>

                <p class="text-xs text-slate-500 mt-1">
                    Dynamisk søgning i sager
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    wire:click="newSearch"
                    class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs tracking-wide transition shadow-lg shadow-blue-600/20 cursor-pointer"
                >
                    Ny søgning
                </button>

                <button
                    wire:click="resetFilters"
                    class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-xs transition cursor-pointer"
                >
                    Nulstil
                </button>
            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- SAVED SEARCHES (ONLY WHEN ACTIVE) --}}
        {{-- ========================================================= --}}
        @if($showSavedSearches)
            @include('livewire.sager.search.saved-searches')
        @endif

        {{-- ========================================================= --}}
        {{-- FILTERS (TAB SYSTEM WRAPPER) --}}
        {{-- ========================================================= --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">

            {{-- TAB HEADER --}}
            <div class="flex items-center gap-2 border-b border-slate-100 px-6 overflow-x-auto">

                @foreach([
                    'status' => 'Status',
                    'finance' => 'Økonomi',
                    'parties' => 'Parter',
                    'case' => 'Sag',
                    'dates' => 'Datoer',
                ] as $key => $label)

                    <button
                        wire:click="setTab('{{ $key }}')"
                        class="px-4 py-4 text-xs font-semibold border-b-2 transition whitespace-nowrap cursor-pointer
                            {{ $activeFilterTab === $key
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-slate-500 hover:text-slate-800' }}"
                    >
                        {{ $label }}
                    </button>

                @endforeach

            </div>

            {{-- TAB CONTENT --}}
            <div class="p-6 sm:p-8">

                @include('livewire.sager.search.filters')

            </div>

        </div>

        {{-- Vis kun det flydende panel hvis der er aktiv søgning OG resultaterne IKKE er åbnet endnu --}}
        @if($hasActiveSearch && ! $showResults)

        <div class="fixed bottom-6 right-6 z-50 w-[420px] animate-in fade-in slide-in-from-bottom-4 duration-300">

            <div class="bg-slate-900 text-white border border-slate-800 shadow-2xl rounded-3xl p-5 space-y-4 backdrop-blur-xl">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0 space-y-1">

                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            Aktiv søgning
                        </p>
                        @include('livewire.sager.search.search-summary')
                        <p class="text-sm font-semibold text-slate-200 truncate">
                            {{-- {{ $this->autoSearchName }} --}}
                        </p>

                    </div>

                    <div class="text-right bg-white/10 px-3.5 py-2 rounded-2xl border border-white/10">

                        <p class="text-2xl font-bold text-blue-400">
                            {{ $total }}
                        </p>

                        <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider">
                            sager
                        </p>

                    </div>

                </div>

                <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-800">

                    <button
                        wire:click="openResults"
                        class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-lg shadow-blue-600/30 cursor-pointer"
                    >
                        Vis sager
                    </button>

                    @if($selectedSavedSearch)

                        <span class="text-xs text-slate-400 font-medium italic">
                            Gemt søgning aktiv
                        </span>

                    @else

                        <div class="flex gap-2">

                            <input
                                wire:model.live="searchName"
                                placeholder="Gem navn..."
                                class="text-xs bg-slate-800/80 border border-slate-700/80 rounded-xl px-3 py-2 w-44 text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition"
                            />
                            

                            <button
                                wire:click="saveSearch"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-600/20 cursor-pointer"
                            >
                                Gem
                            </button>

                        </div>

                    @endif

                </div>

                <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Opdateres live mens du ændrer filtre
                </div>

            </div>

        </div>

        @endif

        {{-- ========================================================= --}}
        {{-- RESULTS MODAL --}}
        {{-- ========================================================= --}}
        @if($showResults)

        <div class="mt-8 animate-in fade-in duration-300">

            @include('livewire.sager.search.results')

        </div>

        @endif

        {{-- ========================================================= --}}
        {{-- EXPORT MODAL --}}
        {{-- ========================================================= --}}
        @if($showExportModal)
            @include('livewire.sager.search.export-modal')
        @endif

    </div>
</div>