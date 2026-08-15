<div class="space-y-6 relative">

    <h2>Jeg er ikke overbevist</h2>

    {{-- TOP BAR --}}
    <div class="border-b flex flex-wrap gap-2 pb-3">

        @foreach($breveList as $brev)

            <div class="flex items-center gap-1">

                <button
                    type="button"
                    wire:click="loadBrev({{ $brev['id'] }})"
                    class="px-4 py-2 rounded-t-lg text-sm transition
                        {{ $brevId === $brev['id']
                            ? 'bg-blue-600 text-white font-semibold'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        }}"
                >
                    {{ $brev['titel'] }}
                </button>

                <button
                    type="button"
                    wire:click="deleteBrev({{ $brev['id'] }})"
                    onclick="confirm('Slet dette brev?') || event.stopImmediatePropagation()"
                    class="text-red-500 text-xs px-1"
                >
                    ✕
                </button>

            </div>

        @endforeach

        <div class="flex items-center gap-2 ml-auto">

            <input
                type="text"
                wire:model="newBrevTitle"
                placeholder="Ny skabelon"
                class="border rounded px-3 py-2 text-sm"
            >

            <button
                type="button"
                wire:click="createNewBrev"
                class="bg-green-600 text-white px-4 py-2 rounded text-sm font-bold"
            >
                Opret
            </button>

        </div>

    </div>

    {{-- TOOLBAR --}}
    <div class="flex gap-2 items-center">

        <button
            type="button"
            wire:click="generatePreview"
            class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm font-medium"
        >
            🔄 Opdater preview
        </button>

        <button
            type="button"
            wire:click="loadRandomSag"
            class="bg-purple-600 text-white px-3 py-1.5 rounded text-sm font-medium"
        >
            🎲 Random sag
        </button>

        <button
            type="button"
            wire:click="saveTemplate"
            class="bg-gray-800 text-white px-3 py-1.5 rounded text-sm font-medium ml-auto"
        >
            💾 Gem brev
        </button>

    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- META --}}
        <div class="bg-white border rounded-2xl p-4 space-y-4 shadow-sm">

            <h2 class="font-semibold text-gray-800">Brev info</h2>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Titel</label>
                <input wire:model="titel" class="w-full border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Titel">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Emne / Overskrift</label>
                <input wire:model="emne" class="w-full border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Emne">
            </div>

        </div>

        {{-- EDITOR --}}
        <div class="bg-white border rounded-2xl p-4 shadow-sm">

            <h2 class="font-semibold mb-3 text-gray-800">Skabelon</h2>

            <textarea
                wire:model="tekst"
                @drop.prevent="
                    const token = $event.dataTransfer.getData('text/plain');
                    const start = $el.selectionStart;
                    const end = $el.selectionEnd;
                    const newValue = $el.value.substring(0, start) + token + $el.value.substring(end);
                    $el.value = newValue;
                    $el.selectionStart = $el.selectionEnd = start + token.length;
                    $el.dispatchEvent(new Event('input', { bubbles: true }));
                "
                @dragover.prevent="$event.dataTransfer.dropEffect = 'copy'"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 min-h-[600px] font-mono text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
            ></textarea>

        </div>

        {{-- PREVIEW --}}
        <div class="bg-white border rounded-2xl p-4 shadow-sm">

            <div class="flex justify-between items-center mb-3">
                <h2 class="font-semibold text-gray-800">Preview</h2>

                <button
                    type="button"
                    wire:click="togglePreview"
                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-2.5 py-1 rounded-lg font-medium transition"
                >
                    Fullscreen
                </button>
            </div>

            <div class="prose border border-gray-100 rounded-xl p-4 min-h-[600px] bg-gray-50/50 max-w-none">
                {!! $previewHtml ?: '<span class="text-gray-400">Klik “Opdater preview”</span>' !!}
            </div>

        </div>

    </div>

    {{-- 🟢 FLYDENDE / TRÆKBAR BOKS MED TILGÆNGELIGE FELTER --}}
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
        {{-- DRAG HEADER --}}
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
                class="text-xs text-slate-400 hover:text-white transition"
            >
                <span x-text="open ? '–' : '+ '"></span>
            </button>
        </div>

        {{-- DRAG BODY / TOKENS --}}
        <div x-show="open" class="p-3 max-h-80 overflow-y-auto bg-slate-50/50 space-y-2">
            <p class="text-[10px] text-slate-400 font-medium">Træk et felt over i skabelon-teksten:</p>
            <div class="flex flex-wrap gap-1.5">
                {{-- Standard flettefelter baseret på Sager model + today --}}
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

    {{-- MODAL --}}
    @if($previewExpanded)
        <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">

            <div class="bg-white w-[95vw] h-[95vh] rounded-2xl p-6 overflow-auto relative shadow-2xl">

                <button
                    type="button"
                    wire:click="togglePreview"
                    class="absolute top-4 right-4 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-xl font-bold text-xs transition"
                >
                    ✕ Close
                </button>

                <div class="prose max-w-none pt-4">
                    {!! $previewHtml !!}
                </div>

            </div>

        </div>
    @endif

</div>