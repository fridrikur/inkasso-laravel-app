@props([
    'id',
    'viewUrl' => null,
    'viewAction' => null,
    'editUrl' => null,
    'showEdit' => true,
    'showDelete' => true,
    'showView' => true,
    'deleteAction' => 'confirmDelete',
    'editAction' => 'openEditModal',
    'deleteConfirm' => null, // 🟢 Tilføjet prop til wire:confirm besked
])

<div class="flex items-center justify-end gap-1.5">
    {{-- EKSTRA KNAPPER (F.EKS. VIS SAGER) --}}
    {{ $slot }}

    <div class="inline-flex items-center gap-1">
        {{-- Vis-knap (med øjet) --}}
        @if($showView)
            @if($viewUrl)
                <a
                    href="{{ $viewUrl }}"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600"
                    title="Vis"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                        <circle cx="12" cy="12" r="2.5"/>
                    </svg>
                </a>
            @elseif($viewAction)
                <button
                    type="button"
                    wire:click="{{ $viewAction }}({{ $id }})"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer"
                    title="Vis"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
                        <circle cx="12" cy="12" r="2.5"/>
                    </svg>
                </button>
            @endif
        @endif

        {{-- Rediger-knap --}}
        @if($showEdit)
            @if($editUrl)
                <a
                    href="{{ $editUrl }}"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    title="Rediger"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 3.651 3.651M4 20h4l11.5-11.5a2.586 2.586 0 0 0-3.657-3.657L4.343 16.343A2 2 0 0 0 4 17.757V20Z"/>
                    </svg>
                </a>
            @else
                <button
                    type="button"
                    wire:click="{{ $editAction }}({{ $id }})"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 cursor-pointer"
                    title="Rediger"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 3.487 3.651 3.651M4 20h4l11.5-11.5a2.586 2.586 0 0 0-3.657-3.657L4.343 16.343A2 2 0 0 0 4 17.757V20Z"/>
                    </svg>
                </button>
            @endif
        @endif

        {{-- Slet-knap (med dynamisk wire:confirm hvis angivet) --}}
        @if($showDelete)
            <button
                type="button"
                wire:click="{{ $deleteAction }}({{ $id }})"
                @if($deleteConfirm) wire:confirm="{{ $deleteConfirm }}" @endif
                class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 cursor-pointer"
                title="Slet"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>
                </svg>
            </button>
        @endif
    </div>
</div>