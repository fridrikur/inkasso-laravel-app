<div class="max-w-5xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Importér data fra tidligere system
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Overfør sager, kreditorer eller debitorer ved at parre kolonner og genbruge skabeloner.
            </p>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-medium">
            {{ session('success') }}
        </div>
    @endif

{{-- ALTERNATIV: LYNFAST SYSTEM-IMPORT (DIREKTE SQL) --}}
    @if ($step == 1)
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs mt-6 space-y-4">
            <div>
                <h3 class="text-xs font-bold text-slate-800 mb-1">Alternativ: Lynfast system-import (Direkte SQL)</h3>
                <p class="text-[11px] text-slate-500">
                    Hvis dine SQL-filer ligger i <code>storage/</code> mappen på serveren, kan du køre en komplet og lynhurtig import af alle tabeller i den korrekte rækkefølge: <strong>1. Debitorer &rarr; 2. Kreditorer &rarr; 3. Sager</strong>.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Bruger SQL-fil</label>
                    <input type="text" wire:model="userFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Kreditor SQL-fil</label>
                    <input type="text" wire:model="kreditorFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Konsulent SQL-fil</label>
                    <input type="text" wire:model="konsulentFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Sagsbehandler SQL</label>
                    <input type="text" wire:model="sagsbehandlerFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Debitor SQL-fil</label>
                    <input type="text" wire:model="debitorFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-600 mb-1">Sager SQL-fil</label>
                    <input type="text" wire:model="sagerFile" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-800 bg-slate-50/50 outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end">
                
                <button 
                    type="button" 
                    wire:click="runSystemImport" 
                    wire:loading.attr="disabled" 
                    class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-2"
                >
                    <span wire:loading.remove>Start komplet system-import 🚀</span>
                    <span wire:loading>Kører database-import...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- STEP 2: Felt-mapping & Templates & Preview --}}
    @if ($step == 2)
        <div class="space-y-6">

            {{-- ⚠️ UPARREDE KOLONNER FRA CSV-FILEN (Kun når en skabelon er valgt) --}}
@php
    $mappedSourceColumns = array_values(array_filter($mapping));
    $unmappedColumns = array_diff($sourceColumns ?? [], $mappedSourceColumns);
@endphp

@if(!empty($sourceColumns) && !empty($selectedTemplateId))
    <div class="bg-amber-50/50 rounded-3xl border border-amber-200/80 p-6 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="text-base">💡</span>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">Uparrede kolonner fra CSV-filen ({{ count($unmappedColumns) }})</h3>
                    <p class="text-xs text-amber-700/80 mt-0.5">Klik på en kolonne for at finde og fremhæve den i CSV-forhåndsvisningen.</p>
                </div>
            </div>
            
            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-bold">
                {{ count($unmappedColumns) }} ubrugte
            </span>
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
                Flot! Alle kolonner fra filen er nu blevet parret i denne skabelon.
            </p>
        @endif
    </div>
@endif

            {{-- 📊 FLOT PARRINGSTABEL OVER AKTIVE MAPPNINGS (SKJULT SOM STANDARD) --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
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
                        <span class="text-slate-400 group-open:rotate-180 transition-transform duration-200">
                            ▼
                        </span>
                    </summary>

                    <div class="p-6 border-t border-slate-100 bg-white space-y-4">
                        <div class="flex items-center justify-between pb-2">
                            <p class="text-xs text-slate-500">Oversigt over felt-til-felt sammenkobling med tilhørende bemærkninger:</p>
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-[10px] font-bold">
                                {{ count(array_filter($mapping)) }} felter parret
                            </span>
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
                                                @if($isMapped)
                                                    <span class="text-emerald-600">✔</span>
                                                @else
                                                    <span class="text-slate-300">·</span>
                                                @endif
                                            </td>

                                            <td class="p-3.5">
                                                <div class="font-bold text-slate-800">{{ $targetLabel }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $targetKey }}</div>
                                            </td>

                                            <td class="p-3.5">
                                                <input 
                                                    type="text" 
                                                    placeholder="Tilføj evt. notat..." 
                                                    class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1 text-[11px] text-slate-700 outline-none focus:border-indigo-500 focus:bg-white transition"
                                                />
                                            </td>

                                            <td class="p-3.5 text-right">
                                                @if($isExactMatch)
                                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Identisk Match
                                                    </span>
                                                @elseif(str_ends_with($targetKey, '_id'))
                                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                                                        Relation
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                                        Standard
                                                    </span>
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

            {{-- GEMTE SKABELONER --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <details class="group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer select-none bg-slate-50/50 hover:bg-slate-100/60 transition">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-base shadow-inner">
                                ⚙️
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">Gemte Skabeloner ({{ count($templates) }})</h3>
                                <p class="text-xs text-slate-500">Brug, redigér, slet eller eksporter/importér dine skabeloner</p>
                            </div>
                        </div>
                        <span class="text-slate-400 group-open:rotate-180 transition-transform duration-200">
                            ▼
                        </span>
                    </summary>

                    <div class="p-6 border-t border-slate-100 space-y-6 bg-white">
                        
                        {{-- LOKAL IMPORT AF SKABELON JSON --}}
                        <div class="bg-slate-50 border border-dashed border-slate-300 rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-slate-800 text-xs">Importér skabelon fra JSON-fil</h4>
                                <p class="text-[11px] text-slate-500">Har du en eksporteret skabelon? Upload den her for at tilføje den.</p>
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <input type="file" wire:model="importTemplateFile" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"/>
                                <button type="button" wire:click="importTemplate" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer whitespace-nowrap">
                                    Importér JSON
                                </button>
                            </div>
                        </div>
                        @error('importTemplateFile') <span class="text-rose-600 text-[11px] block mt-1">{{ $message }}</span> @enderror

                        {{-- LISTE OVER SKABELONER --}}
                        @if(!empty($templates) && count($templates) > 0)
                            @foreach($templates as $tpl)
                                <div x-data="{ editing: false, tempName: '{{ $tpl->name }}' }" class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                                    <div class="flex items-center justify-between gap-4 flex-wrap">
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                                <span>📋</span>
                                                <span>{{ $tpl->name }}</span>
                                            </h4>
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                Parrede felter: <strong class="text-slate-700 font-mono">{{ count($tpl->mapping ?? []) }}</strong>
                                            </p>
                                        </div>

                                        <div class="flex items-center gap-2 flex-wrap">
                                            <button type="button" wire:click="$set('selectedTemplateId', {{ $tpl->id }}); $wire.loadTemplate()" class="px-3 py-1.5 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition shadow-xs cursor-pointer">
                                                📥 Brug
                                            </button>
                                            <button type="button" wire:click="exportTemplate({{ $tpl->id }})" class="px-3 py-1.5 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition shadow-xs cursor-pointer">
                                                📤 Eksportér
                                            </button>
                                            <button type="button" @click="editing = !editing" class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition shadow-xs cursor-pointer">
                                                <span x-text="editing ? 'Luk' : '✏️ Redigér'"></span>
                                            </button>
                                            <button type="button" wire:click="deleteTemplate({{ $tpl->id }})" wire:confirm="Er du sikker på, at du vil slette denne skabelon?" class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition cursor-pointer">
                                                🗑️ Slet
                                            </button>
                                        </div>
                                    </div>

                                    <div x-show="!editing" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 pt-2 border-t border-slate-200/60">
                                        @foreach(($tpl->mapping ?? []) as $targetField => $sourceCol)
                                            <div class="bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/80 text-[11px] flex items-center justify-between gap-1">
                                                <span class="font-semibold text-slate-700 truncate">{{ $targetField }}</span>
                                                <span class="font-mono text-indigo-600 font-bold text-[10px] bg-indigo-50 px-1.5 py-0.5 rounded truncate max-w-[90px]">{{ $sourceCol }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div x-show="editing" class="pt-4 border-t border-slate-200/80 space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">Skabelonnavn</label>
                                            <input type="text" x-model="tempName" class="w-full max-w-md px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:border-indigo-500">
                                        </div>
                                        <div class="flex justify-end pt-2">
                                            <button type="button" wire:click="updateTemplate({{ $tpl->id }}, tempName)" @click="editing = false" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2 rounded-xl transition shadow-xs cursor-pointer">
                                                💾 Gem ændringer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-slate-400 text-center py-4">Ingen gemte skabeloner endnu.</p>
                        @endif

                        {{-- BEMÆRKNINGER TIL IMPORT / MAPPING --}}
                        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Bemærkninger til import</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Tilføj noter eller generér en automatisk beskrivelse.</p>
                                </div>
                                <button type="button" wire:click="generateNotes" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition shadow-xs cursor-pointer flex items-center gap-1.5">
                                    <span>✨</span> Generér automatisk note
                                </button>
                            </div>
                            <textarea wire:model="importNotes" rows="3" placeholder="Skriv egne bemærkninger her..." class="w-full rounded-2xl border border-slate-200 p-3.5 text-xs text-slate-800 outline-none focus:border-indigo-500 bg-slate-50/50 resize-y"></textarea>
                        </div>
                    </div>
                </details>
            </div>

            {{-- 👁️ FORHÅNDSVISNING AF EKSEMPELSKEMA --}}
            @if(!empty($previewRows))
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Forhåndsvisning af fil (Første 3 rækker)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tjek at kolonnerne matcher korrekt.</p>
                        </div>
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[10px] font-bold">
                            {{ count($sourceColumns) }} kolonner fundet
                        </span>
                    </div>

                    <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-700 border-b border-slate-200 font-bold">
                                <tr>
                                    @foreach($sourceColumns as $col)
                                        {{-- Tilføjet unikt ID her, så vi kan pege på den --}}
                                        <th id="csv-col-{{ Str::slug($col) }}" class="p-3 truncate max-w-[150px] transition-colors duration-300">
                                            {{ $col }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-600">
                                @foreach($previewRows as $row)
                                    <tr class="hover:bg-slate-50/50">
                                        @foreach($sourceColumns as $index => $col)
                                            <td class="p-3 truncate max-w-[150px] font-mono text-[11px]">
                                                {{ $row[$index] ?? '' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- KOBLE GAMLE KOLONNER TIL NYE FELTER (Med unikt ID til scroll-to) --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900">Koble gamle kolonner til nye databasefelter</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Vælg hvilken kolonne fra filen der svarer til systemfelterne.</p>
                </div>

                <div class="space-y-3">
                    @foreach($targetFields as $targetKey => $targetLabel)
                        <div id="map-field-{{ $targetKey }}" class="flex items-center justify-between bg-slate-50/50 p-3 rounded-2xl border border-slate-200/80 scroll-mt-6">
                            <span class="text-xs font-bold text-slate-800 w-1/3">
                                {{ $targetLabel }} 
                                <span class="text-[10px] text-slate-400 font-mono font-normal block mt-0.5">DB: {{ $targetKey }}</span>
                            </span>
                            <span class="text-slate-400">&rarr;</span>
                            <select wire:model="mapping.{{ $targetKey }}" class="w-1/2 bg-white border border-slate-200 rounded-xl p-2 text-xs text-slate-800 outline-none focus:border-indigo-500">
                                <option value="">-- Vælg kolonne fra fil --</option>
                                @foreach($sourceColumns as $source)
                                    <option value="{{ $source }}">{{ $source }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- GEM NY SKABELON SEKTION --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs flex gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Gem denne mapping som en ny skabelon</label>
                    <input type="text" wire:model="templateName" placeholder="F.eks. Standard Format" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs outline-none focus:border-indigo-500 bg-white" />
                </div>
                <button type="button" wire:click="saveTemplate" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">Gem skabelon</button>
            </div>

            <div class="flex justify-between pt-2">
                <button type="button" wire:click="$set('step', 1)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition cursor-pointer">Tilbage</button>
                <button type="button" wire:click="executeImport" wire:loading.attr="disabled" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                    <span wire:loading.remove>Start Importér &rarr;</span>
                    <span wire:loading>Importerer...</span>
                </button>
            </div>
        </div>
    @endif

    {{-- STEP 3: Færdig --}}
    @if ($step == 3)
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-xs text-center space-y-4">
            <div class="mx-auto w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 text-3xl shadow-inner">
                🎉
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Import fuldført!</h3>
                <p class="text-xs text-slate-500 mt-1">Dine data er nu succesfuldt overført til systemet.</p>
            </div>
            <div class="pt-4">
                <button type="button" wire:click="$set('step', 1)" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer">
                    Importer mere data
                </button>
            </div>
        </div>
    @endif
</div>