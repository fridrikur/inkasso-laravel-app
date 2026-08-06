<div wire:poll.5s="refreshBadge"><a href="{{ route('kreditor.sager.klientinformation', $sag->id) }}"
class="{{ request()->routeIs('kreditor.sager.klientinformation')
        ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
        whitespace-nowrap pb-2 border-b-2 font-medium text-sm flex items-center gap-2">

    <span>Klientinformation</span>

    @if($klientinformationUnread > 0)
        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-bounce">
            +{{ $klientinformationUnread }}
        </span>
    @endif

</a>
</div>