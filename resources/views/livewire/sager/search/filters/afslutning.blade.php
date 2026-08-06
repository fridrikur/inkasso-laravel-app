<div class="px-8 py-7 border-t">

    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-5">
        Afslutning
    </h3>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div>

            <label class="block text-sm font-medium mb-2">
                Afslutningsårsag
            </label>

            <select
                wire:model.live="filters.afslutning_id"
                class="w-full rounded-lg border-gray-300">

                <option value="">Alle</option>

                @foreach($afslutninger as $afslutning)
                    <option value="{{ $afslutning->id }}">
                        {{ $afslutning->afslutning }}
                    </option>
                @endforeach

            </select>

        </div>

    </div>

</div>