@props([
    'show' => false,
    'maxWidth' => 'max-w-lg',
    'close' => null,
])

@if ($show)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        wire:click="{{ $close }}"
        wire:keydown.escape.window="{{ $close }}"
        wire:transition.opacity
    >
        <div
            class="bg-white rounded-xl shadow-xl w-full {{ $maxWidth }}"
            wire:click.stop
        >
            {{ $slot }}
        </div>
    </div>
@endif
