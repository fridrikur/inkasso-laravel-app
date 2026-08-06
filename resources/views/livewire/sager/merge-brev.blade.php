<div> {{-- SINGLE ROOT FOR LIVEWIRE --}}
    <div class="space-y-6 relative">

        {{-- Tabs --}}
        <div class="border-b flex flex-wrap gap-2 overflow-x-auto pb-2 items-center">
            @foreach($breveList as $brev)
                <div class="flex items-center gap-1">
                    <button
                        wire:click="loadBrev({{ $brev['id'] }})"
                        class="px-3 py-1 text-sm border-b-2 transition
                            {{ $brevId === $brev['id']
                                ? 'border-indigo-600 text-indigo-600 font-semibold'
                                : 'border-transparent text-slate-600 hover:text-slate-800'
                            }}"
                    >
                        {{ $brev['titel'] }}
                    </button>

                    <input
                        type="text"
                        value="{{ $brev['titel'] }}"
                        wire:change="updateBrevTitle({{ $brev['id'] }}, $event.target.value)"
                        class="border rounded px-1.5 py-0.5 text-xs w-28 bg-slate-50 border-slate-200"
                        title="Rediger titel"
                    >
                </div>
            @endforeach

            {{-- Opret nyt brev --}}
            <div class="flex items-center gap-1 ml-2">
                <input
                    type="text"
                    placeholder="Ny brev titel"
                    wire:model="newBrevTitle"
                    class="border border-slate-200 rounded px-2.5 py-1 text-xs"
                >
                <button
                    wire:click="createNewBrev"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-2.5 py-1 rounded text-xs font-bold transition"
                >
                    +
                </button>
            </div>
        </div>

        {{-- Mode Toggle --}}
        <div class="flex border-b border-slate-200">
            <button
                wire:click="$set('mode', 'preview')"
                type="button"
                class="px-4 py-2 text-sm border-b-2 font-medium transition
                    {{ $mode === 'preview'
                        ? 'border-indigo-600 text-indigo-600 font-bold'
                        : 'border-transparent text-slate-500 hover:text-slate-700'
                    }}"
            >
                👁️ Preview
            </button>

            <button
                wire:click="$set('mode', 'edit')"
                type="button"
                class="px-4 py-2 text-sm border-b-2 font-medium transition
                    {{ $mode === 'edit'
                        ? 'border-indigo-600 text-indigo-600 font-bold'
                        : 'border-transparent text-slate-500 hover:text-slate-700'
                    }}"
            >
                ✏️ Rediger skabelon
            </button>
        </div>

        {{-- Emne --}}
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Emne / Overskrift</label>
            <input
                type="text"
                wire:model="emne"
                class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-medium shadow-sm focus:border-indigo-500 focus:outline-none"
            >
        </div>

        {{-- Content Area --}}
        <div class="mt-4">
            {{-- Preview Mode --}}
            @if($mode === 'preview')
                <div class="border border-slate-200 p-8 bg-white prose max-w-none min-h-[500px] rounded-2xl shadow-sm">
                    {!! $preview !!}
                </div>
            @endif

            {{-- Edit Mode --}}
            @if($mode === 'edit')
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Skabelon Tekst</label>
                        <textarea
                            id="skabelon-textarea"
                            wire:model="tekst"
                            class="w-full border border-slate-200 rounded-2xl p-4 min-h-[420px] font-mono text-sm leading-relaxed shadow-sm focus:border-indigo-500 focus:outline-none"
                            ondrop="insertToken(event)"
                            ondragover="event.preventDefault()"
                        ></textarea>
                    </div>

                    {{-- Knapper --}}
                    <div class="relative flex gap-3 items-center">
                        <button
                            wire:click="generatePreview"
                            wire:loading.attr="disabled"
                            type="button"
                            class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-xs shadow-sm hover:bg-indigo-700 transition"
                        >
                            Opdater preview
                        </button>

                        <button
                            wire:click="saveTemplate"
                            type="button"
                            class="px-4 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-xs shadow-sm hover:bg-slate-800 transition disabled:opacity-40"
                            @disabled(!$dirty)
                        >
                            Gem skabelon
                        </button>

                        @if($brevId)
                            <a 
                                href="{{ route('sager.breve.pdf', ['sag' => $sag->id, 'brev' => $brevId]) }}"
                                target="_blank"
                                class="px-4 py-2.5 border border-slate-200 text-slate-700 rounded-xl font-bold text-xs hover:bg-slate-50 transition"
                            >
                                📄 Generer PDF
                            </a>
                        @endif

                        @if($dirty)
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100">
                                Unsaved changes
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- 🟢 FLYDENDE / TRÆKBAR BOKS MED FELTER (Alpine.js Drag) --}}
        @if($mode === 'edit')
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
                        @foreach($availableTokens as $token)
                            <div
                                draggable="true"
                                ondragstart="event.dataTransfer.setData('text/plain', '{{ '{'.$token.'}' }}')"
                                class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-2.5 py-1 rounded-lg cursor-grab active:cursor-grabbing text-xs font-mono font-bold select-none hover:bg-indigo-600 hover:text-white transition shadow-2xs"
                            >
                                {{ '{'.$token.'}' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
function insertToken(event) {
    event.preventDefault();
    const token = event.dataTransfer.getData("text/plain");
    const textarea = document.getElementById('skabelon-textarea');
    if (!textarea) return;

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;

    textarea.value =
        textarea.value.substring(0, start) +
        token +
        textarea.value.substring(end);

    textarea.dispatchEvent(new Event('input'));
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = start + token.length;
}
</script>