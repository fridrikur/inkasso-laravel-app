@props([
    'label' => '',
    'model' => '',          // e.g. "form.kreditor"
    'options' => [],
    'id' => null,
    'onChange' => null,
    'loadingTarget' => null,
])

@php
    // id fallback and safe error key
    $id = $id ?? str_replace(['.', '[', ']'], '_', $model);
    $errorKey = $model; // keep the same key (e.g. "form.kreditor")
@endphp

<div class="flex flex-col">
    <label for="{{ $id }}" class="font-medium text-gray-700 mb-2">{{ $label }}</label>

    <select
        id="{{ $id }}"
        wire:model="{{ $model }}"
        @if($onChange) wire:change="{{ $onChange }}" @endif
        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
    >
        <option value="">Vælg {{ strtolower($label) }}</option>
        @foreach ($options as $key => $text)
            <option value="{{ $key }}">{{ $text }}</option>
        @endforeach
    </select>

    {{-- Optional loading spinner --}}
    @if($loadingTarget)
        <span wire:loading wire:target="{{ $loadingTarget }}" class="text-sm text-gray-500 mt-1">
            Indlæser {{ strtolower($label) }}...
        </span>
    @endif

    {{-- Show validation message reliably using $errors bag --}}
    @if($errorKey && $errors->has($errorKey))
        <p class="text-red-600 text-sm mt-1">{{ $errors->first($errorKey) }}</p>
    @endif
</div>