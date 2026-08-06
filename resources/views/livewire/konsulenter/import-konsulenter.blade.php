<div class="p-6 space-y-6">

    <h1 class="text-2xl font-bold">Importer konsulenter</h1>

    {{-- Upload --}}
    <div class="bg-white p-4 rounded shadow">
        <input type="file" wire:model="file" class="border p-2 rounded w-full">
        @error('file') <p class="text-red-600 mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- Preview --}}
    @if ($preview && count($rows))
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-3">Forhåndsvisning ({{ count($rows) }} rækker)</h2>

            <div class="overflow-x-auto max-h-96 overflow-y-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            @foreach ($headers as $h)
                                <th class="border px-2 py-1">{{ $h }}</th>
                            @endforeach
                            <th class="border px-2 py-1 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $i => $row)
                            <tr class="odd:bg-gray-50">
                                @foreach ($headers as $h)
                                    <td class="border px-2 py-1">
                                        {{ $row[$h] ?? '' }}
                                    </td>
                                @endforeach
                                <td class="border px-2 py-1 text-center">
                                    @if (in_array($i, $importedRows))
                                        <span class="text-green-600 font-bold">✔</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex gap-4">
                <button
                    wire:click="import"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                    Importér
                </button>

                <button
                    wire:click="exportCsv"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    Eksport CSV
                </button>

                <button
                    wire:click="exportJson"
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
                >
                    Eksport JSON
                </button>
            </div>
        </div>
    @endif

    {{-- Result --}}
    @if ($importedCount || count($skipped))
        <div class="bg-white p-4 rounded shadow">
            <p class="text-green-700 font-semibold">
                Importerede: {{ $importedCount }}
            </p>

            @if (count($skipped))
                <p class="text-yellow-700 mt-2">
                    Ignorerede: {{ count($skipped) }}
                </p>

                <ul class="list-disc ml-6 mt-2 text-sm text-gray-700">
                    @foreach ($skipped as $skip)
                        <li>Række {{ $skip['index'] + 1 }} – {{ $skip['reason'] }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

</div>