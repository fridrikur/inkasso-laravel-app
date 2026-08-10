@if(!empty($templates) && count($templates) > 0)
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <details class="group">
            <summary class="flex items-center justify-between p-6 cursor-pointer select-none bg-slate-50/50 hover:bg-slate-100/60 transition">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-base">
                        ⚙️
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Gemte Skabeloner ({{ count($templates) }})</h3>
                        <p class="text-xs text-slate-500">Se, redigér eller slet dine gemte kolonne-parringer</p>
                    </div>
                </div>
                <span class="text-slate-400 group-open:rotate-180 transition-transform duration-200">
                    ▼
                </span>
            </summary>

            <div class="p-6 border-t border-slate-100 space-y-6">
                @foreach($templates as $tpl)
                    <div x-data="{ editing: false }" class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                        
                        {{-- VISNING AF SKABELON --}}
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span>📋</span>
                                    <span>{{ $tpl->navn }}</span>
                                </h4>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Antal parrede felter: <strong class="text-slate-700 font-mono">{{ count($tpl->mapping ?? []) }}</strong>
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="editing = !editing" 
                                    class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition shadow-sm"
                                >
                                    <span x-text="editing ? 'Luk' : '✏️ Redigér'"></span>
                                </button>

                                <form method="POST" action="{{ route('sager.import.templates.destroy', $tpl->id) }}" onsubmit="return confirm('Er du sikker på, at du vil slette denne skabelon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition">
                                        🗑️ Slet
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- FORHÅNDSVISNING AF PARREDE FELTER (NÅR MANGEL PÅ REDIGER) --}}
                        <div x-show="!editing" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 pt-2 border-t border-slate-200/60">
                            @foreach(($tpl->mapping ?? []) as $field => $colIndex)
                                <div class="bg-white px-2.5 py-1.5 rounded-lg border border-slate-200/80 text-[11px] flex items-center justify-between gap-1">
                                    <span class="font-semibold text-slate-700 truncate">{{ $field }}</span>
                                    <span class="font-mono text-indigo-600 font-bold text-[10px] bg-indigo-50 px-1.5 py-0.5 rounded">#{{ $colIndex + 1 }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- REDIGERINGS-FORMULAR --}}
                        <form x-show="editing" method="POST" action="{{ route('sager.import.templates.update', $tpl->id) }}" class="pt-4 border-t border-slate-200/80 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Skabelonnavn</label>
                                <input type="text" name="navn" value="{{ $tpl->navn }}" required class="w-full max-w-md px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs outline-none focus:border-indigo-500">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach(($tpl->mapping ?? []) as $field => $colIndex)
                                    <div class="flex items-center justify-between gap-2 bg-white p-2.5 rounded-xl border border-slate-200 text-xs">
                                        <span class="font-bold text-slate-800">{{ $field }}</span>
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400">Kolonne #:</span>
                                            <input type="number" name="mapping[{{ $field }}]" value="{{ $colIndex }}" min="0" class="w-16 px-2 py-1 rounded-lg border border-slate-200 text-center font-mono font-bold text-indigo-600 text-xs">
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-2 rounded-xl transition shadow-sm">
                                    💾 Gem ændringer
                                </button>
                            </div>
                        </form>

                    </div>
                @endforeach
            </div>
        </details>
    </div>
@endif