<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-900">
            Økonomi
        </h3>

        <p class="text-xs text-gray-500">
            Filtrer på økonomisk status og beløb
        </p>
    </div>

    {{-- MAIN STATUS --}}
    <div class="grid md:grid-cols-3 gap-3">

        <select
            wire:model.live="filters.oekonomisk_status"
            class="rounded-lg border-gray-300 text-sm"
        >
            <option value="">Alle status</option>
            <option value="betalt">Betalt</option>
            <option value="restance">Restance</option>
            <option value="restgaeld">Restgæld</option>
        </select>

        <input
            wire:model.live="filters.restgaeld_dkg"
            class="rounded-lg border-gray-300 text-sm"
            placeholder="Restgæld DKG"
        >

        <input
            wire:model.live="filters.restgaeld_kreditor"
            class="rounded-lg border-gray-300 text-sm"
            placeholder="Restgæld kreditor"
        >

    </div>

</div>