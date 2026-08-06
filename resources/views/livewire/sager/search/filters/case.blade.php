<div class="px-8 py-7 border-t">

    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-5">
        Sagsoplysninger
    </h3>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div>

            <label class="block text-sm font-medium mb-2">
                Global søgning
            </label>

            <input
                wire:model.live.debounce.500ms="filters.search"
                type="text"
                class="w-full rounded-lg border-gray-300"
                placeholder="Søg i hele sagen">

        </div>

        <div>

            <label class="block text-sm font-medium mb-2">
                Sagsnummer
            </label>

            <input
                wire:model.live.debounce.500ms="filters.sagsnr"
                type="text"
                class="w-full rounded-lg border-gray-300">

        </div>

        <div>

            <label class="block text-sm font-medium mb-2">
                Stelnummer
            </label>

            <input
                wire:model.live.debounce.500ms="filters.stelnr"
                type="text"
                class="w-full rounded-lg border-gray-300">

        </div>

    </div>

</div>