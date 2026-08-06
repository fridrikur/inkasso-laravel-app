@props([
    'legendItems' => [], // Example: [['value'=>'HK','label'=>'Hovedkonsulent','color'=>'green','count'=>2], ...]
    'model',            // liveWire model class
    'columns'           // Columns array for datatable
])

<div x-data="{ selectedStatuses: [] }" x-cloak>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-2 mb-4">
        @foreach ($legendItems as $item)
            <button type="button"
                @click="selectedStatuses.includes('{{ $item['value'] }}') ? selectedStatuses = selectedStatuses.filter(s => s !== '{{ $item['value'] }}') : selectedStatuses.push('{{ $item['value'] }}')"
                :class="selectedStatuses.includes('{{ $item['value'] }}') ? 'ring-2 ring-offset-2 ring-indigo-500' : ''"
                class="px-3 py-1 rounded border font-medium text-sm flex items-center gap-1 transition-all duration-150 hover:scale-105"
                style="border-color: {{ $item['color'] ?? '#ccc' }}">
                
                <span class="px-2 py-0.5 rounded-full text-white text-xs font-bold"
                      :class="{
                          'bg-green-500': '{{ $item['color'] ?? '' }}' === 'green',
                          'bg-yellow-400': '{{ $item['color'] ?? '' }}' === 'yellow',
                          'bg-blue-500': '{{ $item['color'] ?? '' }}' === 'blue',
                          'bg-gray-400': !['green','yellow','blue'].includes('{{ $item['color'] ?? '' }}')
                      }">
                    {{ $item['value'] }}
                </span>
                
                {{ $item['label'] }}
                
                @if(isset($item['count']))
                    <span class="ml-1 px-2 py-0.5 bg-indigo-500 text-white text-xs rounded-full font-semibold">
                        {{ $item['count'] }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- DataTable --}}
    <div class="overflow-x-auto">
        <liveWire:data-table :model="$model" :columns="$columns" :wire:key="now()">
            {{-- Pass selectedStatuses to the table rows via Alpine --}}
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('legendFilter', () => ({
                        selectedStatuses: @entangle('selectedStatuses'),
                    }))
                })
            </script>
        </liveWire:data-table>
    </div>

</div>