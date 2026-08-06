<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- HEADER & SØGNING --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 flex items-center gap-3">
                <span class="p-2 rounded-2xl bg-indigo-50 text-indigo-600">🩺</span>
                <span>Doctor Norton 3.0</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">Målrettet diagnose og reparation af sagsrelationer og oprydning</p>
        </div>

        <div class="relative w-full md:w-80">
            <input
                type="search"
                wire:model.live.debounce.400ms="search"
                placeholder="Søg sagsnr, debitor, kreditor..."
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 pl-10 text-sm text-slate-800 shadow-sm transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
            >
            <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- DIAGNOSE KRITERIER --}}
    <div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm mb-6">
        <button 
            type="button" 
            @click="open = !open" 
            class="flex items-center justify-between w-full text-left font-semibold text-slate-800 text-sm"
        >
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Hvad betyder tilstande og kategorier i Doctor Norton?</span>
            </div>
            <span class="text-xs text-indigo-600 font-bold" x-text="open ? 'Skjul forklaring ▲' : 'Vis forklaring ▼'"></span>
        </button>

        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
            <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 space-y-1">
                <div class="font-bold text-rose-900 flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                    <span>🔴 Kritiske Fejl</span>
                </div>
                <p class="text-rose-700 leading-relaxed">Sagen mangler enten et sagsnummer, en tilknyttet debitor eller en kreditor.</p>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 space-y-1">
                <div class="font-bold text-amber-900 flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                    <span>👤 Mangler Behandler</span>
                </div>
                <p class="text-amber-700 leading-relaxed">Sagen mangler en tildelt sagsbehandler eller konsulent.</p>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 border border-amber-100 space-y-1">
                <div class="font-bold text-amber-900 flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                    <span>🏷️ Status Fejl</span>
                </div>
                <p class="text-amber-700 leading-relaxed">Sagen har ingen aktiv status eller har ved en fejl flere samtidige statusser.</p>
            </div>

            <div class="p-3 rounded-xl bg-purple-50 border border-purple-100 space-y-1">
                <div class="font-bold text-purple-900 flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-purple-500"></span>
                    <span>👻 Orphan Debitorer</span>
                </div>
                <p class="text-purple-700 leading-relaxed">Debitorer oprettet i databasen uden tilknyttede sager.</p>
            </div>

            <div class="p-3 rounded-xl bg-orange-50 border border-orange-100 space-y-1">
                <div class="font-bold text-orange-900 flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-orange-500"></span>
                    <span>🧬 Identiske Sagsnumre</span>
                </div>
                <p class="text-orange-700 leading-relaxed">To eller flere sager i databasen, der ved en fejl deler det præcis samme sagsnummer.</p>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100 space-y-1">
                <div class="font-bold text-emerald-900 flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                    <span>🟢 Sunde Sager</span>
                </div>
                <p class="text-emerald-700 leading-relaxed">Sagen har 100% komplette relationer, korrekte statusser og valide data.</p>
            </div>
        </div>
    </div>

    {{-- SEKTION 1: MÅLRETTET AUDIT FANER --}}
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Målrettet Relations-Audit</h2>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <button
                type="button"
                wire:click="setTab('critical')"
                class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ $activeTab === 'critical' ? 'border-rose-500 bg-rose-50 ring-2 ring-rose-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-rose-800 uppercase">Kritiske</span>
                    <span class="rounded-full bg-rose-200 px-2 py-0.5 text-xs font-bold text-rose-900">{{ $stats['critical'] }}</span>
                </div>
                <span class="text-sm font-bold text-slate-800 mt-3">🔴 Struktur-fejl</span>
            </button>

            <button
                type="button"
                wire:click="setTab('missing_handler')"
                class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ $activeTab === 'missing_handler' ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-amber-800 uppercase">Behandlere</span>
                    <span class="rounded-full bg-amber-200 px-2 py-0.5 text-xs font-bold text-amber-900">{{ $stats['missing_handler'] }}</span>
                </div>
                <span class="text-sm font-bold text-slate-800 mt-3">👤 Mangler Behandler</span>
            </button>

            <button
                type="button"
                wire:click="setTab('missing_status')"
                class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ $activeTab === 'missing_status' ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-amber-800 uppercase">Status</span>
                    <span class="rounded-full bg-amber-200 px-2 py-0.5 text-xs font-bold text-amber-900">{{ $stats['missing_status'] }}</span>
                </div>
                <span class="text-sm font-bold text-slate-800 mt-3">🏷️ Status-fejl</span>
            </button>

            <button
                type="button"
                wire:click="setTab('invalid_closure')"
                class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ $activeTab === 'invalid_closure' ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-amber-800 uppercase">Afslutning</span>
                    <span class="rounded-full bg-amber-200 px-2 py-0.5 text-xs font-bold text-amber-900">{{ $stats['invalid_closure'] }}</span>
                </div>
                <span class="text-sm font-bold text-slate-800 mt-3">🏁 Mangler Årsag</span>
            </button>

            <button
                type="button"
                wire:click="setTab('orphans')"
                class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ $activeTab === 'orphans' ? 'border-purple-500 bg-purple-50 ring-2 ring-purple-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-purple-800 uppercase">Orphans</span>
                    <span class="rounded-full bg-purple-200 px-2 py-0.5 text-xs font-bold text-purple-900">{{ $orphanCount }}</span>
                </div>
                <span class="text-sm font-bold text-slate-800 mt-3">👻 Debitorer</span>
            </button>

            <button
                type="button"
                wire:click="setTab('duplicates')"
                class="p-4 rounded-2xl border transition text-left flex flex-col justify-between {{ $activeTab === 'duplicates' ? 'border-orange-500 bg-orange-50 ring-2 ring-orange-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
            >
                <div class="flex items-center justify-between w-full">
                    <span class="text-[11px] font-bold text-orange-800 uppercase">Duplikater</span>
                    <span class="rounded-full bg-orange-200 px-2 py-0.5 text-xs font-bold text-orange-900">{{ $duplicateCount }}</span>
                </div>
                <span class="text-sm font-bold text-slate-800 mt-3">🧬 Sagsnumre</span>
            </button>
        </div>
    </div>

    {{-- SEKTION 2: SEPARAT OVERBLIK OVER SUNDE SAGER --}}
    <div class="pt-4 border-t border-slate-200">
        <button
            type="button"
            wire:click="setTab('healthy')"
            class="w-full p-4 rounded-2xl border transition text-left flex items-center justify-between {{ $activeTab === 'healthy' ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white hover:bg-slate-50' }}"
        >
            <div class="flex items-center gap-3">
                <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                <div>
                    <span class="font-bold text-slate-900 text-sm">🟢 Sunde Sager (Fuldt Overholdt Portefølje)</span>
                    <p class="text-xs text-slate-500">Sager med 100% komplette relationer, korrekte statusser og valide sagsdata</p>
                </div>
            </div>

            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                {{ $stats['healthy'] }} sager
            </span>
        </button>
    </div>

    {{-- FANE 1: ORPHANS --}}
    @if($activeTab === 'orphans')
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="font-semibold text-slate-800 text-sm">Debitorer uden tilknyttede sager</h2>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($results as $debitor)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">{{ $debitor->navn ?: 'Ukendt debitor' }}</div>
                            <div class="text-xs text-slate-500">ID: #{{ $debitor->id }} {{ $debitor->email ? '• '.$debitor->email : '' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">Ingen orphan debitorer fundet.</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- FANE 2: DUPLIKATER --}}
    @if($activeTab === 'duplicates')
        <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="font-semibold text-slate-800 text-sm">Sager der deler samme sagsnummer</h2>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($results as $sag)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Sagsnr: {{ $sag->sagsnr }} <span class="text-xs text-slate-400 font-normal">(Sag #{{ $sag->id }})</span></div>
                            <div class="text-xs text-slate-500">Debitor: {{ $sag->sagerdebitor->first()?->navn ?? 'Ingen tilknyttet' }}</div>
                        </div>
                        <a href="{{ route('sager.edit', $sag->id) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                            Åbn sag
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">Ingen duplikerede sagsnumre fundet.</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- FANE 3, 4, 5 & 6: SAGER PÅ TVÆRS AF KATEGORIER --}}
    @if(in_array($activeTab, ['critical', 'missing_handler', 'missing_status', 'invalid_closure', 'healthy']))
        <div class="space-y-4">
            @forelse($results as $result)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-900 text-base">Sag #{{ $result->sag->id }}</span>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-mono font-semibold bg-slate-100 text-slate-700">
                                {{ $result->sag->sagsnr ?? 'Uden sagsnr' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-500">Health Score: <strong>{{ $result->score }}%</strong></span>
                            <button
                                type="button"
                                wire:click="repairSag({{ $result->sag->id }})"
                                class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition shadow-sm"
                            >
                                🔧 Konsolider
                            </button>
                        </div>
                    </div>

                    @if(count($result->issues) > 0)
                        <div class="space-y-1.5">
                            @foreach($result->issues as $issue)
                                <div class="text-xs font-medium px-3 py-2 rounded-xl flex items-center justify-between {{ $issue->type === 'critical' ? 'bg-rose-50 text-rose-800 border border-rose-100' : 'bg-amber-50 text-amber-800 border border-amber-100' }}">
                                    <span>{{ $issue->message }}</span>
                                    <span class="uppercase font-bold text-[10px]">{{ $issue->type }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-xs text-emerald-700 font-medium">
                            ✔ Sagens relationer, behandlere og statusser er fuldstændigt intakte.
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center text-slate-500 text-sm">
                    Ingen sager fundet i denne kategori.
                </div>
            @endforelse
        </div>
    @endif

    {{-- PAGINERING --}}
    @if($results->hasPages())
        <div class="pt-4 border-t border-slate-200">
            {{ $results->links() }}
        </div>
    @endif

    {{-- ANIMATED REPAIR PROGRESS MODAL --}}
    @if($showRepairModal)
        <div 
            x-data="{
                steps: @js($activeRepairLog),
                currentStep: 0,
                progress: 0,
                isCompleted: false,

                init() {
                    this.runAnimation();
                },

                runAnimation() {
                    let totalSteps = this.steps.length;
                    let interval = setInterval(() => {
                        if (this.currentStep < totalSteps) {
                            this.currentStep++;
                            this.progress = Math.round((this.currentStep / totalSteps) * 100);
                        } else {
                            clearInterval(interval);
                            this.isCompleted = true;
                        }
                    }, 450); // 450ms pr. trin for en naturlig følelse
                }
            }"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
        >
            {{-- BACKDROP --}}
            <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm transition-opacity" wire:click="closeRepairModal"></div>

            {{-- MODAL DIALOG --}}
            <div class="relative z-10 transform overflow-hidden rounded-3xl bg-white p-6 text-left shadow-2xl transition-all w-full max-w-lg border border-slate-100">
                
                {{-- MODAL HEADER --}}
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-2xl bg-indigo-50 text-indigo-600">
                            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Konsoliderer Sag #{{ $repairedSagId }}</h3>
                            <p class="text-xs text-slate-500" x-text="isCompleted ? 'Konsolidering gennemført' : 'Analyserer og udbedrer sagsdata i realtid...'"></p>
                        </div>
                    </div>

                    <button type="button" wire:click="closeRepairModal" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- PROGRESS BAR --}}
                <div class="py-4 space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold text-slate-600">
                        <span x-text="isCompleted ? 'Udført!' : 'Arbejder på sagen...'"></span>
                        <span x-text="progress + '%'"></span>
                    </div>

                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        <div 
                            class="h-full bg-indigo-600 transition-all duration-300 ease-out rounded-full"
                            :style="'width: ' + progress + '%'"
                        ></div>
                    </div>
                </div>

                {{-- STEP BY STEP ANIMATED LIST --}}
                <div class="py-2 space-y-2 max-h-80 overflow-y-auto">
                    <template x-for="(item, index) in steps" :key="index">
                        <div 
                            x-show="index < currentStep"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            class="p-3 rounded-2xl border text-xs flex items-start justify-between gap-3"
                            :class="item.status === 'repaired' ? 'bg-amber-50/60 border-amber-200/60' : 'bg-slate-50/80 border-slate-100'"
                        >
                            <div class="flex items-start gap-2.5">
                                {{-- STATUS IKON --}}
                                <div class="mt-0.5">
                                    <template x-if="item.status === 'repaired'">
                                        <span class="flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-white font-bold text-[10px]">✓</span>
                                    </template>
                                    <template x-if="item.status === 'ok'">
                                        <span class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-white font-bold text-[10px]">✓</span>
                                    </template>
                                </div>

                                <div class="space-y-0.5">
                                    <h4 class="font-bold text-slate-800" x-text="item.label"></h4>
                                    <p class="text-slate-500 text-[11px]" x-text="item.detail"></p>
                                </div>
                            </div>

                            {{-- STATUS BADGE --}}
                            <span 
                                class="px-2 py-0.5 rounded-md font-bold text-[10px] whitespace-nowrap uppercase"
                                :class="item.status === 'repaired' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
                                x-text="item.status === 'repaired' ? 'Korrigeret' : 'OK'"
                            ></span>
                        </div>
                    </template>
                </div>

                {{-- FOOTER / AVSLUTNINGSKNAP --}}
                <div class="pt-4 border-t border-slate-100 flex justify-end">
                    <button
                        type="button"
                        wire:click="closeRepairModal"
                        :disabled="!isCompleted"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold transition shadow-sm"
                        :class="isCompleted ? 'bg-slate-900 hover:bg-slate-800 text-white cursor-pointer' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                    >
                        <span x-text="isCompleted ? 'Færdig / Luk rapport' : 'Konsoliderer...'"></span>
                    </button>
                </div>>

            </div>
        </div>
    @endif