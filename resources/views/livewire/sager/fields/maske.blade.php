<div class="mb-4">
    <label class="block font-medium">{{ ucfirst($field) }}</label>
    <input type="text" wire:model="form.{{ $field }}" class="border rounded p-1 w-full" x-mask:dynamic="$money($input, ',')" x-format-number>
    @error('form.'.$field) <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
</div>
