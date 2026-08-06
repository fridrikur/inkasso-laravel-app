<div class="fixed bottom-6 right-6 w-[340px] z-50">

    <div class="bg-white border shadow-xl rounded-2xl p-4 space-y-3">

        <div class="flex items-start justify-between">

            <div>
                <p class="text-xs text-gray-500 uppercase">
                    Aktiv søgning
                </p>

                <p class="text-sm font-semibold text-gray-900">
                    {{ $autoSearchName ?: 'Ny søgning' }}
                </p>

                <div class="text-xs text-gray-500 mt-1 space-y-1">

                    <div>
                        Visning: {{ ucfirst($filters['visibility'] ?? 'all') }}
                    </div>

                    @if(!empty($this->searchSummary['statuses']))
                        <div>
                            Status: {{ implode(', ', $this->searchSummary['statuses']) }}
                        </div>
                    @endif

                    @if(!empty($this->searchSummary['kreditor']))
                        <div>
                            Kreditor: {{ $this->searchSummary['kreditor'] }}
                        </div>
                    @endif

                </div>
            </div>

        </div>

        <div class="flex gap-2">

            <button
                wire:click="openResults"
                class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg text-sm"
            >
                Vis sager ({{ $total }})
            </button>

            <button
                wire:click="saveSearch"
                class="px-3 py-2 border rounded-lg text-sm"
            >
                Gem
            </button>

        </div>

    </div>
</div>