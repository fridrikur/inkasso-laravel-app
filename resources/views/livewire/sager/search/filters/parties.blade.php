<div class="space-y-6">

    <div>
        <h3 class="text-sm font-semibold text-gray-900">
            Parter
        </h3>

        <p class="text-xs text-gray-500">
            Find sager på debitor, kreditor eller konsulent
        </p>
    </div>

    <div class="grid md:grid-cols-3 gap-3">

        <input
            wire:model.live.debounce.300ms="filters.debitor"
            class="rounded-lg border-gray-300 text-sm"
            placeholder="Debitor"
        >

        <select
            wire:model.live="filters.kreditor_id"
            class="rounded-lg border-gray-300 text-sm"
        >
            <option value="">Alle kreditorer</option>
            @foreach($kreditorer as $kreditor)
                <option value="{{ $kreditor->id }}">
                    {{ $kreditor->navn }}
                </option>
            @endforeach
        </select>

        <select
            wire:model.live="filters.konsulent_id"
            class="rounded-lg border-gray-300 text-sm"
        >
            <option value="">Alle konsulenter</option>
            @foreach($konsulenter as $konsulent)
                <option value="{{ $konsulent->id }}">
                    {{ $konsulent->navn }}
                </option>
            @endforeach
        </select>

    </div>

</div>