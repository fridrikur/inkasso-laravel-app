<div class="relative pb-24">

    {{-- 1. FANER ØVERST --}}
    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 flex-1">
            @foreach ($breveList as $brev)
                <button
                    type="button"
                    wire:click="loadBrev({{ $brev['id'] }})"
                    class="px-4 py-2 rounded-t-xl rounded-b-none text-xs font-bold transition cursor-pointer whitespace-nowrap flex items-center gap-2 border-t border-x {{ $brevId === $brev['id'] ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}"
                >
                    <span>✉️</span> {{ $brev['titel'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- 2. SKABELONINDHOLD --}}
    <div class="bg-white border border-slate-200 rounded-b-2xl rounded-tr-2xl p-6 shadow-sm flex flex-col space-y-4">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Emne-felt --}}
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Emne / overskrift:</label>
                <input 
                    type="text" 
                    wire:model="emne" 
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900" 
                    placeholder="Skriv emne..."
                >
            </div>

            {{-- Top Margin --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Skub indholdet op eller ned">Top-margin (px):</label>
                <input type="number" wire:model.live="margin_top" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
            </div>

            {{-- Left Margin --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Skub indholdet til højre eller venstre">Venstre-margin (px):</label>
                <input type="number" wire:model.live="margin_left" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
            </div>

            {{-- Right Margin --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Juster højre margin">Højre-margin (px):</label>
                <input type="number" wire:model.live="margin_right" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Afstand mellem linjer (f.eks. 1.2, 1.5, 2.0)">Linjeafstand:</label>
            <select wire:model.live="linjeafstand" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900 cursor-pointer">
                <option value="1.0">Tæt (1.0)</option>
                <option value="1.2">Normal (1.2)</option>
                <option value="1.3">Standard brev (1.3)</option>
                <option value="1.5">Luftig (1.5)</option>
                <option value="2.0">Dobbelt (2.0)</option>
            </select>
        </div>

        {{-- Beskyttet Checkbox --}}
        <div class="flex flex-col justify-end">
            <label class="flex items-center gap-2 h-[42px] px-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                <input type="checkbox" wire:model.live="beskyttet" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                <span class="text-xs font-bold text-slate-700">🔒 Beskyttet</span>
            </label>
        </div>

        {{-- OVERSKRIFT OG BREVHOVED-KNAP SAMLET --}}
        <div class="flex items-center justify-between pb-2">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Skabelonens layout og indhold:</label>
            
            {{-- Brevhoved Indstillinger (Dropdown) --}}
            <div class="relative" x-data="{ openHeaderMenu: false }">
                <button 
                    type="button" 
                    @if(!$beskyttet) @click="openHeaderMenu = !openHeaderMenu" @else @click="alert('🚫 Denne skabelon er beskyttet. Brevhovedet kan ikke ændres.');" @endif
                    class="bg-white text-slate-800 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-2xs {{ $beskyttet ? 'opacity-60 cursor-not-allowed bg-slate-100' : 'hover:bg-slate-100 cursor-pointer' }}"
                >
                    <span>📄 Brevhoved indstillinger</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ ($filter['skjulalle'] ?? false) ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $beskyttet ? 'Låst' : (($filter['skjulalle'] ?? false) ? 'Skjult' : 'Aktiv') }}
                    </span>
                </button>

                {{-- Dropdown boks (Kun tilgængelig hvis ikke beskyttet) --}}
                @if(!$beskyttet)
                    <div 
                        x-show="openHeaderMenu" 
                        @click.outside="openHeaderMenu = false"
                        class="absolute right-0 mt-2 w-64 bg-white border border-slate-200 rounded-xl shadow-xl p-4 z-50 space-y-3 text-xs"
                        style="display: none;"
                    >
                        <div class="font-bold text-slate-700 border-b border-slate-100 pb-2 flex justify-between items-center">
                            <span>Styr brevhoved visning</span>
                            <span class="text-[10px] text-slate-400">Pr. skabelon</span>
                        </div>

                        {{-- Master kontrol --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-800">
                                <input type="checkbox" wire:model.live="filter.visalle" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                                <span>Vis alle felter i top</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-800">
                                <input type="checkbox" wire:model.live="filter.skjulalle" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 w-3.5 h-3.5">
                                <span>Skjul hele brevhovedet</span>
                            </label>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Detaljerede felter --}}
                        <div class="space-y-1.5 text-slate-600">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.navn" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Navn</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.adresse" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Adresse</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.dato" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Dato</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.sagsnr" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Sagsnr.</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.emne" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Emne</span>
                            </label>
                        </div>
                    </div>
                    <hr class="border-slate-100">

                    {{-- Styring af højre top-margin --}}
                    <div class="space-y-1 pt-1">
                        <label class="block font-medium text-slate-700">Højre top-afstand (pixel)</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="number" 
                                wire:model.live="right_margin_top" 
                                class="w-full border border-slate-300 rounded-lg px-2.5 py-1 text-slate-800 text-xs focus:ring-indigo-500 focus:border-indigo-500" 
                                min="0" 
                                max="300"
                                step="5"
                            >
                            <span class="text-slate-400 text-[10px]">px</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @php
            $skjulAlt = $filter['skjulalle'] ?? false;
            $visAlt   = $filter['visalle'] ?? false;

            $visNavn    = $visAlt || (!$skjulAlt && ($filter['navn'] ?? true));
            $visAdresse = $visAlt || (!$skjulAlt && ($filter['adresse'] ?? true));
            $visDato    = $visAlt || (!$skjulAlt && ($filter['dato'] ?? true));
            $visSagsnr  = $visAlt || (!$skjulAlt && ($filter['sagsnr'] ?? true));
        @endphp

        {{-- DYNAMISK TOP-HEADER (Viser præcis de valgte eksempler) --}}
        @if($skjulAlt)
            <div class="w-full text-xs text-slate-400 bg-slate-50 border border-dashed border-slate-300 rounded-t-2xl p-4 text-center italic">
                🚫 Brevhovedet er skjult for denne skabelon
            </div>
        @else
            {{-- TOP SEKTION: 2-rækket rammefri tabel, hvor højre side starter hvor venstre side slutter --}}
            <table class="w-full text-xs sm:text-sm text-slate-700 font-sans" style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 20px;" cellspacing="0" cellpadding="0" border="0">
                
                {{-- RÆKKE 1: Kun modtageradresse i venstre side --}}
                <tr style="border: none;">
                    <td style="width: 50%; vertical-align: top; text-align: left; padding: 20px; padding-bottom: 0; border: none;">
                        @if($visNavn || $visAdresse)
                            <div class="space-y-1">
                                @if($visNavn)
                                    <div class="font-bold text-base text-slate-900">[ Eksempel Navn ]</div>
                                @endif
                                @if($visAdresse)
                                    <div class="text-slate-600">[ Eksempel Adresse ]</div>
                                    <div class="text-slate-600">[ Postnr By ]</div>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-300 italic">[ Modtagerfelt skjult ]</span>
                        @endif
                    </td>
                    {{-- Højre side er tom i første række, så venstre side får lov at bestemme højden --}}
                    <td style="width: 50%; border: none;"></td>
                </tr>

                {{-- RÆKKE 2: Dato og Sagsnr i højre side (starter præcist under adressen + din finjustering) --}}
                <tr style="border: none;">
                    {{-- Venstre side er tom i anden række --}}
                    <td style="width: 50%; border: none;"></td>
                    
                    <td style="width: 50%; vertical-align: top; text-align: right; padding: 20px; padding-top: {{ 15 + ($right_margin_top ?? 0) }}px; border: none;">
                        @if($visDato || $visSagsnr)
                            <div class="space-y-1 font-medium text-slate-900" style="line-height: 1.4;">
                                @if($visDato)
                                    <div><strong>Dato:</strong> {{ $sag->dato ?? date('d.m.Y') }}</div>
                                @endif
                                @if($visSagsnr)
                                    <div><strong>J. nr.:</strong> {{ $sag->sagsnr ?? '123456' }}</div>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-300 italic">[ Dato/Sagsnr skjult ]</span>
                        @endif
                    </td>
                </tr>

            </table>
        @endif

    {{-- WYSIWYG REDIGERER TIL SELVE BREVTEKSTEN --}}
    <x-wysiwyg-editor 
        wire:key="editor-{{ $brevId }}-{{ $beskyttet ? 'prot' : 'unprot' }}"
        wireModel="tekst" 
        :value="$tekst"
        :marginTop="$margin_top" 
        :marginLeft="$margin_left" 
        :marginRight="$margin_right"
        :linjeafstand="$linjeafstand" 
        :beskyttet="$beskyttet"
        height="450px" 
    />
    </div>

    {{-- 3. BUNDMED ALLE KNAPPER --}}
    <div class="mt-4 bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4 text-white">
        <div class="flex items-center gap-2">
    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Handlinger:</span>
    @if ($brevId)
        <button type="button" wire:click="openRenameModal" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 border border-slate-700">
            ✏️ Omdøb
        </button>
        <button type="button" wire:click="confirmDeleteBrev({{ $brevId }})" class="px-3.5 py-2 bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 border border-rose-900/50">
            🗑️ Slet
        </button>
    @endif
    <button type="button" wire:click="openCreateModal" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 shadow-sm">
        <span>+</span> Opret ny skabelon
    </button>
</div>

        <div>
        <div>
    <button type="button" wire:click="saveTemplate" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition cursor-pointer shadow-lg flex items-center gap-2">
        💾 Gem skabelon
    </button>
</div>
    </div>

    {{-- 4. TRÆKBAR BOKS MED FELTER --}}
    <div 
        x-data="{
            open: true,
            dragging: false,
            x: window.innerWidth - 320,
            y: 120,
            startX: 0,
            startY: 0,
            init() {
                if (this.x > window.innerWidth - 320) this.x = window.innerWidth - 320;
            }
        }"
        @mousemove.window="
            if (dragging) {
                x = Math.max(10, Math.min(window.innerWidth - 300, startX + ($event.clientX - mouseX)));
                y = Math.max(10, Math.min(window.innerHeight - 200, startY + ($event.clientY - mouseY)));
            }
        "
        @mouseup.window="dragging = false"
        :style="`left: ${x}px; top: ${y}px; position: fixed;`" 
        class="w-72 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden select-none"
    >
        <div 
            @mousedown="
                dragging = true; 
                mouseX = $event.clientX; 
                mouseY = $event.clientY; 
                startX = x; 
                startY = y;
            "
            class="cursor-grab active:cursor-grabbing bg-slate-900 text-white px-4 py-3 flex justify-between items-center"
        >
            <span class="font-bold text-xs flex items-center gap-1.5">
                <span>⠿</span> Tilgængelige felter
            </span>
            <button type="button" @click="open = !open" class="text-xs text-slate-400 hover:text-white cursor-pointer"><span x-text="open ? '–' : '+'"></span></button>
        </div>
        
        <div x-show="open" class="p-3 max-h-80 overflow-y-auto bg-slate-50 space-y-2">
            <p class="text-[10px] text-slate-400 font-medium px-1">Træk felterne ind i editoren:</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($this->validTokens as $token)
                    <div draggable="true" @dragstart="$event.dataTransfer.setData('text/plain', '{{ '{'.$token.'}' }}')" class="px-2.5 py-1 rounded-lg cursor-grab text-xs font-mono font-bold bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-600 hover:text-white transition shadow-2xs">
                        {{ '{'.$token.'}' }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODALER (Opret, Omdøb, Slet) --}}
    {{-- MODAL: OPRET NY SKABELON --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div wire:click.away="closeCreateModal" class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Opret ny brevskabelon</h3>
                    <button type="button" wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700">Skabelontitel</label>
                    <input type="text" wire:model.blur="newBrevTitle" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-slate-50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900" placeholder="F.eks. Rykker 1...">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeCreateModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Annuller</button>
                    <button type="button" wire:click="createNewBrev" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer">Opret</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: OMDØB SKABELON --}}
    @if($showRenameModal)
        <div class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div wire:click.away="closeRenameModal" class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Omdøb skabelon</h3>
                    <button type="button" wire:click="closeRenameModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700">Ny titel</label>
                    <input type="text" wire:model.blur="editTitle" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-slate-50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeRenameModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Annuller</button>
                    <button type="button" wire:click="updateBrevTitle" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold cursor-pointer">Gem navn</button>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteBrevModal)
        <div class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-rose-600">Slet skabelon</h3>
                    <button type="button" wire:click="cancelDeleteBrev" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Er du sikker på, at du vil slette skabelonen <strong class="text-slate-900">{{ $brevToDeleteTitle }}</strong>? Denne handling kan ikke angres.
                </p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="cancelDeleteBrev" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Annuller</button>
                    <button type="button" wire:click="executeDeleteBrev" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold cursor-pointer">Slet permanent</button>
                </div>
            </div>
        </div>
    @endif

</div>