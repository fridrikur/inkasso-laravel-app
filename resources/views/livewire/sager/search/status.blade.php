<div class="bg-white border rounded-xl p-4">

    <div class="flex items-center justify-between mb-3">

        <h3 class="text-xs font-semibold uppercase text-gray-500">
            Status
        </h3>

        <div class="flex gap-2 text-xs">

            <button
                wire:click="setTab('status')"
                class="{{ $activeFilterTab === 'status' ? 'text-blue-600 font-semibold' : 'text-gray-500' }}"
            >
                Status
            </button>

            <button
                wire:click="setTab('afslutning')"
                class="{{ $activeFilterTab === 'afslutning' ? 'text-blue-600 font-semibold' : 'text-gray-500' }}"
            >
                Afslutning
            </button>

        </div>

    </div>

    {{-- STATUS TAB --}}
    @if($activeFilterTab === 'status')
        <div class="flex flex-wrap gap-2">
            @foreach($statuses as $status)
                <label class="flex items-center gap-2 px-3 py-1 border rounded-full text-sm">
                    <input
                        type="checkbox"
                        wire:model.live="filters.status_ids"
                        value="{{ $status->id }}"
                    >
                    {{ $status->tekst }}
                </label>
            @endforeach
        </div>
    @endif

    {{-- AFSLUTNING TAB --}}
    @if($activeFilterTab === 'afslutning')
        <div class="flex flex-wrap gap-2">
            @foreach($afslutninger as $a)
                <label class="flex items-center gap-2 px-3 py-1 border rounded-full text-sm">
                    <input
                        type="checkbox"
                        wire:model.live="filters.afslutning_id"
                        value="{{ $a->id }}"
                    >
                    {{ $a->tekst }}
                </label>
            @endforeach
        </div>
    @endif

</div>