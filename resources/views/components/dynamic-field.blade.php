@props(['setting', 'sag' => null])

@php
    $fieldName = $setting['field_name'] ?? '';
    $label = $setting['alias'] ?? ucfirst(str_replace('_', ' ', $fieldName));
    $type = $setting['field_type'] ?? 'text';
    $readonly = $setting['readonly'] ?? false;
    $required = $setting['required'] ?? false;
    $value = old($fieldName, $sag->{$fieldName} ?? '');
    $options = $setting['options'] ?? [];
@endphp

<div class="p-2 border rounded bg-gray-50">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>

    @if($type === 'textarea')
        <textarea name="{{ $fieldName }}"
                  rows="2"
                  @if($readonly) readonly @endif
                  @if($required) required @endif
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ $value }}</textarea>

    @elseif($type === 'select')
        <select name="{{ $fieldName }}"
                @if($readonly) disabled @endif
                @if($required) required @endif
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">Vælg...</option>
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected($optValue == $value)>{{ $optLabel }}</option>
            @endforeach
        </select>

    @elseif($type === 'boolean')
        <div class="flex items-center space-x-2 mt-1">
            <input type="checkbox"
                   name="{{ $fieldName }}"
                   value="1"
                   @if($value) checked @endif
                   @if($readonly) disabled @endif
                   class="rounded text-blue-600">
            <span>{{ $label }}</span>
        </div>

    @else
        <input type="{{ $type }}"
               name="{{ $fieldName }}"
               value="{{ $value }}"
               @if($readonly) readonly @endif
               @if($required) required @endif
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
    @endif
</div>
