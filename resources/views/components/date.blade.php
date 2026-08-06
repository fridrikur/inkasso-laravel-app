@props(['label', 'wire'])

<div>
    <label class="block font-medium text-gray-700 mb-1">{{ $label }}</label>
    <input type="date" wire:model.lazy="{{ $wire }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
    @error($wire)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
