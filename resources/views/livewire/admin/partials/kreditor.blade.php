<div class="bg-white rounded-xl shadow p-4">

    <div class="font-semibold mb-3">Sager pr. kreditor</div>

    <div class="space-y-2 max-h-64 overflow-y-auto">

        @foreach($recordsByKreditor as $name => $count)

            <div class="flex justify-between text-sm cursor-pointer hover:bg-gray-50 px-2 py-1 rounded"
                 wire:click="filterByKreditor('{{ $name }}')">

                <span>{{ $name }}</span>
                <span class="font-semibold">{{ $count }}</span>

            </div>

        @endforeach

    </div>

</div>