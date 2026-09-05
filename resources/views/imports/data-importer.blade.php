<div class="max-w-5xl mx-auto space-y-6" x-data="{ showFieldMapping: false }">

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Importér data fra tidligere system
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Overfør sager, kreditorer eller debitorer ved at parre kolonner, genbruge skabeloner og køre system-import.
            </p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium">
            {!! session('success') !!}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-medium">
            {!! session('error') !!}
        </div>
    @endif

    {{-- 🟢 STEP 1: FIL-UPLOAD, MAPPING, SKABELONER OG FORHÅNDSVISNING --}}
    @if ($step == 1)
        <div class="space-y-6">

            {{-- 📁 1. VÆLG FIL / UPLOAD --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">1. Vælg CSV-fil til import</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Upload din CSV-fil for at hente kolonner og starte felt-parringen.</p>
                </div>

                <div class="flex items-center gap-4">
                    <input type="file" wire:model="file" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"/>
                </div>
                @error('file') <span class="text-rose-600 text-[11px] block">{{ $message }}</span> @enderror
            </div>

            {{-- ⚠️ UPARREDE KOLONNER FRA CSV-FILEN & TEKNISK FORKLARING --}}
            @php
                $mappedSourceColumns = array_values(array_filter($mapping));
                $unmappedColumns = array_diff($sourceColumns ?? [], $mappedSourceColumns);
            @endphp

            @if(!empty($sourceColumns))
                <div class="bg-amber-50/50 rounded-3xl border border-amber-200/80 p-6 shadow-xs space-y-4" x-data="{ showExplanation: false }">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-base">💡</span>
                            <div>
                                <h3 class="text-sm font-bold text-amber-900">Uparrede kolonner fra CSV-filen ({{ count($unmappedColumns) }})</h3>
                                <p class="text-xs text-amber-700/80 mt-0.5">Klik på en kolonne for at finde den i forhåndsvisningen, eller se teknisk forklaring nedenfor.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">
                                {{ count($unmappedColumns) }} ubrugte
                            </span>
                            <button type="button" @click="showExplanation = !showExplanation" class="px-3 py-1 bg-amber-200 hover:bg-amber-300 text-amber-950 text-[11px] font-bold rounded-xl transition cursor-pointer">
                                <span x-text="showExplanation ? 'Skjul forklaring 🔼' : 'Hvorfor er disse uparrede? 🔽'"></span>
                            </button>
                        </div>
                    </div>

                    @if(count($unmappedColumns) > 0)
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            @foreach($unmappedColumns as $col)
                                @php
                                    $colSlug = Str::slug($col);
                                @endphp
                                <button 
                                    type="button" 
                                    onclick="const el = document.getElementById('csv-col-{{ $colSlug }}'); if(el) { el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' }); el.classList.add('bg-amber-200', 'text-amber-950'); setTimeout(() => el.classList.remove('bg-amber-200', 'text-amber-950'), 2000); }"
                                    class="inline-flex items-center px-2.5 py-1 rounded-xl bg-white border border-amber-300 text-amber-900 hover:border-indigo-500 hover:text-indigo-600 text-[11px] font-mono shadow-xs transition cursor-pointer group"
                                >
                                    <span>{{ $col }}</span>
                                    <span class="ml-1 text-[9px] text-slate-400 group-hover:text-indigo-500">👁️</span>
                                </button>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-emerald-700 font-medium italic pt-1">
                            Flot! Alle kolonner fra filen er nu blevet parret.
                        </p>
                    @endif

                    {{-- 🟢 REDIGERBAR TEXTAREA MED FORKLARING OM PIVOT-TABELLER OG UDGÅEDE FELTER --}}
                    <div x-show="showExplanation" x-transition class="pt-4 border-t border-amber-200/60 space-y-2">
                        <label class="block text-xs font-bold text-amber-900">Teknisk forklaring på de uparrede og omstrukturerede felter (redigerbar):</label>
                        <textarea rows="10" class="w-full bg-white border border-amber-300 rounded-2xl p-3.5 text-xs font-mono text-slate-700 outline-none focus:border-indigo-500 resize-y leading-relaxed">1. HISTORIK, BOGHOLDERI & KLIENTINFO:
Disse data gemmes nu struktureret i `Dialogs`-modellen (`dialogs`-tabellen) med specifikke typer ('bogholderi', 'historik', 'klientinformation'), hvor adgangen styres rollebaseret mellem konsulenter og kreditorer.

2. AKTIVT & LUKKET (Sagstatus):
De gamle flag (-1/0 værdier til aktiv/lukket knapper) er erstattet af det nye dynamiske `Status`-model og `sager_status` pivot-system.

3. FULDMAGT (Firma-alias for kreditor 15):
Fungerede i det gamle system som et visuelt firma-alias, der blev udfyldt via JS, hvis kreditorID var '15' (`kode15firmareplace()`). Håndteres nu dynamisk i applikationslogikken.

4. PIVOT-TABELLER OG RELATEREDE FELTER:
Visse felter håndteres nu via avancerede relationer (f.eks. `sager_konsulent`, `sager_sagsbehandler` m.fl.), hvor det gamle system gemte flade ID'er.

5. UBRUGTE / UDGÅEDE FELTER:
- 'restance_info': Gammelt telefonlog-felt, som ikke længere er i brug.
- 'mdlydelse' & 'lejemaal': Udgået i den nye databasestruktur.
- 'afdragsordning' & 'boligkode': Ikke en del af den aktive kernemodel.</textarea>
                    </div>
                </div>
            @endif

            {{-- 📊 VISUEL PARRINGSOVERSIGT (FORSVINDER NÅR MAN KLIKKER PÅ REDIGÉR MAPPING) --}}
            <div x-show="!showFieldMapping" x-transition class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <details class="group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer select-none bg-slate-50/50 hover:bg-slate-100/60 transition">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base shadow-inner">
                                🔗
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Visuel parringsoversigt (Inkl. række-notater)</h3>
                                <p class="text-xs text-slate-500">Klik for at udfolde og se kolonner samt specifikke notater pr. felt</p>
                            </div>
                        </div>
                        <span class="text-slate-400 group-open:rotate-180 transition-transform duration-200">▼</span>
                    </summary>

                    <div class="p-6 border-t border-slate-100 bg-white space-y-6">
                        
                        {{-- 🎛️ KNAP TIL AT ÅBNE FELT-MAPPING OG SKJULE DENNE OVERSIGT --}}
                        <div class="flex items-center justify-between bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4">
                            <div>
                                <h4 class="font-bold text-indigo-950 text-xs">Vil du ændre felt-parringen?</h4>
                                <p class="text-[11px] text-indigo-700">Skift til fuld redigering for at koble gamle kolonner til de nye databasefelter.</p>
                            </div>
                            <button 
                                type="button" 
                                @click="showFieldMapping = true" 
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs whitespace-nowrap"
                            >
                                Redigér mapping af felter 🛠️
                            </button>
                        </div>

                        <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-700 border-b border-slate-200 font-bold">
                                    <tr>
                                        <th class="p-3.5">Gammelt felt / Kildekolonne</th>
                                        <th class="p-3.5 text-center w-12">Match</th>
                                        <th class="p-3.5">Nyt databasefelt (Target)</th>
                                        <th class="p-3.5">Notat / Bemærkning pr. felt</th>
                                        <th class="p-3.5 text-right">Type</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    @foreach($targetFields as $targetKey => $targetLabel)
                                        @php
                                            $sourceCol = $mapping[$targetKey] ?? null;
                                            $isMapped = !empty($sourceCol);
                                            $isExactMatch = strtolower($sourceCol) === strtolower($targetKey);
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition">
                                            <td class="p-3.5 font-mono text-[11px]">
                                                @if($isMapped)
                                                    <span class="px-2 py-1 rounded-lg border font-bold {{ $isExactMatch ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-indigo-50 text-indigo-700 border-indigo-100' }}">
                                                        {{ $sourceCol }} {{ $isExactMatch ? '🎯 (1:1)' : '' }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic">Ikke valgt</span>
                                                @endif
                                            </td>
                                            <td class="p-3.5 text-center font-bold">
                                                @if($isMapped) <span class="text-emerald-600">✔</span> @else <span class="text-slate-300">·</span> @endif
                                            </td>
                                            <td class="p-3.5">
                                                <div class="font-bold text-slate-800">{{ $targetLabel }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $targetKey }}</div>
                                            </td>
                                            <td class="p-3.5">
                                                <input type="text" placeholder="Tilføj evt. notat..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 text-[11px] text-slate-700 outline-none focus:border-indigo-500 focus:bg-white transition" />
                                            </td>
                                            <td class="p-3.5 text-right">
                                                @if($isExactMatch)
                                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">Identisk Match</span>
                                                @elseif(str_ends_with($targetKey, '_id'))
                                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-purple-50 text-purple-700 border border-purple-200">Relation</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-slate-100 text-slate-600 border border-slate-200">Standard</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </details>
            </div>

            {{-- ⚙️ GEMTE SKABELONER --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <details class="group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer select-none bg-slate-50/50 hover:bg-slate-100/60 transition">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-base shadow-inner">⚙️</div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Gemte Skabeloner ({{ count($templates) }})</h3>
                                <p class="text-xs text-slate-500">Brug, redigér, slet eller eksporter/importér dine skabeloner</p>
                            </div>
                        </div>
                        <span class="text-slate-400 group-open:rotate-180 transition-transform duration-200">▼</span>
                    </summary>

                    <div class="p-6 border-t border-slate-100 space-y-6 bg-white">
                        @if(!empty($templates) && count($templates) > 0)
                            @foreach($templates as $tpl)
                                <div x-data="{ editing: false, tempName: '{{ $tpl->name }}' }" class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                                    <div class="flex items-center justify-between gap-4 flex-wrap">
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                                <span>📋</span><span>{{ $tpl->name }}</span>
                                            </h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">Parrede felter: <strong class="text-slate-700 font-mono">{{ count($tpl->mapping ?? []) }}</strong></p>
                                        </div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <button type="button" wire:click="$set('selectedTemplateId', {{ $tpl->id }}); $wire.loadTemplate()" class="px-3 py-1.5 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition cursor-pointer">📥 Brug</button>
                                            <button type="button" wire:click="exportTemplate({{ $tpl->id }})" class="px-3 py-1.5 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition cursor-pointer">📤 Eksportér</button>
                                            <button type="button" wire:click="deleteTemplate({{ $tpl->id }})" wire:confirm="Er du sikker?" class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition cursor-pointer">🗑️ Slet</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-slate-400 text-center py-4">Ingen gemte skabeloner endnu.</p>
                        @endif
                    </div>
                </details>
            </div>

            {{-- 👁️ FORHÅNDSVISNING AF FIL --}}
            @if(!empty($previewRows))
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Forhåndsvisning af fil (Første 3 rækker)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tjek at kolonnerne matcher korrekt.</p>
                        </div>
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-bold">{{ count($sourceColumns) }} kolonner fundet</span>
                    </div>

                    <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200 font-bold">
                                <tr>
                                    @foreach($sourceColumns as $col)
                                        <th id="csv-col-{{ Str::slug($col) }}" class="p-3 truncate max-w-[150px] transition-colors duration-300">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach($previewRows as $row)
                                    <tr class="hover:bg-slate-50/50">
                                        @foreach($sourceColumns as $index => $col)
                                            <td class="p-3 truncate max-w-[150px] font-mono text-[11px]">{{ $row[$index] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- KOBLE KOLONNER MED PIVOT- OG RELATIONS-MARKERING (VISES KUN NÅR MAN KLIKKER PÅ REDIGÉR MAPPING) --}}
            <div x-show="showFieldMapping" x-transition class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Koble gamle kolonner til nye databasefelter</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Vælg hvilken kolonne fra filen der svarer til systemfelterne. Felter markeret med <span class="text-purple-700 font-bold">Relation (Pivot)</span> slåes automatisk op via ID.</p>
                    </div>
                    <button type="button" @click="showFieldMapping = false" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition cursor-pointer">✕ Luk & Tilbage</button>
                </div>

                <div class="space-y-3">
                    @foreach($targetFields as $targetKey => $targetLabel)
                        @php
                            $isPivotRelation = in_array($targetKey, [
                                'kreditor_id', 'debitor_id', 'sagsbehandler_id', 'konsulent_id', 
                                'token_id', 'status_id', 'ktr_id', 'afslutning_id', 'udlaeg_id', 'bemaerkning_id'
                            ]);
                        @endphp

                        <div id="map-field-{{ $targetKey }}" class="flex items-center justify-between bg-slate-50/50 p-3.5 rounded-2xl border {{ $isPivotRelation ? 'border-purple-200 bg-purple-50/20' : 'border-slate-200/80' }} scroll-mt-6">
                            <span class="text-xs font-bold text-slate-800 w-1/3">
                                {{ $targetLabel }} 
                                <span class="text-[10px] text-slate-400 font-mono font-normal block mt-0.5">DB: {{ $targetKey }}</span>
                                
                                @if($isPivotRelation)
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 text-[9px] font-bold uppercase tracking-wider rounded-md bg-purple-100 text-purple-700 border border-purple-200">
                                        🔗 Relation (Pivot-tabel)
                                    </span>
                                @endif
                            </span>
                            
                            <span class="text-slate-400">&rarr;</span>
                            
                            <div class="w-1/2 space-y-1">
                                <select wire:model="mapping.{{ $targetKey }}" class="w-full bg-white border border-slate-200 rounded-xl p-2 text-xs text-slate-800 outline-none focus:border-indigo-500">
                                    <option value="">-- Vælg kolonne fra fil --</option>
                                    @foreach($sourceColumns as $source)
                                        <option value="{{ $source }}">{{ $source }}</option>
                                    @endforeach
                                </select>
                                
                                @if($isPivotRelation)
                                    <p class="text-[10px] text-purple-600 italic px-1">
                                        * Systemet slår ID op og opretter automatisk relationen i pivot-tabellen.
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- GEM SKABELON & START IMPORT (KUN ÉN KLAR START-KNAP) --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="w-full sm:flex-1">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Gem denne mapping som en ny skabelon</label>
                    <input type="text" wire:model="templateName" placeholder="F.eks. Standard Format" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs outline-none focus:border-indigo-500 bg-white" />
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button type="button" wire:click="saveTemplate" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                        Gem skabelon
                    </button>
                    
                    <button type="button" wire:click="executeImport" wire:loading.attr="disabled" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                        <span wire:loading.remove>Start Importér &rarr;</span>
                        <span wire:loading>Importerer...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 3: Færdig --}}
    @if ($step == 3)
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-xs text-center space-y-4">
            <div class="mx-auto w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-3xl shadow-inner">🎉</div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Import fuldført!</h3>
                <p class="text-xs text-slate-500 mt-1">Dine data er nu succesfuldt overført til systemet.</p>
            </div>
            <div class="pt-4">
                <button type="button" wire:click="$set('step', 1)" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                    Importer mere data
                </button>
            </div>
        </div>
    @endif

    {{-- EKSTRA SEKTION: LYNFAST SYSTEM-IMPORT & BAGGRUNDSIMPORT (SQL) --}}
    <div class="space-y-6 pt-6 border-t border-slate-200">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4" 
             @if($isImportingDialogs) wire:poll.1s="checkDialogImportStatus" @endif>
            <div>
                <h3 class="text-xs font-bold text-slate-800 mb-1">Baggrundsimport af Dialoger & Tokens (Gigantisk SQL)</h3>
                <p class="text-[11px] text-slate-500">Kør importen af store SQL-filer uafhængigt af browser-timeouts.</p>
            </div>

            <div class="flex items-center gap-4 flex-wrap">
                <input type="text" wire:model="dialogFile" class="w-full max-w-xs rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none" @if($isImportingDialogs) disabled @endif placeholder="Dialog fil">
                <input type="text" wire:model="tokenFile" class="w-full max-w-xs rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none" @if($isImportingDialogs) disabled @endif placeholder="Token fil">
                <button type="button" @click="if(confirm('Start baggrundsimport?')) { $wire.startBackgroundDialogImport() }" @if($isImportingDialogs) disabled @endif class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition cursor-pointer">
                    Start Dialog-import 🚀
                </button>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
            <div>
                <h3 class="text-xs font-bold text-slate-800 mb-1">Alternativ: Lynfast system-import (Direkte SQL)</h3>
                <p class="text-[11px] text-slate-500">Kør komplet system-import direkte fra serverens <code>storage/</code> mappe.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Bruger SQL</label>
                    <input type="text" wire:model="userFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50/50 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Kreditor SQL</label>
                    <input type="text" wire:model="kreditorFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50/50 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Konsulent SQL</label>
                    <input type="text" wire:model="konsulentFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50/50 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Sagsbehandler SQL</label>
                    <input type="text" wire:model="sagsbehandlerFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50/50 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Debitor SQL</label>
                    <input type="text" wire:model="debitorFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50/50 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Sager SQL</label>
                    <input type="text" wire:model="sagerFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-slate-50/50 outline-none">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="button" wire:click="runSystemImport" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition cursor-pointer">
                    Start komplet system-import 🚀
                </button>
            </div>
        </div>
    </div>
</div>