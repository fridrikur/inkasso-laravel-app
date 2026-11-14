@props([
    'wireModel' => 'tekst',
    'value' => '',
    'marginTop' => 0,
    'marginLeft' => 0,
    'marginRight' => 0,
    'linjeafstand' => 1.5,
    'height' => '550px',
    'beskyttet' => false
])

<div {{ $attributes->merge(['class' => 'flex flex-col relative']) }} x-data="{
    showHtml: false,
    htmlContent: @js($value),
    format(command) {
        if (@js((bool) $beskyttet)) {
            alert('🚫 Adgang forbudt: Denne brevskabelon er beskyttet og kan ikke formateres.');
            return;
        }
        document.execCommand(command, false, null);
        this.$refs.editor.dispatchEvent(new Event('input'));
    },
    toggleHtmlCode() {
        if (@js((bool) $beskyttet)) {
            alert('🚫 Adgang forbudt: Denne brevskabelon er beskyttet.');
            return;
        }
        if (this.showHtml) {
            // Skifter tilbage til WYSIWYG: synkroniser HTML til editoren
            this.$refs.editor.innerHTML = this.htmlContent;
            this.$refs.editor.dispatchEvent(new Event('input'));
        } else {
            // Skifter til kodevisning: hent opdateret HTML fra editoren
            this.htmlContent = this.$refs.editor.innerHTML;
        }
        this.showHtml = !this.showHtml;
    },
    updateFromTextarea() {
        this.$refs.editor.innerHTML = this.htmlContent;
        this.$refs.editor.dispatchEvent(new Event('input'));
    }
}">
    {{-- WYSIWYG Værktøjslinje --}}
    <div class="flex items-center justify-between bg-slate-100 border border-slate-200 border-b-0 rounded-t-xl px-3 py-2 flex-wrap gap-2">
        <div class="flex items-center gap-1 flex-wrap">
            <button type="button" @click.prevent="format('bold')" class="px-2.5 py-1 bg-white hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold border border-slate-300 shadow-2xs {{ $beskyttet ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}" title="Fed"><b>B</b></button>
            <button type="button" @click.prevent="format('italic')" class="px-2.5 py-1 bg-white hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-italic border border-slate-300 shadow-2xs {{ $beskyttet ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}" title="Kursiv"><i>I</i></button>
            <button type="button" @click.prevent="format('underline')" class="px-2.5 py-1 bg-white hover:bg-slate-200 text-slate-800 rounded-lg text-xs underline border border-slate-300 shadow-2xs {{ $beskyttet ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}" title="Understreget"><u>U</u></button>
            <div class="h-4 w-px bg-slate-300 mx-1"></div>
            <button type="button" @click.prevent="format('insertUnorderedList')" class="px-2.5 py-1 bg-white hover:bg-slate-200 text-slate-800 rounded-lg text-xs font-bold border border-slate-300 shadow-2xs {{ $beskyttet ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}" title="Punktopstilling">• List</button>
            
            {{-- NY: Vis kode knap placeret lige ved siden af List-knappen --}}
            <button 
                type="button" 
                @click.prevent="toggleHtmlCode()" 
                class="px-3 py-1 rounded-lg text-xs font-bold border shadow-2xs cursor-pointer transition flex items-center gap-1.5"
                :class="showHtml ? 'bg-indigo-600 text-white border-indigo-700' : 'bg-white hover:bg-slate-200 text-slate-800 border-slate-300'"
                title="Vis eller skjul rå HTML-kode"
            >
                💻 <span x-text="showHtml ? 'Vis brev' : 'Vis kode'"></span>
            </button>
        </div>

        <div>
            @if($beskyttet)
                <span class="text-xs text-rose-600 font-bold px-2.5 py-1 bg-rose-50 rounded-lg border border-rose-200 flex items-center gap-1 shadow-xs animate-pulse">
                    🚫 SKRIVEBESKYTTET (ADGANG NÆGTET)
                </span>
            @endif
        </div>
    </div>

    {{-- 1. Visuel WYSIWYG Editor (vises som standard) --}}
    <div 
        x-show="!showHtml"
        x-ref="editor"
        contenteditable="{{ $beskyttet ? 'false' : 'true' }}" 
        @if(!$beskyttet) @input="$wire.set('{{ $wireModel }}', $refs.editor.innerHTML, false)" @endif
        @if($beskyttet) @click="alert('🚫 Adgang forbudt: Denne brevskabelon er beskyttet mod ændringer.');" @endif
        class="font-sans text-xs sm:text-sm outline-none border border-slate-200 rounded-b-xl p-4 overflow-y-auto transition-all {{ $beskyttet ? 'bg-slate-100 text-slate-500 cursor-not-allowed select-none opacity-90' : 'bg-slate-50/50 text-slate-900' }}"
        style="height: {{ $height }}; margin-top: {{ $marginTop ?? 0 }}px; margin-left: {{ $marginLeft ?? 0 }}px; margin-right: {{ $marginRight ?? 0 }}px; line-height: {{ $linjeafstand ?? 1.5 }};"
    >{!! $value !!}</div>

    {{-- 2. Rå HTML-kodevisning (aktiveres når man klikker på 'Vis kode') --}}
    <textarea 
        x-show="showHtml"
        x-model="htmlContent"
        @input="updateFromTextarea()"
        class="font-mono text-xs sm:text-sm outline-none border border-slate-200 rounded-b-xl p-4 overflow-y-auto bg-slate-900 text-emerald-400 transition-all resize-none shadow-inner"
        style="height: {{ $height }}; margin-top: {{ $marginTop ?? 0 }}px; margin-left: {{ $marginLeft ?? 0 }}px; margin-right: {{ $marginRight ?? 0 }}px; line-height: 1.5;"
    ></textarea>
</div>