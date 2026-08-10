<x-layouts.app title="Import detaljer #{{ $session->id }}">
    <div 
        x-data="{ 
            previewModalOpen: false, 
            activeSag: null,
            openPreview(sagData) {
                this.activeSag = sagData;
                this.previewModalOpen = true;
            }
        }"
        class="max-w-6xl mx-auto py-8 px-4 sm:px-6 space-y-8 relative"
    >

        {{-- TOP NAVIGATION & STATUS BADGE --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
            <div class="space-y-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                        Import Session <span class="font-mono text-indigo-600">#{{ $session->id }}</span>
                    </h1>

                    {{-- STATUS BADGES --}}
                    @if($session->status === 'rolled_back')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200/80 font-bold text-xs shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>↺ Rullet tilbage</span>
                        </span>
                    @elseif(($session->failed ?? $session->failed_rows ?? 0) > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200/80 font-bold text-xs shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            <span>⚠️ Delvist gennemført med fejl</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-bold text-xs shadow-sm">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span>✓ Gennemført succesfuldt</span>
                        </span>
                    @endif
                </div>

                <p class="text-xs text-slate-500 flex items-center gap-2">
                    <span>Oprettet: <strong class="text-slate-700 font-mono">{{ $session->created_at?->format('d/m-Y H:i') ?? '-' }}</strong></span>
                    <span>•</span>
                    <span>Kreditor: <strong class="text-slate-700">{{ $session->kreditor?->navn ?? 'Ukendt Kreditor' }}</strong></span>
                </p>
            </div>

            {{-- HANDLINGS-KNAPPER --}}
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('sager.import.log') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-sm">
                    <span>← Tilbage til log</span>
                </a>

                @if($session->status === 'rolled_back' || ($session->failed ?? $session->failed_rows ?? 0) > 0)
                    @if(Route::has('sager.import.session.retry'))
                        <a href="{{ route('sager.import.session.retry', $session) }}" 
                           class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                            <span>🔄 Forsøg import igen</span>
                        </a>
                    @endif
                @endif

                @if(($session->inserted ?? $session->inserted_rows ?? 0) > 0 && $session->status !== 'rolled_back')
                    <form method="POST"
                        action="{{ route('sager.import.session.rollback', $session) }}"
                        onsubmit="return confirm('Er du sikker på, at du vil rulle denne import tilbage? Alle oprettede sager i denne session vil blive slettet.');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs px-4 py-2.5 rounded-xl border border-rose-200 transition cursor-pointer">
                            <span>🚨 Rul import tilbage</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- STATS / NØGLETAL --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            
            {{-- INDSATTE SAGER --}}
            <div class="bg-gradient-to-b from-emerald-50/40 to-white rounded-3xl border border-emerald-100 p-6 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl font-black text-emerald-600 font-mono tracking-tight">
                            {{ number_format($session->inserted ?? $session->inserted_rows ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-1">
                            Sager indsat
                        </div>
                    </div>
                    <div class="h-10 w-10 rounded-2xl bg-emerald-100/60 flex items-center justify-center text-emerald-600 text-lg">
                        ✓
                    </div>
                </div>
            </div>

            {{-- FEJLEDE RÆKKER --}}
            <div class="bg-gradient-to-b from-rose-50/40 to-white rounded-3xl border border-rose-100 p-6 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl font-black {{ ($session->failed ?? $session->failed_rows ?? 0) > 0 ? 'text-rose-600' : 'text-slate-400' }} font-mono tracking-tight">
                            {{ number_format($session->failed ?? $session->failed_rows ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-1">
                            Rækker fejlede
                        </div>
                    </div>
                    <div class="h-10 w-10 rounded-2xl bg-rose-100/60 flex items-center justify-center text-rose-600 text-lg">
                        ⚠️
                    </div>
                </div>
            </div>

            {{-- FIL INFORMATION --}}
            <div class="bg-gradient-to-b from-indigo-50/40 to-white rounded-3xl border border-indigo-100 p-6 shadow-sm relative overflow-hidden">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="text-sm font-bold text-slate-900 truncate font-mono">
                            {{ $session->file_name ?? basename($session->file_path ?? 'Kilde-fil') }}
                        </div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mt-1">
                            Importeret kilde-fil
                        </div>
                    </div>
                    <div class="h-10 w-10 rounded-2xl bg-indigo-100/60 flex items-center justify-center text-indigo-600 text-lg flex-shrink-0">
                        📄
                    </div>
                </div>
            </div>

        </div>

        {{-- VISUEL MAPPING FORKLARING --}}
        @includeWhen(view()->exists('sager.import.partials.mapping-legend'), 'sager.import.partials.mapping-legend')

        {{-- TABEL OVER IMPORTEREDE SAGER --}}
        @if(isset($sager) && $sager->count() > 0)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <span>📋</span>
                        <span>Importerede sager ({{ $sager->count() }})</span>
                    </h3>
                </div>

                <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[10px] bg-slate-50/60">
                                <th class="py-3.5 px-4">Sagsnummer</th>
                                <th class="py-3.5 px-4">Debitor</th>
                                <th class="py-3.5 px-4 text-right">Hovedstol</th>
                                <th class="py-3.5 px-4 text-right">Restance</th>
                                <th class="py-3.5 px-4 text-right">Ydelse</th>
                                <th class="py-3.5 px-4 text-center">Oprettet</th>
                                <th class="py-3.5 px-4 text-right">Handling</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($sager as $sag)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-4 font-bold font-mono text-slate-900">
                                        {{ $sag->sagsnr }}
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-slate-800">
                                        @if($sag->sagerdebitor && $sag->sagerdebitor->count() > 0)
                                            <div class="space-y-0.5">
                                                @foreach($sag->sagerdebitor as $deb)
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-slate-900 font-bold">{{ $deb->navn ?? 'Uden navn' }}</span>
                                                        @if(!empty($deb->pnr))
                                                            <span class="text-[10px] font-mono text-slate-400">({{ $deb->pnr }})</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Ingen debitor</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-semibold text-slate-900">
                                        {{ number_format($sag->hovedstol ?? 0, 2, ',', '.') }} kr.
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-rose-600 font-medium">
                                        {{ number_format($sag->restgaeld_dkg ?? 0, 2, ',', '.') }} kr.
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-600">
                                        {{ number_format($sag->n_mdlydelse ?? 0, 2, ',', '.') }} kr.
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono text-slate-500 whitespace-nowrap">
                                        {{ $sag->created_at?->format('d/m-Y H:i') ?? '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        {{-- 🟢 "SE SAG" PREVIEW KNAP --}}
                                        <button 
                                            type="button"
                                            @click="openPreview({{ json_encode([
                                                'id' => $sag->id,
                                                'sagsnr' => $sag->sagsnr,
                                                'hovedstol' => number_format($sag->hovedstol ?? 0, 2, ',', '.'),
                                                'restance' => number_format($sag->restgaeld_dkg ?? 0, 2, ',', '.'),
                                                'ydelse' => number_format($sag->n_mdlydelse ?? 0, 2, ',', '.'),
                                                'stelnr' => $sag->stelnr ?? '-',
                                                'fakturanr' => $sag->fakturanr ?? '-',
                                                'created_at' => $sag->created_at?->format('d/m-Y H:i') ?? '-',
                                                'debitorer' => $sag->sagerdebitor->map(fn($d) => [
                                                    'navn' => $d->navn,
                                                    'pnr' => $d->pnr,
                                                    'adresse' => $d->adresse,
                                                    'postnr' => $d->postnr,
                                                    'email' => $d->email,
                                                    'tlf' => $d->tlf ?? $d->mobil,
                                                    'rolle' => $d->pivot->rolle ?? 'Debitor'
                                                ])
                                            ]) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-indigo-200 bg-indigo-50/70 hover:bg-indigo-100 text-indigo-700 font-bold text-[11px] transition cursor-pointer shadow-sm"
                                        >
                                            <span>👁️ Se sag</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- FEJLEDE RÆKKER TABEL --}}
        @if(!empty($failedRows) && count($failedRows) > 0)
            <div class="bg-rose-50/60 border border-rose-200/80 rounded-3xl p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-rose-900 flex items-center gap-2">
                        <span>⚠️</span>
                        <span>Fejlede rækker i filen ({{ count($failedRows) }})</span>
                    </h3>
                </div>

                <div class="overflow-x-auto bg-white rounded-2xl border border-rose-100 shadow-sm">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-rose-100/50 text-rose-900 font-semibold uppercase text-[10px]">
                            <tr>
                                <th class="px-4 py-3">Række #</th>
                                <th class="px-4 py-3">Sagsnummer / Kontraktnr</th>
                                <th class="px-4 py-3">Fejlårsag</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-100 text-slate-700">
                            @foreach($failedRows as $fail)
                                <tr class="hover:bg-rose-50/30 transition">
                                    <td class="px-4 py-2.5 font-mono text-slate-500">
                                        {{ $fail['row'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 font-mono font-bold text-slate-900">
                                        {{ $fail['sagsnr'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-rose-700 font-medium">
                                        {{ $fail['reason'] ?? 'Ukendt fejl' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- 🟢 SAGS-PREVIEW MODAL (Vises ved klik på "Se sag") --}}
        <div 
            x-show="previewModalOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="previewModalOpen = false"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto"
            style="display: none;"
        >
            <div 
                @click.away="previewModalOpen = false"
                class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-slate-100 overflow-hidden space-y-6 my-8"
            >
                {{-- MODAL HEADER --}}
                <div class="bg-slate-50/80 px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hurtigvisning</div>
                        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span>📂</span>
                            <span>Sag <span class="font-mono text-indigo-600" x-text="activeSag?.sagsnr"></span></span>
                        </h3>
                    </div>
                    <button 
                        @click="previewModalOpen = false"
                        class="h-8 w-8 rounded-full bg-slate-200/60 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold text-xs transition"
                    >
                        ✕
                    </button>
                </div>

                {{-- MODAL INDHOLD --}}
                <div class="px-6 space-y-6 text-xs" x-if="activeSag">
                    
                    {{-- ØKONOMI NØGLETAL --}}
                    <div class="grid grid-cols-3 gap-3 bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100 text-center">
                        <div>
                            <div class="text-[10px] font-bold text-indigo-900/60 uppercase">Hovedstol</div>
                            <div class="text-sm font-bold text-indigo-950 font-mono mt-0.5" x-text="(activeSag?.hovedstol || '0,00') + ' kr.'"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-rose-800/60 uppercase">Restance</div>
                            <div class="text-sm font-bold text-rose-700 font-mono mt-0.5" x-text="(activeSag?.restance || '0,00') + ' kr.'"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-600 uppercase">Ydelse</div>
                            <div class="text-sm font-bold text-slate-800 font-mono mt-0.5" x-text="(activeSag?.ydelse || '0,00') + ' kr.'"></div>
                        </div>
                    </div>

                    {{-- SAGSDATA --}}
                    <div class="space-y-2">
                        <h4 class="font-bold text-slate-800 uppercase text-[10px] tracking-wider">Sagsdetaljer</h4>
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 grid grid-cols-2 gap-3 font-mono text-[11px]">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-sans">Stelnummer (VIN)</span>
                                <span class="font-bold text-slate-800" x-text="activeSag?.stelnr"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-sans">Fakturanummer</span>
                                <span class="font-bold text-slate-800" x-text="activeSag?.fakturanr"></span>
                            </div>
                        </div>
                    </div>

                    {{-- DEBITORER --}}
                    <div class="space-y-2">
                        <h4 class="font-bold text-slate-800 uppercase text-[10px] tracking-wider">Tilknyttede Debitorer</h4>
                        <template x-for="deb in activeSag?.debitorer || []">
                            <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 space-y-2">
                                <div class="flex justify-between items-center">
                                    <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                                        <span>👤</span>
                                        <span x-text="deb.navn || 'Uden navn'"></span>
                                    </div>
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 font-bold text-[10px] rounded-md font-mono" x-text="deb.pnr ? 'CPR: ' + deb.pnr : 'Ingen CPR'"></span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-600 border-t border-slate-200/60 pt-2">
                                    <div>
                                        <span class="text-slate-400">Adresse:</span>
                                        <span class="font-medium" x-text="(deb.adresse || '-') + (deb.postnr ? ', ' + deb.postnr : '')"></span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400">Kontakt:</span>
                                        <span class="font-medium" x-text="deb.tlf || deb.email || '-'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                </div>

                {{-- MODAL FOOTER --}}
                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                    <button 
                        @click="previewModalOpen = false"
                        class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition"
                    >
                        Luk vindue
                    </button>

                    <a 
                        :href="'/sager/' + activeSag?.id + '/edit'"
                        class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition"
                    >
                        <span>Åbn fuld sag &rarr;</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>