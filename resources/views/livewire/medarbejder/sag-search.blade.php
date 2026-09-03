<div>
    {{-- HEADER --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Søg sager</h1>
            <p class="mt-1 text-sm text-slate-500">
                Søg på tværs af sagsnumre, debitornavne og kreditornavne.
            </p>
        </div>

        <a href="{{ route('medarbejder.sager.create') }}"
           class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow transition hover:bg-indigo-700 cursor-pointer">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Opret ny sag</span>
        </a>
    </div>

    {{-- SØGEFELT KORT --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
        <div class="relative w-full">
            <input
                type="search"
                wire:model.live.debounce.600ms="search"
                placeholder="Indtast sagsnr, debitor eller kreditor for at søge..."
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pl-11 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                autofocus
            >
            <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6 6a7.5 7.5 0 0 0 10.65 10.65Z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- SØGERESULTATER TABEL --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden relative">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/60">
                    <tr>
                        <th scope="col" class="px-6 py-4">Sagsnr</th>
                        <th scope="col" class="px-6 py-4">Debitor</th>
                        <th scope="col" class="px-6 py-4">Kreditor</th>
                        <th scope="col" class="px-6 py-4">Modtaget</th>
                        <th scope="col" class="px-6 py-4">Status</th>
                        <th scope="col" class="px-6 py-4 text-right">Handling</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                @forelse($this->searchResults as $sag)
                    @php
                        $debitor = $sag->sagerdebitor->first();
                        $kreditor = $sag->sagerkreditor->first();
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition duration-150">
                        <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">
                            <span class="font-mono text-slate-700">#{{ $sag->display_number ?? $sag->sagsnr ?? $sag->id }}</span>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $debitor->navn ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $kreditor->navn ?? '-' }}</td>
                        <td class="whitespace-nowrap px-6 py-4 font-mono text-slate-500">
                            {{ $sag->modtaget ? \Carbon\Carbon::parse($sag->modtaget)->format('d-m-Y') : ($sag->created_at ? $sag->created_at->format('d-m-Y') : '-') }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if($sag->afsluttet)
                                <span class="inline-flex items-center rounded-full bg-slate-100 border border-slate-200 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                    Afsluttet
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-medium text-emerald-700 shadow-sm">
                                    <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Aktiv
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right font-medium">
                            <a href="{{ route('medarbejder.sager.edit', $sag->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white text-xs font-bold transition shadow-2xs">
                                <span>Åbn sag</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z" />
                            </svg>
                            <span class="block text-sm font-semibold text-slate-900">
                                @if(empty(trim($search)))
                                    Indtast noget i søgefeltet ovenfor for at finde sager.
                                @else
                                    Ingen sager matcher din søgning ("{{ $search }}")
                                @endif
                            </span>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>