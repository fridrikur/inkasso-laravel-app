@props([
    'title' => 'Modal Title', 
    'size' => 'md',
])

@php
$maxWidth = match($size) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    default => 'max-w-md',
};
@endphp

<div x-data="{ modalIsOpen: false }" x-cloak>
    <!-- Trigger slot -->
    {{ $trigger ?? '' }}

    <!-- Overlay -->
    <div 
        x-show="modalIsOpen" 
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="modalIsOpen = false"
    >
        <!-- Modal -->
        <div 
            x-show="modalIsOpen"
            x-transition.scale.duration.200ms
            class="bg-white rounded-lg shadow-lg w-full {{ $maxWidth }} p-4"
        >
            <!-- Header -->
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h2 class="text-lg font-semibold">{{ $title }}</h2>
                <button @click="modalIsOpen = false" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>

            <!-- Body -->
            <div class="mb-4">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-2">
                {{ $footer ?? '' }}
            </div>
        </div>
    </div>
    </div>
