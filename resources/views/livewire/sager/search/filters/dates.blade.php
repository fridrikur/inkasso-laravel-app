<div class="px-8 py-7 border-t">

    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 mb-5">
        Datoer
    </h3>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

        <div>

            <h4 class="font-medium text-gray-700 mb-4">
                Modtaget
            </h4>

            <div class="grid grid-cols-2 gap-4">

                <div>

                    <label class="block text-sm mb-2">
                        Fra
                    </label>

                    <input
                        type="date"
                        wire:model.live="filters.modtaget_from"
                        class="w-full rounded-lg border-gray-300">

                </div>

                <div>

                    <label class="block text-sm mb-2">
                        Til
                    </label>

                    <input
                        type="date"
                        wire:model.live="filters.modtaget_to"
                        class="w-full rounded-lg border-gray-300">

                </div>

            </div>

        </div>

        <div>

            <h4 class="font-medium text-gray-700 mb-4">
                Afsluttet
            </h4>

            <div class="grid grid-cols-2 gap-4">

                <div>

                    <label class="block text-sm mb-2">
                        Fra
                    </label>

                    <input
                        type="date"
                        wire:model.live="filters.afsluttet_from"
                        class="w-full rounded-lg border-gray-300">

                </div>

                <div>

                    <label class="block text-sm mb-2">
                        Til
                    </label>

                    <input
                        type="date"
                        wire:model.live="filters.afsluttet_to"
                        class="w-full rounded-lg border-gray-300">

                </div>

            </div>

        </div>

    </div>

</div>