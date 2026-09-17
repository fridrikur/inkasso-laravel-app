<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-xs font-semibold mb-2">
                    🔍 Avanceret Søgning
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Søg i sager
                </h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                    Find og filtrér i dine aktive og afsluttede sager med udvidede søgekriterier.
                </p>
            </div>
        </div>

        {{-- SEARCH & FILTERS CONTAINER --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">

            {{-- Sagsnummer søgefelt & Ryd filtre --}}
            <div class="flex flex-col md:flex-row gap-4 items-end justify-between">
                <div class="w-full md:max-w-md">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                        Sagsnummer eller tekst
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            🔍
                        </span>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Søg efter sagsnummer..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition"
                        >
                    </div>
                </div>

                <div>
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-2"
                    >
                        <span>🔄</span> Ryd alle filtre
                    </button>
                </div>
            </div>

            <hr class="border-slate-100 my-4">

            {{-- DATO FILTRE --}}
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                    Dato filtre
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                            Modtaget fra
                        </label>
                        <input
                            type="date"
                            wire:model.live="modtagetFrom"
                            class="w-full rounded-xl border border-slate-200 text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                            Modtaget til
                        </label>
                        <input
                            type="date"
                            wire:model.live="modtagetTo"
                            class="w-full rounded-xl border border-slate-200 text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                            Afsluttet fra
                        </label>
                        <input
                            type="date"
                            wire:model.live="afsluttetFrom"
                            class="w-full rounded-xl border border-slate-200 text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">
                            Afsluttet til
                        </label>
                        <input
                            type="date"
                            wire:model.live="afsluttetTo"
                            class="w-full rounded-xl border border-slate-200 text-xs py-2 px-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        >
                    </div>
                </div>
            </div>

            <hr class="border-slate-100 my-4">

            {{-- STATUS / SAGER FILTER (FANER / TRAGT) --}}
            <div class="space-y-3">
                <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                    Sagsstatus
                </h3>

                <div class="flex flex-wrap gap-2.5">
                    <button
                        type="button"
                        wire:click="$set('filter','all')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ $filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'border border-slate-200 hover:bg-slate-50 text-slate-700' }}"
                    >
                        📋 Alle sager
                    </button>

                    <button
                        type="button"
                        wire:click="$set('filter','active')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ $filter === 'active' ? 'bg-indigo-600 text-white shadow-sm' : 'border border-slate-200 hover:bg-slate-50 text-slate-700' }}"
                    >
                        🟢 Aktive sager
                    </button>

                    <button
                        type="button"
                        wire:click="$set('filter','closed')"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ $filter === 'closed' ? 'bg-slate-900 text-white shadow-sm' : 'border border-slate-200 hover:bg-slate-50 text-slate-700' }}"
                    >
                        🏁 Afsluttede sager
                    </button>
                </div>
            </div>

            {{-- AFSLUTNINGSTYPE FILTRE --}}
            @if(isset($afslutninger) && count($afslutninger) > 0)
                <hr class="border-slate-100 my-4">

                <div class="space-y-3">
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">
                        Afslutningstype
                    </h3>

                    <div class="flex flex-wrap gap-2">
                        @foreach($afslutninger as $afslutning)
                            <button
                                type="button"
                                wire:click="$set('afslutningId', {{ $afslutning->id }})"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer {{ $afslutningId === $afslutning->id ? 'bg-indigo-600 text-white shadow-sm' : 'border border-slate-200 hover:bg-slate-50 text-slate-700' }}"
                            >
                                {{ $afslutning->tekst }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- RESULTS TABLE (MATCHER DASHBOARD OG ALLE SAGER) --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Søgeresultater
                </h2>
                <span class="text-xs text-slate-500 font-medium">
                    Viser matchende sager
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px] bg-slate-50/30">
                            <th class="py-3 px-6">Sagsnr.</th>
                            <th class="py-3 px-6">Debitor</th>
                            <th class="py-3 px-6">Hovedstol</th>
                            <th class="py-3 px-6">Modtaget</th>
                            <th class="py-3 px-6">Afsluttet</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6 text-right">Handling</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($sager as $sag)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-6 font-bold text-slate-900 font-mono">
                                    #{{ $sag->sagsnr }}
                                </td>
                                <td class="py-3.5 px-6 font-medium">
                                    @foreach($sag->debitor as $debitor)
                                        {{ $debitor->navn }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </td>
                                <td class="py-3.5 px-6 font-semibold text-slate-900">
                                    {{ number_format($sag->hovedstol, 2, ',', '.') }} kr.
                                </td>
                                <td class="py-3.5 px-6 text-slate-500">
                                    {{ optional($sag->modtaget)->format('d-m-Y') ?? '-' }}
                                </td>
                                <td class="py-3.5 px-6 text-slate-500">
                                    {{ optional($sag->afsluttet)->format('d-m-Y') ?? '-' }}
                                </td>
                                <td class="py-3.5 px-6">
                                    @if($sag->afsluttet)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[10px]">
                                            Afsluttet
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-semibold text-[10px]">
                                            Aktiv
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    <a 
                                        href="{{ route('kreditor.sag.view', $sag->id) }}" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 text-indigo-600 font-bold text-[11px] transition"
                                    >
                                        <span>Se sag</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                    Ingen sager matcher dine valgte søgekriterier.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $sager->links() }}
        </div>

    </div>
</div>