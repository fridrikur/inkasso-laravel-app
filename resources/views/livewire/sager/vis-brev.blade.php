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
                // Sikrer at komma erstattes med punktum til CSS (f.eks. "5,45" -> "5.45")
                $navnTop  = str_replace(',', '.', $brev->navn_top_margin ?? 5.45);
                $datoTop  = str_replace(',', '.', $brev->dato_margin_top ?? 7.57);
                $emneTop  = str_replace(',', '.', $brev->emne_top_margin ?? 8.91);
                $bodyTop  = str_replace(',', '.', $brev->body_margin_top ?? 1.00);
            @endphp

            <div 
                id="print-container" 
                style="width: 210mm; height: 297mm; font-family: {!! $brev->font_family ?? 'Century Schoolbook, Georgia, serif' !!};"
                class="bg-white shadow-2xl rounded-xl p-[25mm] text-slate-800 text-sm leading-relaxed relative box-border overflow-hidden shrink-0 mx-auto"
            >
                
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
                                    {{ $sag->debitor()->first()?->postnr ?? '[ Postnr ]' }} &nbsp; {{ $sag->debitor()->first()?->postnummer?->by ?? '[ By ]' }}
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
                                <div><strong>Dato:</strong> {{ $sag->dato ?? date('d.m.Y') }}</div>
                            @endif
                            @if($this->visSagsnr)
                                <div><strong>J. nr.:</strong> {{ $sag->sagsnr ?? '123456' }}</div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 3. EMNE (Placeret præcist på emne_top_margin fra toppen) --}}
                @if($this->visEmne && !empty($flettetEmne))
                    <div style="position: absolute; top: {{ $emneTop }}cm; left: 25mm; right: 25mm;">
                        <h3 class="font-bold text-slate-900 border-b border-slate-100 pb-2" style="font-size: {{ $brev->emne_font_size ?? 10.5 }}pt;">
                            {!! $flettetEmne !!}
                        </h3>
                    </div>
                @endif

                {{-- 4. BREVTEKST / WYSIWYG-EDITOR (Placeret relativt lige efter emnet vha. body_margin_top som afstand) --}}
                @php
                    // Beregner pladsen ud fra emnets top-position + ca. 1.2 cm til selve emne-overskriften + body_margin_top
                    $calculatedBodyTop = (float)$emneTop + (float)$bodyTop + 1.2;
                @endphp

                <div style="position: absolute; top: {{ $calculatedBodyTop }}cm; left: 25mm; right: 25mm; bottom: 20mm;" class="flex flex-col">
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
        @endif

</div>