<div class="flex items-center justify-between">

    <div>
        <h1 class="text-3xl font-bold text-gray-900">
            Avanceret sagsøgning
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Dynamisk søgning i sager
        </p>
    </div>

    <div class="flex gap-2">

        <button
            wire:click="newSearch"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white"
        >
            Ny søgning
        </button>

        <button
            wire:click="resetFilters"
            class="px-4 py-2 rounded-lg bg-white border"
        >
            Nulstil
        </button>

    </div>

</div>