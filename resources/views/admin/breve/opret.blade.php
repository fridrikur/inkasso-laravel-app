<x-layouts.app>

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                Brevskabeloner
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Opret og administrér brevskabeloner
            </p>
        </div>

        {{-- Livewire --}}
        @livewire('admin.breve.opret-brev')

    </div>

</x-layouts.app>