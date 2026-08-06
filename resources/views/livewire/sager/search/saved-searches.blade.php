<div class="space-y-4">

    {{-- ===================================================== --}}
    {{-- NEW SEARCH --}}
    {{-- ===================================================== --}}
    <div>

        <button
            wire:click="newSearch"
            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">

            + Ny søgning

        </button>

    </div>

    {{-- ===================================================== --}}
    {{-- SAVED SEARCHES --}}
    {{-- ===================================================== --}}
    <div class="bg-white rounded-xl border shadow-sm">

        <div class="flex items-center justify-between px-6 py-4 border-b">

            <div>

                <h2 class="font-semibold text-gray-900">

                    Gemte søgninger

                </h2>

                <p class="text-sm text-gray-500">

                    Genbrug tidligere søgninger.

                </p>

            </div>

        </div>

        <div class="p-6 space-y-4">

            @if($showSearchNameInput)

                <div class="flex items-center gap-3">

                    <input
                        type="text"
                        wire:model="searchName"
                        placeholder="Navn på søgning..."
                        class="flex-1 rounded-lg border">

                    @if($hasResults)

                        <button
                            wire:click="saveSearch"
                            class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white">

                            Gem

                        </button>

                    @endif

                </div>

            @endif

            @if($savedSearches->count())

                <div class="flex flex-wrap gap-2">

                    @foreach($savedSearches as $saved)

                        <div
                            class="flex items-center gap-2 rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2">

                            <button
                                wire:click="loadSearch({{ $saved->id }})"
                                class="text-sm font-medium text-blue-700">

                                {{ $saved->name }}

                            </button>

                            <button
                                wire:click="deleteSearch({{ $saved->id }})"
                                class="text-red-500 hover:text-red-700">

                                ✕

                            </button>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="text-sm text-gray-500">

                    Der er endnu ingen gemte søgninger.

                </div>

            @endif

        </div>

    </div>

</div>