<div>
    <div><h1>{{ $status->navn }} Sager</h1></div>   
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    @foreach($groupedResults as $statusName => $group)
        @if($group['items']->count())
            <div class="border rounded-lg shadow-sm bg-white p-4">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold">
                        {{ $statusName }}
                    </h2>
                    <span class="text-sm bg-gray-100 px-2 py-1 rounded">
                        {{ $group['total'] }} sager
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="py-2 pr-4">Sagsnr</th>
                                <th class="py-2 pr-4">Kreditor</th>
                                <th class="py-2 pr-4">Modtaget</th>
                                <th class="py-2 pr-4">Afsluttet</th>
                                <th class="py-2 pr-4">Hovedstol</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['items'] as $sag)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 pr-4">{{ $sag->sagsnr }}</td>
                                    <td class="py-2 pr-4">{{ optional($sag->sagerkreditor->first())->navn }}</td>
                                    <td class="py-2 pr-4">{{ optional($sag->modtaget)?->format('d-m-Y') }}</td>
                                    <td class="py-2 pr-4">{{ optional($sag->afsluttet)?->format('d-m-Y') }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) str_replace(',', '.', $sag->hovedstol ?? 0), 2, ',', '.') }} kr.</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($group['total'] > 5)
                    <div class="mt-4 text-right">
                        <a href="{{ route('admin.sager.status', $group['status']->id) }}"
                           class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                            Vis alle ({{ $group['total'] }}) →
                        </a>
                    </div>
                @endif

            </div>
        @endif
    @endforeach
    </div>
</div>