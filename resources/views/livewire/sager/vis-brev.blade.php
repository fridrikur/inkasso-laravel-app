<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start pb-12">

    {{-- KOLONNE 1: SKABELON-LISTE (Fast venstremenu) --}}
    <div class="lg:col-span-1 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-2">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2 pb-2 border-b border-slate-100">
            Vælg brevskabelon
        </h3>
        <div class="space-y-1.5 pt-1">
            @foreach ($breveList as $brevItem)
                <button
                    type="button"
                    wire:click="loadBrev({{ $brevItem['id'] }})"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center justify-between {{ $selectedBrevId === $brevItem['id'] ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}"
                >
                    <span class="truncate">✉️ {{ $brevItem['titel'] }}</span>
                    @if($selectedBrevId === $brevItem['id'])
                        <span class="text-[10px] bg-indigo-500 text-white px-1.5 py-0.5 rounded">Aktiv</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- KOLONNE 2: A4-ARK OG UDSKRIV-KNAP (Højre side) --}}
    <div class="lg:col-span-3 space-y-4">
        
        {{-- Udskriv-knap --}}
        <div class="flex justify-end">
            <button type="button" onclick="printPreview()" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 shadow-sm">
                🖨️ Udskriv brev
            </button>
        </div>

        {{-- Selve A4-arket --}}
        @if($brev)
            <div 
                id="print-container" 
                wire:key="print-ark-{{ $selectedBrevId ?? 'default' }}-{{ $this->navnTop }}-{{ $this->datoTop }}-{{ $this->emneTop }}-{{ $this->subEmneTop }}-{{ $this->bodyTop }}-{{ $this->brevhovedTop }}-{{ $this->firmaLeft }}-{{ $this->firmaFontSize }}-{{ md5($this->fontColor) }}"
                style="width: 210mm; height: 297mm; font-family: {{ $this->fontFamily }}; color: {{ $this->fontColor }};"
                class="bg-white shadow-2xl rounded-xl p-[25mm] text-sm leading-relaxed relative box-border overflow-hidden shrink-0 mx-auto"
            >
                {{-- 0. FIRMA-BREVHOVED --}}
                @if($brev->visfirmabrevhoved ?? true)
                    <div
                        style="
                            position: absolute;
                            top: {{ $this->brevhovedTop }}cm;
                            left: {{ $this->firmaLeft }}cm;
                            border-left: 2.5px solid #1e293b;
                            padding-left: 10px;
                            font-family: Arial, Helvetica, sans-serif;
                            line-height: 1.35;
                            color: {{ $this->fontColor }};
                        "
                        class="text-left"
                    >
                        <div
                            style="font-size: {{ $this->firmaTitleSize }}pt; line-height: 1.15; letter-spacing: -0.01em; color: {{ $this->fontColor }};"
                            class="font-extrabold uppercase mb-1.5"
                        >
                            {{ $brev->firma_navn ?: 'DANSK KONSULENT GRUPPE A/S' }}
                        </div>

                        <table class="firma-brevhoved-tabel" style="width: auto; border-collapse: collapse !important; border: none !important; font-size: {{ $this->firmaFontSize }}pt; color: {{ $this->fontColor }};" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; padding-right: 14px; color: {{ $this->fontColor }};" class="font-bold uppercase pb-1.5 align-top whitespace-nowrap">ADRESSE:</td>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; color: {{ $this->fontColor }};" class="uppercase pb-1.5 align-top">{{ $brev->firma_adresse ?? 'SCT. HANSGADE 9, 1 SAL, 4100 RINGSTED' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; padding-right: 14px; color: {{ $this->fontColor }};" class="font-bold uppercase align-top whitespace-nowrap">TELEFON:</td>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; color: {{ $this->fontColor }};" class="uppercase align-top">{{ $brev->firma_telefon ?? '+45 22 22 68 94' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; padding-right: 14px; color: {{ $this->fontColor }};" class="font-bold uppercase align-top whitespace-nowrap">MAIL:</td>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; color: {{ $this->fontColor }};" class="uppercase align-top">{{ $brev->firma_mail ?? 'DKG@DKG-APS.DK' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; padding-right: 14px; color: {{ $this->fontColor }};" class="font-bold uppercase pb-1.5 align-top whitespace-nowrap">WEB:</td>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; color: {{ $this->fontColor }};" class="uppercase pb-1.5 align-top">{{ $brev->firma_web ?? 'WWW.DKG-APS.DK' }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; padding-right: 14px; color: {{ $this->fontColor }};" class="font-bold uppercase align-top whitespace-nowrap">CVR. NR.:</td>
                                <td style="font-size: {{ $this->firmaFontSize }}pt; color: {{ $this->fontColor }};" class="uppercase align-top">{{ $brev->firma_cvr ?? '21 24 72 86' }}</td>
                            </tr>
                        </table>
                    </div>
                @endif

                {{-- 1. ADRESSE / NAVN --}}
                <div style="position: absolute; top: {{ $this->navnTop }}cm; left: 25mm; right: 25mm;">
                    @if($this->visNavn || $this->visAdresse)
                        <div class="space-y-1">
                            @if($this->visNavn)
                                <div class="font-medium" style="font-size: {{ $brev->navn_font_size ?? 9.5 }}pt; color: {{ $this->fontColor }};">
                                    {{ $sag->debitor()->first()?->navn ?? '[ Eksempel Navn ]' }}
                                </div>
                            @endif
                            @if($this->visAdresse)
                                <div style="font-size: {{ $brev->navn_font_size ?? 9.5 }}pt; color: {{ $this->fontColor }};">
                                    {{ $sag->debitor()->first()?->adresse ?? '[ Eksempel Adresse ]' }}<br>
                                    {{ $sag->debitor()->first()?->postnr ?? '[ Postnr ]' }} &nbsp; {{ $sag->debitor()->first()?->postnummer?->by ?? '[ By ]' }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 2. DATO OG SAGSNR --}}
                <div style="position: absolute; top: {{ $this->datoTop }}cm; right: 25mm; text-align: right;">
                    @if($this->visDato || $this->visSagsnr)
                        <div class="space-y-1 font-medium" style="font-size: {{ $brev->navn_font_size ?? 9.5 }}pt; line-height: 1.4; color: {{ $this->fontColor }};">
                            @if($this->visDato)
                                <div><strong>Dato:</strong> {{ $sag->dato ? \Carbon\Carbon::parse($sag->dato)->format('d.m.Y') : date('d.m.Y') }}</div>
                            @endif
                            @if($this->visSagsnr)
                                <div><strong>Sags nr.:</strong> {{ $sag->sagsnr ?? '123456' }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 3 + 4. EMNE, SUB-EMNE OG BREVTEKST --}}
                <div style="position: absolute; top: {{ $this->emneTop }}cm; left: 25mm; right: 25mm; bottom: 20mm;" class="flex flex-col">
                    @if($this->visEmne)
                        {{-- SAMLET DIV TIL EMNE (H3) OG SUB-EMNE --}}
                        <div
                            x-data="{ redigerEmne: false, redigerSubEmne: false }"
                            class="border-b border-slate-100 pb-1.5"
                            style="color: {{ $this->fontColor }};"
                        >
                            <h3
                                x-show="!redigerEmne"
                                @click="redigerEmne = true; $nextTick(() => $refs.visEmneInput?.focus())"
                                title="Klik for at ændre emne til udskrift"
                                class="m-0 font-bold cursor-pointer rounded px-1 -mx-1 transition hover:ring-1 hover:ring-indigo-300/80 hover:bg-indigo-50/10"
                                style="font-size: {{ $this->emneFontSize }}pt; line-height: 1.2; color: {{ $this->fontColor }};"
                            >
                                @if(filled($flettetEmne))
                                    {{ $flettetEmne }}
                                @else
                                    <span class="print:hidden text-slate-400 italic font-normal text-xs font-sans">[ Klik for at skrive emne... ]</span>
                                @endif
                            </h3>

                            <div x-show="redigerEmne" x-cloak class="pt-1 print:hidden">
                                <input
                                    x-ref="visEmneInput"
                                    type="text"
                                    wire:model.live.debounce.250ms="flettetEmne"
                                    @blur="redigerEmne = false"
                                    @keydown.enter.prevent="redigerEmne = false"
                                    @keydown.escape="redigerEmne = false"
                                    placeholder="Skriv emne til udskrift..."
                                    class="w-full border border-indigo-300 rounded-lg px-3 py-1.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900 font-sans"
                                >
                            </div>

                            <div
                                x-show="!redigerSubEmne"
                                @click="redigerSubEmne = true; $nextTick(() => $refs.visSubEmneInput?.focus())"
                                title="Klik for at ændre sub-emne til udskrift"
                                class="font-bold cursor-pointer rounded px-1 -mx-1 transition hover:ring-1 hover:ring-indigo-300/80 hover:bg-indigo-50/10 {{ filled($flettetSubEmne) ? '' : 'print:hidden' }}"
                                style="margin-top: {{ $this->subEmneTop }}cm; font-size: {{ $this->emneFontSize }}pt; line-height: 1.2; color: {{ $this->fontColor }};"
                            >
                                @if(filled($flettetSubEmne))
                                    {{ $flettetSubEmne }}
                                @else
                                    <span class="text-slate-400 italic font-normal text-xs font-sans">[ Klik for at tilføje sub-emne til udskrift... ]</span>
                                @endif
                            </div>

                            <div x-show="redigerSubEmne" x-cloak class="pt-1 print:hidden">
                                <input
                                    x-ref="visSubEmneInput"
                                    type="text"
                                    wire:model.live.debounce.250ms="flettetSubEmne"
                                    @input="$event.target.value = $event.target.value.replace(/<[^>]*>?/gm, '')"
                                    @blur="redigerSubEmne = false"
                                    @keydown.enter.prevent="redigerSubEmne = false"
                                    @keydown.escape="redigerSubEmne = false"
                                    placeholder="Skriv sub-emne til udskrift..."
                                    class="w-full border border-indigo-300 rounded-lg px-3 py-1.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900 font-sans"
                                >
                            </div>
                        </div>
                    @endif

                    {{-- 4. BREVTEKST (Spontan inline-redigering) --}}
                    <div 
                        x-data="{ redigerTekst: false }"
                        style="margin-top: {{ $this->bodyTop }}em;" 
                        class="flex-1 flex flex-col group"
                    >
                        {{-- LÆSEVISNING --}}
                        <div 
                            wire:key="laesevisning-{{ $selectedBrevId ?? 'default' }}-{{ md5($flettetTekst ?? '') }}-{{ md5($this->fontColor) }}"
                            x-show="!redigerTekst"
                            @click="redigerTekst = true"
                            title="Klik for at redigere brevteksten"
                            style="
                                font-size: {{ $this->bodyFontSize }}pt;
                                font-family: {{ $this->fontFamily }};
                                line-height: {{ $this->linjeafstand }};
                                color: {{ $this->fontColor }};
                            "
                            class="prose prose-sm max-w-none cursor-pointer rounded-lg transition hover:ring-1 hover:ring-indigo-300/80 hover:bg-indigo-50/10 p-1 -m-1 relative"
                        >
                            <button 
                                type="button"
                                @click.stop="redigerTekst = true"
                                class="print:hidden opacity-0 group-hover:opacity-100 transition-opacity absolute -top-7 right-0 bg-slate-800 text-white text-[11px] font-sans font-semibold px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5 cursor-pointer"
                            >
                                ✏️ Rediger tekst
                            </button>

                            {!! $flettetTekst !!}
                        </div>

                        {{-- REDIGERINGSVISNING --}}
                        <div 
                            x-show="redigerTekst" 
                            style="display: none;" 
                            class="flex-1 flex flex-col print:hidden"
                        >
                            <div class="flex justify-end mb-1.5">
                                <button 
                                    type="button" 
                                    @click="
                                        const editorEl = $el.closest('[x-data]').querySelector('[contenteditable], textarea, .ql-editor, .ProseMirror');
                                        if (editorEl) editorEl.dispatchEvent(new Event('blur', { bubbles: true }));
                                        setTimeout(() => {
                                            $wire.$refresh().then(() => { redigerTekst = false; });
                                        }, 120);
                                    "
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-sans font-bold px-3 py-1 rounded-lg shadow-sm transition cursor-pointer flex items-center gap-1"
                                >
                                    ✓ Afslut redigering
                                </button>
                            </div>

                            <x-wysiwyg-editor 
                                wire:key="editor-vis-brev-{{ $selectedBrevId ?? 'default' }}-{{ md5($this->fontColor) }}"
                                wireModel="flettetTekst" 
                                :value="$flettetTekst"
                                :bodyMarginTop="0"
                                :bodyFontSize="$this->bodyFontSize"
                                :fontFamily="$this->fontFamily"
                                :fontColor="$this->fontColor"
                                :linjeafstand="$this->linjeafstand" 
                                :beskyttet="false"
                                height="100%" 
                            />
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>