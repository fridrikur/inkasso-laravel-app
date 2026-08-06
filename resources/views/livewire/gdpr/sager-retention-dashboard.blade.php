<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">GDPR Sagsretention</h1>
            <p class="text-sm text-slate-500">Overblik over sager der har nået eller nærmer sig 5-års grænsen for anonymisering</p>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <button 
            type="button"
            wire:click="setTab('expired')"
            class="text-left p-5 rounded-2xl border transition shadow-sm {{ $tab === 'expired' ? 'border-rose-500 bg-rose-50/50 ring-2 ring-rose-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-rose-700">Klar til anonymisering / sletning (> 5 år)</span>
                <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">{{ $stats['expired_count'] }}</span>
            </div>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['expired_count'], 0, ',', '.') }} sager</p>
        </button>

        <button 
            type="button"
            wire:click="setTab('expiring')"
            class="text-left p-5 rounded-2xl border transition shadow-sm {{ $tab === 'expiring' ? 'border-amber-500 bg-amber-50/50 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
        >
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-amber-700">Nærmer sig udløb (4–5 år)</span>
                <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">{{ $stats['expiring_soon_count'] }}</span>
            </div>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['expiring_soon_count'], 0, ',', '.') }} sager</p>
        </button>
    </div>

    {{-- TABEL OG HANDLINGER --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h2 class="font-semibold text-slate-800">
                {{ $tab === 'expired' ? 'Sager klar til behandling' : 'Sager der nærmer sig 5 år' }}
            </h2>

            {{-- MASSE-HANDLINGER --}}
            @if($tab === 'expired' && count($selected) > 0)
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="confirmAnonymize()"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-amber-500 px-3.5 py-2 text-xs font-semibold text-white hover:bg-amber-600 transition shadow-sm"
                    >
                        <span>Anonymiser valgte ({{ count($selected) }})</span>
                    </button>

                    <button
                        type="button"
                        wire:click="confirmForceDelete()"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white hover:bg-rose-700 transition shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Slet permanent ({{ count($selected) }})</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-100/70 text-xs uppercase text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        @if($tab === 'expired')
                            <th class="p-3 w-10 text-center">
                                <input 
                                    type="checkbox" 
                                    wire:click="toggleSelectAll({{ json_encode($sager->pluck('id')->toArray()) }})"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                >
                            </th>
                        @endif
                        <th class="p-3">Sagsnr</th>
                        <th class="p-3">Afsluttet dato</th>
                        <th class="p-3">Debitor</th>
                        <th class="p-3">GDPR Status</th>
                        <th class="p-3 text-right">Handlinger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sager as $sag)
                        <tr class="hover:bg-slate-50/80 transition">
                            @if($tab === 'expired')
                                <td class="p-3 text-center">
                                    <input 
                                        type="checkbox" 
                                        wire:click="toggleSelect({{ $sag->id }})"
                                        @checked(in_array($sag->id, $selected))
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    >
                                </td>
                            @endif
                            <td class="p-3 font-semibold text-slate-900">
                                {{ $sag->sagsnr ?? $sag->id }}
                            </td>
                            <td class="p-3">
                                {{ $sag->afsluttet?->format('d-m-Y') ?? '-' }}
                            </td>
                            <td class="p-3">
                                {{ $sag->sagerdebitor->first()?->navn ?? '-' }}
                            </td>
                            <td class="p-3">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-{{ $sag->gdpr_status['color'] }}-50 text-{{ $sag->gdpr_status['color'] }}-700 ring-1 ring-inset ring-{{ $sag->gdpr_status['color'] }}-600/20">
                                    {{ $sag->gdpr_status['label'] }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                @if($tab === 'expired')
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- ANONYMISER --}}
                                        <button
                                            type="button"
                                            wire:click="confirmAnonymize({{ $sag->id }})"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 transition"
                                            title="Anonymiser personoplysninger"
                                        >
                                            Anonymiser
                                        </button>

                                        {{-- PERMANENT SLET --}}
                                        <button
                                            type="button"
                                            wire:click="confirmForceDelete({{ $sag->id }})"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 transition inline-flex items-center gap-1"
                                            title="Slet sagen og alt relateret permanent"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            <span>Slet</span>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">Under overvågning</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Ingen sager fundet i denne kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sager->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $sager->links() }}
            </div>
        @endif
    </div>

    {{-- BEKRÆFTELSES-MODAL --}}
    @if($confirming)
        <div class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl space-y-4 border border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">
                    {{ $actionType === 'anonymize' ? 'Bekræft GDPR Anonymisering' : 'Bekræft Permanent Sletning' }}
                </h3>
                
                <p class="text-sm text-slate-600">
                    @if($singleId)
                        Du er ved at {{ $actionType === 'anonymize' ? 'anonymisere' : 'permanent slette' }} <strong class="text-slate-900">sag #{{ $singleId }}</strong>.
                    @else
                        Du er ved at {{ $actionType === 'anonymize' ? 'anonymisere' : 'permanent slette' }} <strong class="text-slate-900">{{ count($selected) }} valgte sager</strong>.
                    @endif

                    @if($actionType === 'anonymize')
                        Sagens følsomme personoplysninger fjernes, men selve sagsnummeret og økonomiske historik bevares.
                    @else
                        <span class="text-rose-600 font-semibold">ADVARSEL:</span> Sagen og alle relaterede filer, dialoger og pivot-rækker slettes fuldstændigt fra databasen. Denne handling kan IKKE fortrydes!
                    @endif
                </p>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="cancel" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Annullér
                    </button>
                    <button 
                        type="button" 
                        wire:click="executeAction" 
                        class="px-4 py-2 text-xs font-semibold text-white rounded-xl transition shadow-sm {{ $actionType === 'anonymize' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-rose-600 hover:bg-rose-700' }}"
                    >
                        Bekræft {{ $actionType === 'anonymize' ? 'Anonymisering' : 'Permanent Sletning' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>