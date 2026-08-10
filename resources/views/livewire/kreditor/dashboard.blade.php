<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-8">

    {{-- HEADER & VELKOMST BANNER --}}
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-200 border border-indigo-400/30 text-xs font-semibold mb-3">
                    🏢 Kreditor Portal
                </span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight">
                    Velkommen, {{ $kreditor->navn }}
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1">
                    Her er din aktuelle status og oversigt over sager hos DKG.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button
                    wire:click="createSag"
                    class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg transition flex items-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Opret ny sag</span>
                </button>
            </div>
        </div>
    </div>

    {{-- HOVED NØGLETAL (KPI CARDS) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        {{-- AKTIVE SAGER --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aktive sager</p>
                <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $this->activeCount }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Under aktiv behandling</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                📂
            </div>
        </div>

        {{-- SAMLET HOVEDSTOL --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aktiv hovedstol</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">
                    {{ number_format($this->totalHovedstol, 2, ',', '.') }} kr.
                </p>
                <p class="text-[11px] text-slate-400 mt-0.5">Tilgodehavende i alt</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                💰
            </div>
        </div>

        {{-- AFSLUTTEDE SAGER I ALT --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Afsluttede sager</p>
                <p class="text-3xl font-bold text-slate-900 mt-1">{{ $this->closedCount }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Historisk afsluttede</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-lg">
                ✅
            </div>
        </div>

        {{-- HURTIG SØGNING / SKIFT --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm flex flex-col justify-between">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Hurtig filtrering</p>
            <div class="flex items-center gap-2 mt-2">
                <button wire:click="showActive" class="flex-1 py-1.5 px-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition text-center">
                    Aktive
                </button>
                <button wire:click="showClosed" class="flex-1 py-1.5 px-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition text-center">
                    Afsluttede
                </button>
                <button wire:click="showAll" class="flex-1 py-1.5 px-2 border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold rounded-xl transition text-center">
                    Alle
                </button>
            </div>
        </div>

    </div>

    {{-- SØGEBAR --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form wire:submit.prevent="performSearch" class="flex gap-3">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    🔍
                </span>
                <input
                    type="text"
                    wire:model="search"
                    placeholder="Søg efter sagsnummer, debitor navn..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                >
            </div>
            <button
                type="submit"
                class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer shrink-0"
            >
                Søg
            </button>
        </form>
    </div>

    {{-- SENESTE SAGER TABEL --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Senest oprettede sager</h2>
            <a href="{{ route('kreditor.sager.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                Se alle sager &rarr;
            </a>
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
                    @forelse($this->recentSager as $sag)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-bold text-slate-900 font-mono">
                                #{{ $sag->sagsnr }}
                            </td>
                            <td class="py-3.5 px-6 font-medium">
                                {{ $sag->sagerdebitor->first()?->navn ?? 'Ingen debitor' }}
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-900">
                                {{ number_format($sag->hovedstol, 2, ',', '.') }} kr.
                            </td>
                            <td class="py-3.5 px-6 text-slate-400">
                                {{ $sag->created_at->format('d/m-Y') }}
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
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                Du har ingen sager registreret i systemet endnu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- AFSLUTTEDE SAGER STATISTIK OG GRAF --}}
    @if(array_sum($this->closedStats) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">
                    Fordeling af afsluttede sager (sidste 30 dage)
                </h2>
                <div class="h-64">
                    <div
                        x-data="{
                            chart: null,
                            init() {
                                const labels = @js($this->chartData['labels']);
                                const values = @js($this->chartData['values']);

                                this.chart = new Chart(this.$refs.canvas, {
                                    type: 'doughnut',
                                    data: {
                                        labels,
                                        datasets: [{ data: values }],
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: { legend: { position: 'bottom' } },
                                    },
                                });
                            }
                        }"
                        class="h-full"
                    >
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-3">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-2">
                    Afslutningsårsager
                </h2>
                @foreach($afslutninger as $afslutning)
                    <a
                        href="{{ route('kreditor.search', ['filter' => 'closed', 'afslutning_id' => $afslutning->id]) }}"
                        class="flex items-center justify-between p-3 rounded-2xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/30 transition text-xs font-semibold text-slate-700"
                    >
                        <span>{{ $afslutning->tekst }}</span>
                        <span class="font-mono text-indigo-600 font-bold">
                            {{ $this->closedStats[$afslutning->tekst] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>