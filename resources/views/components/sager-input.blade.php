@props([
    'label' => '',
    'type' => 'text',
    'model' => '',
    'numeric' => false,
])

<div>
    <label class="block font-medium text-gray-700 mb-1">{{ $label }}</label>
    <input
        type="{{ $type }}"
        wire:model.lazy="{{ $model }}"
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
        @if($numeric) pattern="[0-9]*" inputmode="numeric" @endif
    >
    @error($model)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
