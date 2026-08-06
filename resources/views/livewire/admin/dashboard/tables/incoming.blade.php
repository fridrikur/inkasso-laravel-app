<div class="p-4 border-b font-semibold">
            Indkomne sager
        </div>

        <div class="p-4">

            @if($loadIncoming)

                <livewire:sager.sager-data-table
                    mode="incoming"
                    uiMode="table"
                    :key="'incoming-table'"
                />

            @else

                <div class="flex items-center justify-center h-32">

            <svg
                class="animate-spin h-8 w-8 text-blue-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24">

                <circle
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                    class="opacity-25"
                />

                <path
                    fill="currentColor"
                    class="opacity-75"
                    d="M4 12a8 8 0 018-8v8H4z"
                />

            </svg>

        </div>

            @endif

        </div>
