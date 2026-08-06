<div class="space-y-6">

    {{-- Tabs --}}
    <div class="flex gap-2 border-b">
        @foreach($tabs as $label => $letters)
            <button
                wire:click="$set('activeTab', '{{ $label }}')"
                class="px-4 py-2 border-b-2
                    {{ $activeTab === $label ? 'border-blue-500 font-semibold' : 'border-transparent' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Reorder list --}}
    <div
        x-data
        x-init="
            Sortable.create($el, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'bg-blue-50',
                onEnd: () => {
                    const ids = Array.from($el.children).map(el => el.dataset.id)
                    $wire.updateOrder(ids)
                }
            })
        "
        class="space-y-2"
    >