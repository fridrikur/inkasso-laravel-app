<div class="space-y-6">
    <x-search-breadcrumbs />
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('scroll-to-results', () => {
                const anchor = document.getElementById('results-anchor');

                if (anchor) {
                    anchor.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <h2 class="text-lg font-semibold">Søgning</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Sagsnr *</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="filters.sagsnr"
                            class="w-full rounded border-gray-300"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Kreditor *</label>
                        <select
                            wire:model.live="filters.kreditor_id"
                            class="w-full rounded border-gray-300"
                        >
                            <option value="">Vælg kreditor</option>

                            @foreach($kreditorer as $kreditor)
                                <option value="{{ $kreditor->id }}">{{ $kreditor->navn }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Debitor</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="filters.debitor_navn"
                            class="w-full rounded border-gray-300"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select
                            wire:model.live="filters.status_id"
                            class="w-full rounded border-gray-300"
                        >
                            <option value="">Alle</option>

                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->tekst }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Postnr</label>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="filters.postnr"
                            class="w-full rounded border-gray-300"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-lg border bg-slate-50 px-4 py-3">
                    <div>
                        <div class="text-sm font-medium text-slate-700">Preview</div>

                        <div class="text-sm text-slate-500">
                            @if(! $hasPreviewed)
                                Udfyld Sagsnr og Kreditor
                            @elseif($previewCount === 0)
                                Ingen resultater fundet
                            @else
                                {{ $previewCount }} resultat(er) fundet
                            @endif
                        </div>
                    </div>

                    @if($previewCount > 0)
                        <div class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-700">
                            Klar til at gemme
                        </div>
                    @endif
                </div>

                <div class="border-t pt-4 space-y-3">
                    <label class="block text-sm font-medium">Gem søgning som</label>

                    <div class="flex gap-2">
                        <input
                            type="text"
                            wire:model="searchName"
                            placeholder="Fx: Aktive Volvo sager"
                            class="flex-1 rounded border-gray-300"
                        >

                        <button
                            wire:click="saveSearch"
                            type="button"
                            {{ $previewCount < 1 ? 'disabled' : '' }}
                            class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Gem ({{ $previewCount }})
                        </button>
                    </div>
                </div>
            </div>

            <div id="results-anchor" class="space-y-4">
                <div id="results-anchor" class="space-y-4">

    @if($sqlPreview)
        <div class="flex items-center justify-between">
            <div class="text-sm font-medium text-slate-700">
                SQL Preview (udvikler)
            </div>

            <button
                wire:click="toggleSqlPreview"
                type="button"
                class="text-sm text-blue-600 hover:underline"
            >
                {{ $showSqlPreview ? 'Skjul SQL' : 'Vis SQL' }}
            </button>
                </div>

                @if($showSqlPreview)
                    <div class="bg-slate-950 rounded-xl border border-slate-800 overflow-hidden shadow">
                        <div class="px-4 py-3 border-b border-slate-800">
                            <div class="text-sm font-semibold text-white">SQL Query Preview</div>
                            <div class="text-xs text-slate-400">Kun til udviklere – ikke redigerbar</div>
                        </div>

                        <div class="p-4">
                            <textarea
                                readonly
                                rows="8"
                                class="w-full resize-none rounded-lg border border-slate-700 bg-slate-900 p-3 font-mono text-xs text-green-300 focus:ring-0"
                            >{{ $sqlPreview }}</textarea>
                        </div>
                    </div>
                @endif
            @endif

        </div>

            </div></div>

        <div>
            <div class="bg-white rounded-xl shadow p-6 sticky top-6">
                <h2 class="text-lg font-semibold mb-4">Gemte søgninger</h2>

                <div class="space-y-2 max-h-[70vh] overflow-y-auto">
                    @foreach($savedSearches as $saved)
                        <div class="border rounded-lg p-3 hover:bg-slate-50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <button
                                    wire:click="loadSearch({{ $saved->id }})"
                                    class="flex-1 text-left"
                                >
                                    <div class="font-medium text-slate-800">
                                        {{ $saved->name }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $saved->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </button>

                                <div class="flex flex-col items-end gap-2">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $saved->result_count > 250 ? 'bg-red-100 text-red-700' : ($saved->result_count > 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                        {{ $saved->result_count }}
                                    </span>

                                    <button
                                        wire:click="deleteSearch({{ $saved->id }})"
                                        class="text-xs text-red-600 hover:text-red-800"
                                    >
                                        Slet
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div id="results" class="w-full bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                        Sagsnr
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                        Kreditor
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                        Debitor
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                        Postnr
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($this->results as $sag)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            {{ $sag->sagsnr }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $sag->sagerkreditor->pluck('navn')->join(', ') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $sag->sagerdebitor->pluck('navn')->join(', ') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $sag->sagerStatus->pluck('tekst')->join(', ') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $sag->sagerdebitor->pluck('postnr')->join(', ') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            Ingen resultater fundet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($previewCount > 5)
        <div class="border-t border-slate-200 px-4 py-4 flex items-center justify-between">
            <div class="text-sm text-slate-500">
                Viser de første 5 af {{ $previewCount }} resultater
            </div>
            @if($selectedSavedSearch)
                <a
                    href="{{ route('saved-search.results', $selectedSavedSearch) }}"
                    class="inline-flex items-center rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
                >
                    Vis alle
                </a>
            @endif
        </div>
    @endif
</div>
</div>
```
