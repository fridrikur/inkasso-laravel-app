<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200/60 text-xs font-semibold mb-2">
                    📂 Sagsoversigt
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                    Alle sager
                </h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                    Fuld oversigt over dine sager, status og udvikling hos DKG.
                </p>
            </div>

            <div>
                <a
                    href="{{ route('kreditor.sag.create') }}"
                    class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-sm transition flex items-center gap-2 cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Opret ny sag</span>
                </a>
            </div>
        </div>

        {{-- SØGEBAR --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-sm space-y-3">
            <div wire:loading wire:target="search" class="text-xs font-semibold text-indigo-600 flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Søger i sager...
            </div>

            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    🔍
                </span>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Søg efter sagsnummer, debitor navn, adresse eller by..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none transition"
                >
            </div>

            @if($suggestion)
                <div class="text-xs text-slate-600 pt-1">
                    Mente du:
                    <button
                        wire:click="$set('search', '{{ $suggestion }}')"
                        class="text-indigo-600 font-bold underline hover:text-indigo-800 transition"
                    >
                        {{ $suggestion }}
                    </button>
                    ?
                </div>
            @endif
        </div>

        {{-- 🟢 FUNNEL / STATUS FANER (TRAGT) --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <button
                type="button"
                wire:click="$set('statusFilter', 'all')"
                class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-2 {{ ($statusFilter ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
            >
                <span>📋 Alle sager</span>
            </button>

            <button
                type="button"
                wire:click="$set('statusFilter', 'active')"
                class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-2 {{ ($statusFilter ?? 'all') === 'active' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
            >
                <span>🟢 Aktive sager</span>
            </button>

            <button
                type="button"
                wire:click="$set('statusFilter', 'closed')"
                class="px-4 py-2 rounded-2xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-2 {{ ($statusFilter ?? 'all') === 'closed' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
            >
                <span>🏁 Afsluttede sager</span>
            </button>
        </div>

        {{-- DATATABEL --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                    Sagsliste
                </h2>
                <span class="text-xs text-slate-500 font-medium">
                    Viser filtrerede sager
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px] bg-slate-50/30">
                            <th class="py-3 px-6">Sagsnr.</th>
                            <th class="py-3 px-6">Debitor</th>
                            <th class="py-3 px-6">Hovedstol</th>
                            <th class="py-3 px-6">Oprettet</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6 text-right">Handling</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($sager as $sag)
                            <tr
                                id="sag-row-{{ $sag->id }}"
                                class="hover:bg-slate-50/60 transition"
                            >
                                <td class="py-3.5 px-6 font-bold text-slate-900 font-mono">
                                    #{{ $sag->sagsnr }}
                                </td>
                                <td class="py-3.5 px-6 font-medium">
                                    {{ $sag->debitor->first()?->navn ?? 'Ingen debitor' }}
                                </td>
                                <td class="py-3.5 px-6 font-semibold text-slate-900">
                                    {{ number_format($sag->hovedstol, 2, ',', '.') }} kr.
                                </td>
                                <td class="py-3.5 px-6 text-slate-400">
                                    {{ optional($sag->created_at)->format('d/m-Y') }}
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
                                        <span>Åbn</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                    Ingen sager fundet under dette filter.
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

    {{-- SCRIPTS OG STYLING --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const created = urlParams.get('created');
        const sagId = urlParams.get('sag_id');

        if (created === '1') {
            Livewire.dispatch('toast', {
                message: 'Sagen blev oprettet og sendt til DKG',
                type: 'success'
            });

            if (sagId) {
                setTimeout(() => {
                    const row = document.getElementById('sag-row-' + sagId);
                    if (row) {
                        row.classList.add('highlight-row');
                        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => {
                            row.classList.remove('highlight-row');
                        }, 4000);
                    }
                }, 300);
            }

            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
    </script>

    <style>
        .highlight-row {
            animation: highlightFade 4s ease;
        }
        @keyframes highlightFade {
            0%   { background-color: #86efac; }
            50%  { background-color: #dcfce7; }
            100% { background-color: transparent; }
        }
    </style>
</div>