<div class="mb-4">
    <label class="block font-medium">{{ ucfirst($field) }}</label>
    <select wire:model="form.{{ $field }}" class="border rounded p-1 w-full">
        @foreach($options[$field] ?? [] as $option)
            <option value="{{ $option->id }}">{{ $option->navn ?? $option->tekst ?? $option->id }}</option>
        @endforeach
    </select>
    @error('form.'.$field) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
