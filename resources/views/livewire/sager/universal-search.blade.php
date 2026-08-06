<div class="min-h-screen bg-gray-50">

    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}
        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Avanceret sagsøgning
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Dynamisk søgning i sager
                </p>
            </div>

            <div class="flex gap-2">

                <button
                    wire:click="newSearch"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white"
                >
                    Ny søgning
                </button>

                <button
                    wire:click="resetFilters"
                    class="px-4 py-2 rounded-lg bg-white border"
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
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

            {{-- TAB HEADER --}}
            <div class="flex items-center gap-2 border-b px-4">

                @foreach([
                    'status' => 'Status',
                    'finance' => 'Økonomi',
                    'parties' => 'Parter',
                    'case' => 'Sag',
                    'dates' => 'Datoer',
                ] as $key => $label)

                    <button
                        wire:click="setTab('{{ $key }}')"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition
                            {{ $activeFilterTab === $key
                                ? 'border-blue-600 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700' }}"
                    >
                        {{ $label }}
                    </button>

                @endforeach

            </div>

            {{-- TAB CONTENT --}}
            <div class="p-6">

                @include('livewire.sager.search.filters')

            </div>

        </div>

        @if($hasActiveSearch)

        <div class="fixed bottom-6 right-6 z-50 w-[420px]">

            <div class="bg-white border shadow-2xl rounded-2xl p-4 space-y-3">

                <div class="flex items-start justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs text-gray-500 uppercase">
                            Aktiv søgning
                        </p>
                        @include('livewire.sager.search.search-summary')
                        <p class="text-sm font-semibold text-gray-900 truncate">
                            {{-- {{ $this->autoSearchName }} --}}
                        </p>

                    </div>

                    <div class="text-right">

                        <p class="text-2xl font-bold text-blue-600">
                            {{ $total }}
                        </p>

                        <p class="text-xs text-gray-500">
                            sager
                        </p>

                    </div>

                </div>

                <div class="flex items-center justify-between gap-2 pt-2 border-t">

                    <button
                        wire:click="openResults"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold"
                    >
                        Vis sager
                    </button>

                    @if($selectedSavedSearch)

                        <span class="text-xs text-gray-500">
                            Gemt søgning aktiv
                        </span>

                    @else

                        <div class="flex gap-2">

                            <input
                                wire:model.live="searchName"
                                placeholder="Gem navn"
                                class="text-sm border rounded-lg px-3 py-2 w-72"
                            />
                            

                            <button
                                wire:click="saveSearch"
                                class="px-3 py-2 bg-green-600 text-white rounded-lg text-sm"
                            >
                                Gem
                            </button>

                        </div>

                    @endif

                </div>

                <div class="text-xs text-gray-500">
                    Opdateres live mens du ændrer filtre
                </div>

            </div>

        </div>

        @endif

        {{-- ========================================================= --}}
        {{-- RESULTS MODAL --}}
        {{-- ========================================================= --}}
        @if($showResults)

        <div class="mt-8">

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