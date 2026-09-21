<div class="space-y-6 relative" x-data="{ showCreateModal: false, renamingId: null }">

    {{-- TOP BAR: SKABELONER MED BLYANT + OPRET NY KNAP --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">

        {{-- Venstre: Tabs med blyant-omdøbning --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1">Skabeloner:</span>
            
            @foreach($breveList as $brev)
                <div class="relative flex items-center bg-slate-50 border border-slate-200 rounded-xl p-1 shadow-2xs">
                    
                    {{-- Vælg brev --}}
                    <button
                        type="button"
                        wire:click="loadBrev({{ $brev['id'] }})"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition
                            {{ $brevId === $brev['id']
                                ? 'bg-indigo-600 text-white shadow-xs'
                                : 'text-slate-700 hover:bg-slate-200/60'
                            }}"
                    >
                        {{ $brev['titel'] }}
                    </button>

                    {{-- ✏️ Blyant-ikon til omdøbning --}}
                    <button
                        type="button"
                        @click="renamingId = (renamingId === {{ $brev['id'] }} ? null : {{ $brev['id'] }})"
                        class="text-slate-400 hover:text-indigo-600 px-1.5 text-xs transition"
                        title="Omdøb skabelon"
                    >
                        ✏️
                    </button>

                    {{-- Slet knap (Opdateret til at åbne den professionelle modal) --}}
                    <button
                        type="button"
                        wire:click="confirmDeleteBrev({{ $brev['id'] }})"
                        class="text-rose-400 hover:text-rose-600 px-1.5 text-xs font-bold transition cursor-pointer"
                        title="Slet skabelon"
                    >
                        ✕
                    </button>

                    {{-- SKJULT OMDØBNINGS-POPUP NÅR DER KLIKKES PÅ BLYANTEN --}}
                    <div 
                        x-show="renamingId === {{ $brev['id'] }}" 
                        @click.away="renamingId = null"
                        class="absolute top-full left-0 mt-2 z-30 bg-white border border-slate-200 rounded-xl p-2 shadow-xl flex items-center gap-2"
                        style="display: none;"
                    >
                        <input
                            type="text"
                            value="{{ $brev['titel'] }}"
                            wire:change="updateBrevTitle({{ $brev['id'] }}, $event.target.value); renamingId = null;"
                            placeholder="Ny titel..."
                            class="border border-slate-200 rounded-lg px-2 py-1 text-xs w-36 bg-slate-50 text-slate-800 font-medium focus:outline-none focus:border-indigo-500"
                        >
                        <button 
                            type="button" 
                            @click="renamingId = null" 
                            class="text-xs bg-slate-900 text-white px-2 py-1 rounded-lg font-bold cursor-pointer"
                        >
                            Gem
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Højre side: Opret-knap + Link til Sortering/Filter opsætning --}}
        {{-- Route::get('/breve/filter', [BrevFilterController::class, 'index'])->name('breve.filter'); --}}
        
        <div class="flex items-center gap-2">
            <a
                href="{{ route('breve.filter') }}" 
                class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs flex items-center gap-1.5 cursor-pointer"
                title="Ret rækkefølge og felter for breve"
            >
                <span>⚙️</span> Sorter & felter
            </a>

            <button
                type="button"
                @click="showCreateModal = true"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5 cursor-pointer"
            >
                <span>+</span> Opret ny skabelon
            </button>
        </div>

    </div>

    {{-- TOOLBAR --}}
    <div class="flex gap-2 items-center">
        <button
            type="button"
            wire:click="generatePreview"
            class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer"
        >
            🔄 Opdater preview
        </button>

        <button
            type="button"
            wire:click="loadRandomSag"
            class="bg-purple-600 hover:bg-purple-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer"
        >
            🎲 Random sag
        </button>

        <button
            type="button"
            wire:click="saveTemplate"
            class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs ml-auto flex items-center gap-1.5 cursor-pointer"
        >
            💾 Gem brev
        </button>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- META --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 space-y-4 shadow-sm">
            <h2 class="font-bold text-slate-800 text-sm">Brev info</h2>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Titel</label>
                <input wire:model="titel" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Titel">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Emne / Overskrift</label>
                <input wire:model="emne" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Emne">
            </div>
        </div>

        {{-- EDITOR --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm" x-data="{
            formatText(tag) {
                const textarea = document.getElementById('brev-textarea');
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const selectedText = textarea.value.substring(start, end);
                
                // Indpak markeret tekst i HTML tags (f.eks. <b>tekst</b>)
                const replacement = `<${tag}>${selectedText}</${tag}>`;
                
                textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
                
                // Synkroniser med Livewire ved at udløse et input-event
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                
                // Sæt fokus tilbage og marker teksten/placer cursoren
                textarea.focus();
                textarea.setSelectionRange(start + tag.length + 2, end + tag.length + 2);
            }
        }">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-slate-800 text-sm">Skabelon</h2>

                {{-- SIMPEL FORMATERINGS-TOOLBAR --}}
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <button type="button" @click="formatText('b')" class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700 hover:bg-white transition shadow-2xs cursor-pointer" title="Fed tekst"><b>B</b></button>
                    <button type="button" @click="formatText('i')" class="px-2.5 py-1 rounded-lg text-xs italic text-slate-700 hover:bg-white transition shadow-2xs cursor-pointer" title="Kursiv tekst"><i>I</i></button>
                    <button type="button" @click="formatText('u')" class="px-2.5 py-1 rounded-lg text-xs underline text-slate-700 hover:bg-white transition shadow-2xs cursor-pointer" title="Understreget tekst"><u>U</u></button>
                    <span class="text-slate-300 px-1">|</span>
                    <button type="button" @click="formatText('p')" class="px-2 py-1 rounded-lg text-[11px] font-semibold text-slate-700 hover:bg-white transition shadow-2xs cursor-pointer" title="Ny afsnit">Afsnit</button>
                </div>
            </div>

            <textarea
                id="brev-textarea"
                wire:model="tekst"
                @drop.prevent="
                    const token = $event.dataTransfer.getData('text/plain');
                    const start = $el.selectionStart;
                    const end = $el.selectionEnd;
                    const newValue = $el.value.substring(0, start) + token + $el.value.substring(end);$el.value = newValue;
                    $el.selectionStart =$el.selectionEnd = start + token.length;
                    $el.dispatchEvent(new Event('input', { bubbles: true }));
                "
                @dragover.prevent="$event.dataTransfer.dropEffect = 'copy'"
                class="w-full border border-slate-200 rounded-xl px-3 py-2 min-h-[600px] font-mono text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
            ></textarea>
        </div>

        {{-- PREVIEW --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-bold text-slate-800 text-sm">Preview</h2>

                <button
                    type="button"
                    wire:click="togglePreview"
                    class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg font-medium transition cursor-pointer"
                >
                    Fullscreen
                </button>
            </div>

            <div class="prose border border-slate-100 rounded-xl p-4 min-h-[600px] bg-slate-50/50 max-w-none">
                {!! $previewHtml ?: '<span class="text-slate-400">Klik “Opdater preview”</span>' !!}
            </div>
        </div>

    </div>

    {{-- MODAL TIL OPRETTELSE AF NY SKABELON --}}
    <div 
        x-show="showCreateModal" 
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div @click.away="showCreateModal = false" class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Opret ny brevskabelon</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Navn på skabelon</label>
                <input
                    type="text"
                    wire:model="newBrevTitle"
                    placeholder="F.eks. Rykkerbrev 1..."
                    class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs bg-slate-50 text-slate-800 focus:outline-none focus:border-emerald-500 font-medium"
                >
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    @click="showCreateModal = false"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer"
                >
                    Annuller
                </button>
                <button
                    type="button"
                    wire:click="createNewBrev(); showCreateModal = false;"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm cursor-pointer"
                >
                    Opret skabelon
                </button>
            </div>
        </div>
    </div>

    {{-- FLYDENDE / TRÆKBAR BOKS MED TILGÆNGELIGE FELTER --}}
    <div 
        x-data="{ 
            open: true,
            x: window.innerWidth - 320, 
            y: 120,
            dragging: false,
            startX: 0,
            startY: 0,
            startDrag(e) {
                this.dragging = true;
                this.startX = e.clientX - this.x;
                this.startY = e.clientY - this.y;
            },
            doDrag(e) {
                if (!this.dragging) return;
                this.x = e.clientX - this.startX;
                this.y = e.clientY - this.startY;
            },
            stopDrag() {
                this.dragging = false;
            }
        }"
        @mousemove.window="doDrag($event)"
        @mouseup.window="stopDrag()"
        :style="`left: ${x}px; top: ${y}px; position: fixed;`"
        class="w-72 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden transition-shadow"
    >
        <div 
            @mousedown="startDrag($event)"
            class="cursor-move bg-slate-900 text-white px-4 py-3 flex justify-between items-center select-none"
        >
            <div class="flex items-center gap-2">
                <span class="text-xs">⋮⋮</span>
                <span class="font-bold text-xs tracking-wide">Tilgængelige felter</span>
            </div>
            <button 
                type="button" 
                @click="open = !open" 
                class="text-xs text-slate-400 hover:text-white transition cursor-pointer"
            >
                <span x-text="open ? '–' : '+ '"></span>
            </button>
        </div>

        <div x-show="open" class="p-3 max-h-80 overflow-y-auto bg-slate-50/50 space-y-2">
            <p class="text-[10px] text-slate-400 font-medium">Træk et felt over i skabelon-teksten:</p>
            <div class="flex flex-wrap gap-1.5">
                @php
                    $standardTokens = array_merge(
                        (new \App\Models\Sager())->getFillable(),
                        ['today', 'aktiv', 'firmanavn', 'debitor_navn', 'ktr']
                    );
                @endphp
                @foreach($standardTokens as $token)
                    <div
                        draggable="true"
                        @dragstart="$event.dataTransfer.setData('text/plain', '{{ '{'.$token.'}' }}')"
                        class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-2.5 py-1 rounded-lg cursor-grab active:cursor-grabbing text-xs font-mono font-bold select-none hover:bg-indigo-600 hover:text-white transition shadow-2xs"
                    >
                        {{ '{'.$token.'}' }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- FULLSCREEN PREVIEW MODAL --}}
    @if($previewExpanded)
        <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white w-[95vw] h-[95vh] rounded-2xl p-6 overflow-auto relative shadow-2xl">
                <button
                    type="button"
                    wire:click="togglePreview"
                    class="absolute top-4 right-4 bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl font-bold text-xs transition cursor-pointer"
                >
                    ✕ Close
                </button>

                <div class="prose max-w-none pt-4">
                    {!! $previewHtml !!}
                </div>
            </div>
        </div>
    @endif

    {{-- 🗑️ PROFESSIONEL SLET-MODAL --}}
    @if($showDeleteBrevModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-xs p-4">
            <div class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 text-xl font-bold">
                        🗑️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Bekræft sletning</h3>
                        <p class="text-xs text-slate-500">Denne handling kan ikke fortydes.</p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed">
                    Er du sikker på, at du vil slette skabelonen <span class="font-bold text-slate-900">"{{ $brevToDeleteTitle }}"</span>?
                </p>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="cancelDeleteBrev"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer"
                    >
                        Annuller
                    </button>
                    <button
                        type="button"
                        wire:click="executeDeleteBrev"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm cursor-pointer"
                    >
                        Ja, slet permanent
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>