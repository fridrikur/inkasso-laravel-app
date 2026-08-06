<div class="bg-gray-50 border rounded-xl p-4 space-y-2">

    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">
            Aktiv søgning
        </h3>

        <span class="text-xs text-gray-500">
            {{ $total }} resultater
        </span>
    </div>

    <div class="text-sm text-gray-700 space-y-1">

        {{-- VISIBILITY --}}
        <div>
            <span class="text-gray-500">Visning:</span>
            {{ ucfirst($searchSummary['visibility'] ?? 'all') }}
        </div>

        {{-- STATUS --}}
        @if(!empty($searchSummary['statuses']))
            <div>
                <span class="text-gray-500">Status:</span>
                {{ implode(', ', $searchSummary['statuses']) }}
            </div>
        @endif

        {{-- AFSLUTNING --}}
        @if(!empty($searchSummary['afslutning']))
            <div>
                <span class="text-gray-500">Afslutning:</span>
                {{ implode(', ', $searchSummary['afslutning']) }}
            </div>
        @endif

        {{-- KREDITOR --}}
        @if(!empty($searchSummary['kreditor']))
            <div>
                <span class="text-gray-500">Kreditor:</span>
                {{ $searchSummary['kreditor'] }}
            </div>
        @endif

        {{-- DEBITOR --}}
        @if(!empty($searchSummary['debitor']))
            <div>
                <span class="text-gray-500">Debitor:</span>
                {{ $searchSummary['debitor'] }}
            </div>
        @endif

        {{-- FREE TEXT --}}
        @if(!empty($searchSummary['sagsnr']) || !empty($searchSummary['stelnr']))
            <div>
                <span class="text-gray-500">Søgning:</span>
                {{ $searchSummary['sagsnr'] }} {{ $searchSummary['stelnr'] }}
            </div>
        @endif

    </div>

</div>