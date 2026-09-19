<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER & GLOBALE STATISTIKKER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-bold uppercase tracking-wider">Globalt Arkiv</span>
                <span class="text-xs text-slate-400">·</span>
                <span class="text-xs text-slate-500 font-medium">{{ number_format($totalDokumenterCount, 0, ',', '.') }} filer i alt</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 mt-1">
                Fugleperspektiv: Dokumenter & Sagshierarki
            </h1>
        </div>

        {{-- HURTIG STAT-BADGE --}}
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-600 flex items-center gap-2">
                <span>💾</span> Samlet forbrug: <strong class="text-slate-900">{{ round($totalStorageSize / 1024 / 1024, 1) }} MB</strong>
            </div>
        </div>
    </div>

    {{-- REAKTIV SØGEBAR & FILTRE (LIVEWIRE) --}}
    <div class="bg-white rounded-3xl border border-slate-200/80 p-4 shadow-xs flex flex-col sm:flex-row gap-3 items-center justify-between">
        <div class="flex items-center gap-3 w-full sm:w-1/2 pl-2">
            <span class="text-slate-400">🔍</span>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Søg på tværs af alle filer og sagsnumre..." class="w-full text-xs text-slate-800 bg-transparent outline-none border-0 focus:ring-0">
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <select wire:model.live="filterType" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none">
                <option value="">Alle filtyper</option>
                <option value="pdf">PDF dokumenter</option>
                <option value="xlsx">Excel ark</option>
                <option value="jpg">Billeder (JPG/PNG)</option>
            </select>

            @if($search || $filterType)
                <button type="button" wire:click="$set('search', ''); $set('filterType', '');" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition cursor-pointer">
                    Nulstil
                </button>
            @endif
        </div>
    </div>

    {{-- TRÆSTRUKTUR / MAPPE-OVERBLIK (BIRD'S EYE VIEW) --}}
    <div class="space-y-4">
        @if($sager->isEmpty())
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center space-y-3 shadow-xs">
                <div class="mx-auto w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-400 text-xl">📂</div>
                <h4 class="text-sm font-bold text-slate-800">Ingen sager eller filer matcher din søgning</h4>
                <p class="text-xs text-slate-500">Prøv at ændre dine søgekriterier.</p>
            </div>
        @else
            @foreach($sager as $sag)
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden transition-all hover:border-slate-300" 
                     x-data="{ open: true }">
                    
                    {{-- MAPPE-HEADER (SAG) --}}
                    <div @click="open = !open" class="p-4 sm:p-5 bg-slate-50/70 hover:bg-slate-100/70 transition flex items-center justify-between cursor-pointer select-none">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-inner font-bold shrink-0">
                                📁
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-xs font-bold text-slate-900">
                                        Sag #{{ $sag->sagsnr ?? $sag->id }}
                                    </h3>
                                    <span class="px-2 py-0.5 bg-indigo-100/80 text-indigo-800 rounded-md text-[10px] font-bold">
                                        {{ $sag->dokumenter->count() }} filer
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Oprettet: {{ $sag->created_at ? $sag->created_at->format('d/m/Y') : 'Ukendt' }} 
                                    @if($sag->kreditor && $sag->kreditor->isNotEmpty()) 
                                        · Kreditor: <span class="font-semibold text-slate-700">{{ $sag->kreditor->first()->navn ?? 'Ikke angivet' }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('sager.dokumenter.index', $sag->id) }}" @click.stop class="px-3.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition shadow-xs">
                                Gå til sag ➔
                            </a>
                            <span class="text-slate-400 transition-transform duration-200 w-5 h-5 flex items-center justify-center rounded-lg hover:bg-slate-200/60" :class="open ? 'rotate-180' : ''">▼</span>
                        </div>
                    </div>

                    {{-- FIL-LISTE UNDER SAGEN (TRÆETS GRENE) --}}
                    <div x-show="open" x-transition class="divide-y divide-slate-100 bg-white border-t border-slate-100">
                        @foreach($sag->dokumenter as $dok)
                            @php
                                $ext = strtolower(pathinfo($dok->file_name, PATHINFO_EXTENSION));
                                $icon = match($ext) {
                                    'pdf' => '📄',
                                    'doc', 'docx' => '📝',
                                    'xls', 'xlsx', 'csv' => '📊',
                                    'jpg', 'jpeg', 'png', 'webp' => '🖼️',
                                    default => '📎'
                                };
                                $sizeKb = round(($dok->file_size ?? 0) / 1024, 1);
                                $sizeFormatted = $sizeKb > 1024 ? round($sizeKb / 1024, 1) . ' MB' : $sizeKb . ' KB';
                            @endphp

                            <div class="py-3 px-6 pl-14 hover:bg-slate-50/60 transition flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="text-base shrink-0 p-2 bg-slate-50 rounded-xl border border-slate-100 shadow-2xs">{{ $icon }}</span>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-800 truncate" title="{{ $dok->file_name }}">
                                            {{ $dok->file_name }}
                                        </h4>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $sizeFormatted }} · Uploadet {{ $dok->created_at ? $dok->created_at->format('d/m/Y H:i') : '' }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('sager.dokumenter.download', [$sag->id, $dok->id]) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                        <span>⬇️</span> Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            @endforeach

            {{-- Livewire Pagination --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-4 shadow-xs mt-6">
                {{ $sager->links() }}
            </div>
        @endif
    </div>

</div>