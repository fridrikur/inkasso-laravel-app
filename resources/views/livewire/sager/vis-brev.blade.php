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
            @php
                $navnTop        = str_replace(',', '.', ($brev->navn_top_margin ?: 5.45));
                $datoTop        = str_replace(',', '.', ($brev->dato_margin_top ?: 7.57));
                $emneTop        = str_replace(',', '.', ($brev->emne_top_margin ?: 8.91));
                $bodyTop        = str_replace(',', '.', ($brev->body_margin_top ?: ($body_margin_top ?? 2)));
                $brevhovedTop   = str_replace(',', '.', ($brev->brevhoved_top_margin ?: 1.50));
                $firmaLeft      = str_replace(',', '.', ($brev->firma_left_margin ?: 11.80));
                $firmaFontSize  = (float) str_replace(',', '.', ($brev->firma_font_size ?: 8.5));
                $firmaTitleSize = round($firmaFontSize * 1.55, 1);
            @endphp

            <div 
                id="print-container" 
                wire:key="print-ark-{{ $selectedBrevId ?? 'default' }}-{{ $navnTop }}-{{ $datoTop }}-{{ $emneTop }}-{{ $bodyTop }}-{{ $brevhovedTop }}-{{ $firmaLeft }}-{{ $firmaFontSize }}"
                style="width: 210mm; height: 297mm; font-family: {!! $brev->font_family ?? 'Century Schoolbook, Georgia, serif' !!};"
                class="bg-white shadow-2xl rounded-xl p-[25mm] text-slate-800 text-sm leading-relaxed relative box-border overflow-hidden shrink-0 mx-auto"
            >
                {{-- 0. FIRMA-BREVHOVED (Styres af visfirmabrevhoved, brevhoved_top_margin, firma_left_margin og firma_font_size) --}}
                @if($brev->visfirmabrevhoved ?? true)
                    <div
                        style="
                            position: absolute;
                            top: {{ $brevhovedTop }}cm;
                            left: {{ $firmaLeft }}cm;
                            border-left: 2.5px solid #1e293b;
                            padding-left: 10px;
                            font-family: Arial, Helvetica, sans-serif;
                            line-height: 1.35;
                        "
                        class="text-left text-slate-900"
                    >
                        <div
                            style="font-size: {{ $firmaTitleSize }}pt; line-height: 1.15; letter-spacing: -0.01em;"
                            class="font-extrabold uppercase text-slate-950 mb-1.5"
                        >
                            {{ $brev->firma_navn ?: 'DANSK KONSULENT GRUPPE A/S' }}
                        </div>

                        <table class="firma-brevhoved-tabel" style="width: auto; border-collapse: collapse !important; border: none !important; font-size: {{ $firmaFontSize }}pt;" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="font-size: {{ $firmaFontSize }}pt; padding-right: 14px;" class="font-bold uppercase text-slate-800 pb-1.5 align-top whitespace-nowrap">ADRESSE:</td>
                                <td style="font-size: {{ $firmaFontSize }}pt;" class="uppercase text-slate-900 pb-1.5 align-top">{{ $brev->firma_adresse ?? ($this->firma_adresse ?? 'SCT. HANSGADE 9, 1 SAL, 4100 RINGSTED') }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $firmaFontSize }}pt; padding-right: 14px;" class="font-bold uppercase text-slate-800 align-top whitespace-nowrap">TELEFON:</td>
                                <td style="font-size: {{ $firmaFontSize }}pt;" class="uppercase text-slate-900 align-top">{{ $brev->firma_telefon ?? ($this->firma_telefon ?? '+45 22 22 68 94') }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $firmaFontSize }}pt; padding-right: 14px;" class="font-bold uppercase text-slate-800 align-top whitespace-nowrap">MAIL:</td>
                                <td style="font-size: {{ $firmaFontSize }}pt;" class="uppercase text-slate-900 align-top">{{ $brev->firma_mail ?? ($this->firma_mail ?? 'DKG@DKG-APS.DK') }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $firmaFontSize }}pt; padding-right: 14px;" class="font-bold uppercase text-slate-800 pb-1.5 align-top whitespace-nowrap">WEB:</td>
                                <td style="font-size: {{ $firmaFontSize }}pt;" class="uppercase text-slate-900 pb-1.5 align-top">{{ $brev->firma_web ?? ($this->firma_web ?? 'WWW.DKG-APS.DK') }}</td>
                            </tr>
                            <tr>
                                <td style="font-size: {{ $firmaFontSize }}pt; padding-right: 14px;" class="font-bold uppercase text-slate-800 align-top whitespace-nowrap">CVR. NR.:</td>
                                <td style="font-size: {{ $firmaFontSize }}pt;" class="uppercase text-slate-900 align-top">{{ $brev->firma_cvr ?? ($this->firma_cvr ?? '21 24 72 86') }}</td>
                            </tr>
                        </table>
                    </div>
                @endif

                {{-- 1. ADRESSE / NAVN (Placeret præcist på navn_top_margin fra toppen) --}}
                <div style="position: absolute; top: {{ $navnTop }}cm; left: 25mm; right: 25mm;">
                    @if($this->visNavn || $this->visAdresse)
                        <div class="space-y-1">
                            @if($this->visNavn)
                                <div class="text-slate-900 font-medium" style="font-size: {{ $brev->navn_font_size ?? 9.5 }}pt;">
                                    {{ $sag->debitor()->first()?->navn ?? '[ Eksempel Navn ]' }}
                                </div>
                            @endif
                            @if($this->visAdresse)
                                <div class="text-slate-600" style="font-size: {{ $brev->navn_font_size ?? 9.5 }}pt;">
                                    {{ $sag->debitor()->first()?->adresse ?? '[ Eksempel Adresse ]' }}<br>
                                    {{ $sag->debitor()->first()?->postnr ?? '[ Postnr ]' }}   {{ $sag->debitor()->first()?->postnummer?->by ?? '[ By ]' }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 2. DATO OG SAGSNR (Placeret præcist på dato_margin_top fra toppen i højre side) --}}
                <div style="position: absolute; top: {{ $datoTop }}cm; right: 25mm; text-align: right;">
                    @if($this->visDato || $this->visSagsnr)
                        <div class="space-y-1 font-medium text-slate-900" style="font-size: {{ $brev->navn_font_size ?? 9.5 }}pt; line-height: 1.4;">
                            @if($this->visDato)
                                <div><strong>Dato:</strong> {{ $sag->dato ? \Carbon\Carbon::parse($sag->dato)->format('d.m.Y') : date('d.m.Y') }}</div>
                            @endif
                            @if($this->visSagsnr)
                                <div><strong>Sags nr.:</strong> {{ $sag->sagsnr ?? '123456' }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 3 + 4. EMNE OG BREVTEKST (Emne på emne_top_margin, og brevtekst i normalt flow med margin-top i em) --}}
                <div style="position: absolute; top: {{ $emneTop }}cm; left: 25mm; right: 25mm; bottom: 20mm;" class="flex flex-col">
                    @if($this->visEmne && !empty($flettetEmne))
                        <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2" style="font-size: {{ $brev->emne_font_size ?? 10.5 }}pt;">
                            {!! $flettetEmne !!}
                        </h3>
                    @endif

                    <div 
                        x-data="{ redigerTekst: false }"
                        style="margin-top: {{ $bodyTop }}em;" 
                        class="flex-1 flex flex-col group"
                    >
                        {{-- LÆSEVISNING (Standard: rent A4-udtryk uden værktøjslinje) --}}
                        <div 
                            wire:key="laesevisning-{{ $selectedBrevId ?? 'default' }}-{{ md5($flettetTekst ?? '') }}"
                            x-show="!redigerTekst"
                            @click="redigerTekst = true"
                            title="Klik for at redigere brevteksten"
                            style="
                                font-size: {{ $brev->body_font_size ?? 9.5 }}pt;
                                font-family: {!! $brev->font_family ?? 'Century Schoolbook, Georgia, serif' !!};
                                line-height: {{ $brev->linjeafstand ?? 1.5 }};
                            "
                            class="prose prose-sm max-w-none text-slate-900 cursor-pointer rounded-lg transition hover:ring-1 hover:ring-indigo-300/80 hover:bg-indigo-50/10 p-1 -m-1 relative"
                        >
                            {{-- Diskret svævende knap ved hover (skjules automatisk ved print) --}}
                            <button 
                                type="button"
                                @click.stop="redigerTekst = true"
                                class="print:hidden opacity-0 group-hover:opacity-100 transition-opacity absolute -top-7 right-0 bg-slate-800 text-white text-[11px] font-sans font-semibold px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1.5 cursor-pointer"
                            >
                                ✏️ Rediger tekst
                            </button>

                            {!! $flettetTekst !!}
                        </div>

                        {{-- REDIGERINGSVISNING (Vises kun når brugeren klikker for at redigere) --}}
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
                                wire:key="editor-vis-brev-{{ $selectedBrevId ?? 'default' }}"
                                wireModel="flettetTekst" 
                                :value="$flettetTekst"
                                :bodyMarginTop="0"
                                :bodyFontSize="$brev->body_font_size ?? 9.5"
                                :fontFamily="$brev->font_family ?? 'Century Schoolbook, Georgia, serif'"
                                :linjeafstand="$brev->linjeafstand ?? 1.5" 
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