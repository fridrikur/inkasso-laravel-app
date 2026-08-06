@props(['statuses' => []])

<td class="px-4 py-2 space-x-1">
    @foreach((array) $statuses as $st)
        @switch($st)
            @case('HK')
                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-bold">
                    HK
                </span>
                @break

            @case('SK')
                <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium">
                    SK
                </span>
                @break

            @case('NK')
                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                    NK
                </span>
                @break
        @endswitch
    @endforeach
</td>
