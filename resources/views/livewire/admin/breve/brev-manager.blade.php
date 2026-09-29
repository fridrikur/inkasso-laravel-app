<div class="space-y-6 relative" x-data="{ showCreateModal: false, showSelectModal: false, showRenameModal: false, editTitle: '' }">

    {{-- TOP BAR: NAVN FØST (VENSTRE) OG CRUD SIDST (HØJRE) --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">

        {{-- 1. NAVNET PÅ SKABELONEN (ALLERFØRSTE TIL VENSTRE) --}}
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Aktiv skabelon:</span>
            <span class="px-3.5 py-2 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded-xl text-xs font-bold shadow-2xs">
                {{ $titel ?: 'Ingen skabelon valgt' }}
            </span>
        </div>

        {{-- MIDDEL: SKABELON INFO (TITEL & EMNE) --}}
        <div class="flex items-center gap-3 flex-1 max-w-xl">
            <div class="flex-1">
                <input wire:model="titel" class="w-full border border-slate-200 rounded-xl px-3 py-1.5 text-xs bg-slate-50 focus:outline-none focus:border-indigo-500 font-medium" placeholder="Skabelontitel...">
            </div>
            <div class="flex-1">
                <input wire:model="emne" class="w-full border border-slate-200 rounded-xl px-3 py-1.5 text-xs bg-slate-50 focus:outline-none focus:border-indigo-500 font-medium" placeholder="Emne / overskrift...">
            </div>
        </div>

        {{-- 2. CRUD AFDELINGEN (ALLERSIDSTE TIL HØJRE) --}}
        <div class="flex items-center gap-2">
            <button
                type="button"
                @click="showSelectModal = true"
                class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer flex items-center gap-1.5"
            >
                <span>📂</span> Skift skabelon
            </button>

            @if ($brevId)
                <button
                    type="button"
                    @click="showRenameModal = true; editTitle = '{{ $titel }}'"
                    class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition cursor-pointer"
                    title="Omdøb aktiv skabelon"
                >
                    ✏️
                </button>

                <button
                    type="button"
                    wire:click="confirmDeleteBrev({{ $brevId }})"
                    class="p-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-xs font-bold transition cursor-pointer"
                    title="Slet aktiv skabelon"
                >
                    🗑️
                </button>
            @endif

            <a
                href="{{ route('breve.filter') }}" 
                class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition shadow-2xs flex items-center justify-center cursor-pointer"
                title="Sorter & felter"
            >
                ⚙️
            </a>

            <button
                type="button"
                @click="showCreateModal = true"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5 cursor-pointer"
            >
                <span>+</span> Opret ny
            </button>
        </div>

    </div>

    {{-- TOOLBAR --}}
    <div class="flex flex-wrap gap-2 items-center">
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
            onclick="printPreview()"
            class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5 cursor-pointer"
            title="Udskriv kun forhåndsvisningen af brevet"
        >
            🖨️ Udskriv brev
        </button>

        <button
            type="button"
            wire:click="saveTemplate"
            class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs ml-auto flex items-center gap-1.5 cursor-pointer"
        >
            💾 Gem brev
        </button>
    </div>

    {{-- EDITOR GRID --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col" x-data="{
        activeTab: 'visual',
        formatText(tag) {
            if (this.activeTab === 'visual') {
                document.execCommand(tag === 'b' ? 'bold' : (tag === 'i' ? 'italic' : 'underline'), false, null);
                return;
            }
            const textarea = document.getElementById('brev-textarea');
            if (!textarea) return;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            
            const replacement = `<${tag}>${selectedText}</${tag}>`;
            textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
        },
        insertTable() {
            const tableHtml = `
<table class=&quot;w-full border-collapse border border-slate-300 my-4 text-xs font-mono&quot;>
    <thead>
        <tr class=&quot;bg-slate-100&quot;>
            <th class=&quot;border border-slate-300 p-2 text-left&quot;>Beskrivelse</th>
            <th class=&quot;border border-slate-300 p-2 text-right&quot;>Beløb</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class=&quot;border border-slate-300 p-2&quot;>Hovedstol / Restance</td>
            <td class=&quot;border border-slate-300 p-2 text-right&quot;>{hovedstol}</td>
        </tr>
        <tr>
            <td class=&quot;border border-slate-300 p-2&quot;>Gebyrer og renter</td>
            <td class=&quot;border border-slate-300 p-2 text-right&quot;>{gebyr}</td>
        </tr>
    </tbody>
</table>
`;
            if (this.activeTab === 'visual') {
                document.execCommand('insertHTML', false, tableHtml);
            } else {
                const textarea = document.getElementById('brev-textarea');
                if (!textarea) return;
                const start = textarea.selectionStart;
                textarea.value = textarea.value.substring(0, start) + tableHtml + textarea.value.substring(start);
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    }">
        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
            {{-- Faneblade knapper --}}
            <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200">
                <button 
                    type="button" 
                    @click="activeTab = 'visual'"
                    :class="activeTab === 'visual' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                >
                    ✨ Visuel Editor
                </button>
                <button 
                    type="button" 
                    @click="activeTab = 'source'"
                    :class="activeTab === 'source' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                >
                    💻 Kilde / HTML
                </button>
                <button 
                    type="button" 
                    @click="activeTab = 'preview'"
                    :class="activeTab === 'preview' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-700 hover:bg-white'"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer"
                >
                    👁️ Preview
                </button>
            </div>

            {{-- FORMATERINGS-TOOLBAR --}}
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200" x-show="activeTab !== 'preview'">
                <button type="button" @click="formatText('b')" class="px-2.5 py-1 rounded-lg text-xs font-bold text-slate-700 hover:bg-white transition shadow-2xs cursor-pointer" title="Fed tekst"><b>B</b></button>
                <button type="button" @click="formatText('i')" class="px-2.5 py-1 rounded-lg text-xs italic text-slate-700 hover:bg-white transition shadow-2xs cursor-pointer" title="Kursiv tekst"><i>I</i></button>
                <button type="button" @click="formatText('u')" class="px-2.5 py-1 rounded-lg text-xs underline text-slate-700 hover:bg-white transition shadow-2xs cursor-pointer" title="Understreget tekst"><u>U</u></button>
                <span class="text-slate-300 px-1">|</span>
                <button type="button" @click="insertTable()" class="px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition shadow-2xs cursor-pointer flex items-center gap-1" title="Indsæt standard tabel">
                    <span>📊</span> Tabel
                </button>
            </div>
        </div>

        {{-- 1A. VISUEL EDITOR (ContentEditable - Ingen rå HTML-tags) --}}
        <div x-show="activeTab === 'visual'" class="flex-1 flex">
            <div 
                contenteditable="true"
                @input="$wire.set('tekst',$event.target.innerHTML)"
                class="w-full h-[700px] min-h-[700px] border border-slate-200 rounded-xl px-4 py-3 font-sans text-xs sm:text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 overflow-y-auto bg-white"
            >
                {!! str_replace(
                    ['{hovedstol}', '{gebyr}', '{restgaeld}', '{sagsnr}'],
                    ['<span contenteditable="false" class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded font-mono text-xs border border-indigo-100 font-bold">25.400,00</span>', 
                     '<span contenteditable="false" class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded font-mono text-xs border border-indigo-100 font-bold">450,00</span>', 
                     '<span contenteditable="false" class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded font-mono text-xs border border-indigo-100 font-bold">25.850,00</span>', 
                     '<span contenteditable="false" class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded font-mono text-xs border border-indigo-100 font-bold">SAG-2026-992</span>'],
                    $tekst
                ) !!}
            </div>
        </div>

        {{-- 1B. KILDE / HTML EDITOR --}}
        <div x-show="activeTab === 'source'" class="flex-1 flex">
            <textarea
                id="brev-textarea"
                wire:model="tekst"
                rows="30"
                @drop.prevent="
                    const token = $event.dataTransfer.getData('text/plain');
                    const start = $el.selectionStart;
                    const end = $el.selectionEnd;
                    const newValue = $el.value.substring(0, start) + token + $el.value.substring(end);$el.value = newValue;
                    $el.selectionStart =$el.selectionEnd = start + token.length;
                    $el.dispatchEvent(new Event('input', { bubbles: true }));
                "
                @dragover.prevent="$event.dataTransfer.dropEffect = 'copy'"
                class="w-full h-[700px] min-h-[700px] border border-slate-200 rounded-xl px-4 py-3 font-mono text-xs sm:text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-y"
                placeholder="Rå HTML kildekode..."
            ></textarea>
        </div>

        {{-- 2. LIVE PREVIEW FANE --}}
        <div x-show="activeTab === 'preview'" style="display: none;" class="flex-1 flex flex-col">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase">Forhåndsvisning med data:</span>
                <button
                    type="button"
                    wire:click="togglePreview"
                    class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg font-medium transition cursor-pointer"
                >
                    Fuldskærm
                </button>
            </div>

            <div class="prose border border-slate-200 rounded-xl p-5 h-[700px] min-h-[700px] overflow-y-auto bg-slate-50/50 max-w-none font-sans text-sm leading-relaxed">
                {!! $previewHtml ?: '<span class="text-slate-400">Klik “Opdater preview” for at generere visning.</span>' !!}
            </div>
        </div>
    </div>

    {{-- MODAL TIL SKIFT / VÆLG SKABELON --}}
    <div 
        x-show="showSelectModal" 
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div @click.away="showSelectModal = false" class="bg-white w-full max-w-lg rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Vælg brevskabelon</h3>
                <button type="button" @click="showSelectModal = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
            </div>

            <div class="max-h-96 overflow-y-auto space-y-2 py-2">
                @foreach ($breveList as $brev)
                    <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200/80 hover:bg-slate-50 transition">
                        <span class="text-xs font-bold text-slate-800">{{ $brev['titel'] }}</span>
                        <button
                            type="button"
                            wire:click="loadBrev({{ $brev['id'] }})"
                            @click="showSelectModal = false"
                            class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-xs cursor-pointer"
                        >
                            Vælg skabelon
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button
                    type="button"
                    @click="showSelectModal = false"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer"
                >
                    Luk
                </button>
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

    {{-- MODAL TIL OMDØBNING AF AKTIV SKABELON --}}
    <div 
        x-show="showRenameModal" 
        class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div @click.away="showRenameModal = false" class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Omdøb skabelon</h3>
                <button type="button" @click="showRenameModal = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Ny titel</label>
                <input
                    type="text"
                    x-model="editTitle"
                    class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs bg-slate-50 text-slate-800 focus:outline-none focus:border-indigo-500 font-medium"
                >
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button
                    type="button"
                    @click="showRenameModal = false"
                    class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer"
                >
                    Annuller
                </button>
                <button
                    type="button"
                    @click="$wire.updateBrevTitle({{$brevId ?? 0 }}, editTitle); showRenameModal = false;"
                    class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition shadow-sm cursor-pointer"
                >
                    Gem navn
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
                        ['today', 'aktiv', 'firmanavn', 'debitor_navn', 'ktr', 'debitor_email']
                    );
                @endphp
                @foreach ($standardTokens as $token)
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

<script>
function printPreview() {
    let previewEl = document.querySelector('.prose');
    if (!previewEl) {
        alert('Ingen forhåndsvisning fundet til udskrivning.');
        return;
    }
    let printContents = previewEl.innerHTML;
    let printWindow = window.open('', '_blank', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Udskriv brev</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: ui-sans-serif, system-ui, sans-serif; font-size: 14px; line-height: 1.6; color: #1e293b; padding: 40px; margin: 0; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin: 20px 0; }');
    printWindow.document.write('th, td { border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; }');
    printWindow.document.write('th { background-color: #f1f5f9; }');
    printWindow.document.write('@media print { body { padding: 0; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}
</script>