<div class="w-full px-6 py-6 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                {{ $savedSearch->name }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                {{ $this->results->total() }} resultater fundet
            </p>
        </div>

        <a
            href="{{ route('search-constructor') }}"
            class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
        >
            Tilbage
        </a>
    </div>

    <div class="w-full bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Sagsnr</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Kreditor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Debitor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Postnr</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($this->results as $sag)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3">{{ $sag->sagsnr }}</td>
                            <td class="px-4 py-3">{{ $sag->sagerkreditor->pluck('navn')->join(', ') }}</td>
                            <td class="px-4 py-3">{{ $sag->sagerdebitor->pluck('navn')->join(', ') }}</td>
                            <td class="px-4 py-3">{{ $sag->sagerStatus->pluck('tekst')->join(', ') }}</td>
                            <td class="px-4 py-3">{{ $sag->sagerdebitor->pluck('postnr')->join(', ') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                                Ingen resultater fundet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-4 py-4">
            {{ $this->results->links() }}
        </div>
    </div>
</div>