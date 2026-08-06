<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
    <label class="block text-gray-700 font-semibold mb-3">
        Kreditor
    </label>

    <div class="flex items-center space-x-3">
        {{-- LotusID input --}}
        <div class="w-1/3">
            <input
                type="text"
                wire:model.live="kreditornr"
                placeholder="Kreditornr (LotusID)"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
            >
        </div>

        {{-- Kreditor dropdown --}}
        <div class="w-2/3">
            <select wire:model="selectedKreditor" class="...">
                <option value="">Vælg kreditor</option>
                @foreach($options as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Hidden kreditor field for form submission --}}
    <input type="hidden" name="kreditor" wire:model="selectedKreditor" />

    {{-- Optional validation error display --}}
    @error('selectedKreditor')
        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
    @enderror
</div>
