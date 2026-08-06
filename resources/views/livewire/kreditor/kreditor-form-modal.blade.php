<div>@if($showModal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">

        <button
            wire:click="closeModal"
            class="absolute top-2 right-2 text-gray-500 text-2xl hover:text-gray-700"
        >
            &times;
        </button>

        <h2 class="text-lg font-semibold mb-4">
            {{ $kreditor ? 'Rediger kreditor' : 'Opret kreditor' }}
        </h2>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Navn</label>
            <input
                type="text"
                wire:model.defer="navn"
                class="w-full border-gray-300 rounded-md shadow-sm"
            >
            @error('navn')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Lotus ID</label>
            <div class="mb-4">
    
            <input
                type="number"
                wire:model.live="lotusID"
                placeholder="lotusID"
                class="border rounded px-3 py-2 w-full"
            >

    {{-- STATUS --}}
    @if(!empty($lotusID))

        @if(in_array($lotusID, $usedLotusIds))

            <div class="text-red-500 text-xs mt-1">
                ⚠ LotusID findes allerede i systemet
            </div>

        @else

            <div class="text-green-600 text-xs mt-1">
                ✓ LotusID er ledigt
            </div>

        @endif

    @endif
   {{-- SUGGESTION --}}
    <div class="text-blue-600 text-xs mt-2">
        Forslag: {{ $this->suggestedLotusId }}
    </div>

    {{-- USED IDS --}}
    <div class="mt-3">

        <div class="text-xs font-semibold text-gray-500 mb-1">
            Brugte LotusID:
        </div>

        <div class="max-h-24 overflow-y-auto border rounded p-2 bg-gray-50 text-xs text-gray-600">
            {{ implode(', ', $usedLotusIds) }}
        </div>

    </div>
</div>
        </div>

        <div class="flex justify-end gap-2">
            <button
                wire:click="closeModal"
                class="px-4 py-2 bg-gray-200 rounded-md"
            >
                Annuller
            </button>

            <button
                wire:click="save"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md"
            >
                Gem
            </button>
        </div>

    </div>
</div>
@endif
</div>