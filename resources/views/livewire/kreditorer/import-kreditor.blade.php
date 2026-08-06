<div class="p-6 bg-white rounded shadow">

    <h2 class="text-xl font-bold mb-4">Importer Kreditorer</h2>

    {{-- File upload --}}
    <input type="file" wire:model="file" class="mb-4" />

    @error('file')
        <div class="text-red-600 mb-2">{{ $message }}</div>
    @enderror

    {{-- Export buttons --}}
    <div class="flex gap-4 mb-6">
        <button wire:click="exportCsv"
            class="bg-blue-600 text-white px-4 py-2 rounded">
            Export CSV
        </button>

        <button wire:click="exportJson"
            class="bg-indigo-600 text-white px-4 py-2 rounded">
            Export JSON
        </button>
    </div>

    @if($preview)

        {{-- Mapping --}}
        <div class="mb-6">
            <h3 class="font-semibold mb-2">Felt-mapping</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Lotus ID → kreditors.lotusID</label>
                    <select wire:model="mapping.lotusID" class="w-full border rounded">
                        @foreach($headers as $header)
                            <option value="{{ $header }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Firmanavn → kreditors.navn</label>
                    <select wire:model="mapping.navn" class="w-full border rounded">
                        @foreach($headers as $header)
                            <option value="{{ $header }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($importedCount > 0)
            <div class="mb-4 p-3 bg-green-50 border border-green-300 rounded">
                <span class="font-semibold text-green-700">
                    ✓ Importeret {{ $importedCount }} ud af {{ count($rows) }} rækker
                </span>
            </div>
        @endif

{{-- Preview table --}}
        <div class="overflow-x-auto">
            <table class="table-auto w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-center">✓</th>
                        @foreach($headers as $h)
                            <th class="border px-2 py-1">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $index => $row)
                        @php
                            $imported = in_array($index, $importedRows);
                        @endphp
                        <tr class="{{ $imported ? 'bg-green-50' : '' }}">
                            <td class="border px-2 py-1 text-center font-bold">
                                @if($imported)
                                    <span class="text-green-600">✓</span>
                                @else
                                    <span class="text-gray-400">–</span>
                                @endif
                            </td>

                            @foreach($headers as $h)
                                <td class="border px-2 py-1">
                                    {{ $row[$h] ?? '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>

        {{-- Import button --}}
        <div class="mt-6">
            <button wire:click="import"
                class="bg-green-600 text-white px-6 py-2 rounded">
                Importér kreditorer
            </button>
        </div>

    @endif

</div>
