<div class="bg-white rounded-2xl shadow border">

    <div class="px-6 py-5 border-b flex justify-between">

        <div>

            <h2 class="text-xl font-semibold">
                Søgeresultater
            </h2>

            <p class="text-gray-500">
                {{ number_format($total,0,',','.') }} sager
            </p>

        </div>

        <div class="flex gap-2">

            <button
                wire:click="openExportModal"
                class="px-4 py-2 rounded-lg bg-green-600 text-white">

                Excel

            </button>

        </div>

    </div>

    <livewire:sager.sager-data-table
        :filters="$filters"
        :mode="'all'"
        uiMode="table"
        :key="'results-'.$searchVersion"
/>

</div>