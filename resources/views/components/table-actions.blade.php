@props([
    'id',
    'editUrl' => null,
    'showEdit' => true,
    'showDelete' => true,
    'deleteAction' => 'confirmDelete',
    'editAction' => 'openEditModal'
])

<div class="flex items-center justify-end gap-1.5">
    {{-- EKSTRA KNAPPER (F.EKS. OVERFØR) --}}
    {{ $slot }}

    {{-- REDIGÉR KNAP --}}
    @if($showEdit)
        @if($editUrl)
            <a 
                href="{{ $editUrl }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition"
                title="Redigér"
            >
                <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Redigér
            </a>
        @else
            <button
                type="button"
                wire:click="{{ $editAction }}({{ $id }})"
                class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:text-slate-900 transition cursor-pointer"
                title="Redigér"
            >
                <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Redigér
            </button>
        @endif
    @endif

    {{-- SLET KNAP --}}
    @if($showDelete)
        <button
            type="button"
            wire:click="{{ $deleteAction }}({{ $id }})"
            class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-rose-600 shadow-sm hover:bg-rose-50 hover:border-rose-200 transition cursor-pointer"
            title="Slet"
        >
            <svg class="w-3.5 h-3.5 mr-1 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Slet
        </button>
    @endif
</div>