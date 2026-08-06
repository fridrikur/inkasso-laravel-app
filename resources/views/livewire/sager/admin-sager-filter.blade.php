<div class="max-w-4xl mx-auto p-6 bg-white shadow rounded">
    <h2 class="text-xl font-bold mb-4">Tilrettede sagsfelter til {{ $kreditor->navn }}</h2>

    @if(session()->has('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Felt</th>
                    <th class="px-4 py-2 text-center">Synligt</th>
                    <th class="px-4 py-2 text-center">Påkrævet</th>
                    <th class="px-4 py-2 text-center">Redigérbart</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($fields as $fieldName => $data)
                    <tr>
                        <td class="px-4 py-2">{{ $fieldName }}</td>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox" wire:model="fields.{{ $fieldName }}.visible" class="h-5 w-5">
                        </td>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox" wire:model="fields.{{ $fieldName }}.required" class="h-5 w-5">
                        </td>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox" wire:model="fields.{{ $fieldName }}.editable" class="h-5 w-5">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="relative">

            <div class="mt-6">
                <button 
                    wire:click="save" 
                    class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700"
                >
                    Gem
                </button>
            </div>
        </div>
    </div>
</div>