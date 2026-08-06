<div class="space-y-5">

    {{-- Header --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-6 py-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h2 class="text-2xl font-semibold text-gray-900">
                    Sager for {{ $kreditornavn }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $total }} sager fundet
                </p>
            </div>
        </div>
        
            <livewire:sager.sager-data-table
                mode="kreditor"
                ui-mode="table"
                :kreditor="$kreditor"
                :key="'kreditor'.$kreditor"
            />
    
    {{-- Pagination --}}
    <div>
        {{ $sager->links() }}
    </div>
</div>

</div>
</div>