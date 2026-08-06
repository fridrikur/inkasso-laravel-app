@props([
    'items' => [],
])

<div
    x-data="{
        selected: [],
        toggle(v) {
            if (this.selected.includes(v)) {
                this.selected = this.selected.filter(s => s !== v)
            } else {
                this.selected.push(v)
            }
            // Tell liveWire DataTable about the change (no entangle needed)
            window.dispatchEvent(new CustomEvent('legend-status-change', { detail: { statuses: this.selected }}))
        }
    }"
    class="flex flex-wrap items-center gap-2"
>
    @foreach ($items as $item)
        @php
            $color = $item['color'] ?? 'gray';
            $label = $item['label'] ?? ($item['value'] ?? '');
            $value = $item['value'] ?? '';
            $count = $item['count'] ?? null;
        @endphp

        <button
            type="button"
            @click="toggle(@js($value))"
            class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-sm font-medium transition
                   hover:bg-gray-50"
            :class="selected.includes(@js($value)) ? 'ring-2 ring-offset-2 ring-indigo-500' : ''"
        >
            {{-- small status badge --}}
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold text-white"
                  :class="{
                    'bg-green-500': @js($color) === 'green',
                    'bg-yellow-500': @js($color) === 'yellow',
                    'bg-blue-500': @js($color) === 'blue',
                    'bg-gray-500': !['green','yellow','blue'].includes(@js($color))
                  }">
              {{ $value }}
            </span>
            <span class="text-gray-700">{{ $label }}</span>

            @if(!is_null($count))
                <span class="ml-1 inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-[11px] font-semibold text-indigo-700">
                    {{ $count }}
                </span>
            @endif
        </button>
    @endforeach
</div>
