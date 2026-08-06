<div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg text-white p-8">

    <div class="flex justify-between items-center">

        <div>

            <div class="text-5xl font-bold">

                {{ number_format($total,0,',','.') }}

            </div>

            <div class="mt-2 text-blue-100">

                sager matcher dine filtre

            </div>

        </div>

        <div class="flex gap-3">

            @if($hasResults)

                <button
                    wire:click="openResults"
                    class="px-6 py-3 rounded-lg bg-white text-blue-700 font-semibold">

                    Se sager

                </button>

            @endif

            @if($hasResults)

                <button
                    wire:click="saveSearch"
                    class="px-6 py-3 rounded-lg bg-blue-900">

                    Gem søgning

                </button>

            @endif

        </div>

    </div>

</div>