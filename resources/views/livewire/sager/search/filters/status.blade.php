<div class="space-y-4">

    <div class="flex items-center justify-between">

        <div>
            <h3 class="text-sm font-semibold text-gray-900">
                Status
            </h3>
        </div>

        <select
            wire:model.live="filters.visibility"
            class="text-sm rounded-lg border-gray-300"
        >
            <option value="all">Alle</option>
            <option value="open">Åbne</option>
            <option value="closed">Lukkede</option>
        </select>

    </div>

    <div class="flex flex-wrap gap-2">

        @foreach($statuses as $status)

            <label class="flex items-center gap-2 px-3 py-2 rounded-lg border bg-gray-50">

                <input type="checkbox"
                       wire:model.live="filters.status_ids"
                       value="{{ $status->id }}">

                <span class="text-sm">{{ $status->tekst }}</span>

            </label>

        @endforeach

    </div>

</div>