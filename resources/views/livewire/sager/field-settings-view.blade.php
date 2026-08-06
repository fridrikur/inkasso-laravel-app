<form wire:submit.prevent="save" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @foreach($allFields as $field => $settings)
            @php
                $type = $settings['field_type'] ?? 'text';
                $isNumeric = in_array($field, $numericFields);
            @endphp
            <div>
                <label class="block font-medium text-gray-700 mb-1" for="{{ $field }}">
                    {{ $settings['alias'] ?? ucfirst($field) }}
                </label>

                @if($type === 'textarea')
                    <textarea
                        wire:model.defer="form.{{ $field }}"
                        
                        class="w-full border-gray-300 rounded p-2"
                        rows="3"></textarea>

                @elseif($type === 'date')
                    <input
                        type="date"
                        wire:model.defer="form.{{ $field }}"
                        class="w-full border-gray-300 rounded p-2">

                @else
                    <input
                        type="text"
                        wire:model.live="form.{{ $field }}"
                        class="w-full border-gray-300 rounded p-2">
                @endif

                @error("form.$field")
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Gem Sag
        </button>
    </div>
</form>
