@php
    $items = [
        [
            'label' => 'Gemte søgninger',
            'route' => 'search-constructor',
            'active' => request()->routeIs('search-constructor'),
        ],
        [
            'label' => 'Låste søgninger',
            'route' => 'lukkede.sager.search',
            'active' => request()->routeIs('lukkede.sager.search'),
        ],
        [
            'label' => 'Fri søgning',
            'route' => 'sager.search',
            'active' => request()->routeIs('sager.search'),
        ],
    ];
@endphp

<nav class="mb-6">
    <ol class="flex items-center space-x-2 text-sm text-gray-500">
        @foreach ($items as $index => $item)
            <li class="flex items-center">
                
                <a href="{{ route($item['route']) }}"
                   class="
                        px-3 py-1 rounded-md transition
                        {{ $item['active']
                            ? 'bg-indigo-100 text-indigo-700 font-semibold'
                            : 'hover:text-gray-700 hover:bg-gray-100'
                        }}
                   ">
                    {{ $item['label'] }}
                </a>

                @if (!$loop->last)
                    <span class="mx-2 text-gray-400">/</span>
                @endif
            </li>

        @endforeach
    </ol>
</nav>