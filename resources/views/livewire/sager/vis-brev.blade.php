<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start pb-12">

    {{-- KOLONNE 1: SKABELON-LISTE (Fast venstremenu i stedet for flydende boks) --}}
    <div class="lg:col-span-1 bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-2">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2 pb-2 border-b border-slate-100">
            Vælg brevskabelon
        </h3>
        <div class="space-y-1.5 pt-1">
            @foreach ($breveList as $brev)
                <button
                    type="button"
                    wire:click="loadBrev({{ $brev['id'] }})"
                    class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center justify-between {{ $selectedBrevId === $brev['id'] ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' }}"
                >
                    <span class="truncate">✉️ {{ $brev['titel'] }}</span>
                    @if($selectedBrevId === $brev['id'])
                        <span class="text-[10px] bg-indigo-500 text-white px-1.5 py-0.5 rounded">Aktiv</span>
                    @endif
                </button>
            @endforeach
        </div>
    </div>

    {{-- KOLONNE 2: A4-ARK OG UDSKRIV-KNAP (Højre side) --}}
    <div class="lg:col-span-3 space-y-4">
        
        {{-- Udskriv-knap placeret rent over arket --}}
        <div class="flex justify-end">
            <button type="button" onclick="printPreview()" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-2 shadow-sm">
                🖨️ Udskriv brev
            </button>
        </div>

       {{-- Selve A4-arket med fast størrelse, luft omkring og dyb skygge --}}
        <div class="flex justify-center p-4 sm:p-8 bg-slate-100/80 rounded-3xl overflow-x-auto">
            <div 
                id="print-container" 
                style="width: 210mm; height: 297mm;"
                class="bg-white shadow-2xl rounded-xl px-[35mm] py-[30mm] text-slate-800 font-sans text-sm leading-relaxed relative box-border overflow-hidden shrink-0 flex flex-col"
            >
                
                @php
                    $skjulAlt = $filter?->skjulalle ?? false;
                    $visAlt   = $filter?->visalle ?? false;

                    $visNavn    = $visAlt || (!$skjulAlt && ($filter?->navn ?? true));
                    $visAdresse = $visAlt || (!$skjulAlt && ($filter?->adresse ?? true));
                    $visDato    = $visAlt || (!$skjulAlt && ($filter?->dato ?? true));
                    $visSagsnr  = $visAlt || (!$skjulAlt && ($filter?->sagsnr ?? true));
                    $visEmne    = $visAlt || (!$skjulAlt && ($filter?->emne ?? true));
                @endphp

                <div class="space-y-6 flex-grow flex flex-col">
                    {{-- TOP SEKTION: 2-rækket rammefri tabel, hvor højre side starter hvor venstre side slutter --}}
                    <table class="w-full text-xs sm:text-sm text-slate-700 font-sans shrink-0" style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 10px;" cellspacing="0" cellpadding="0" border="0">
                        
                        {{-- RÆKKE 1: Kun modtageradresse i venstre side --}}
                        <tr style="border: none;">
                            <td style="width: 50%; vertical-align: top; text-align: left; padding: 10px; padding-bottom: 0; border: none;">
                                @if($visNavn || $visAdresse)
                                    <div class="space-y-1">
                                        @if($visNavn)
                                            <div class="text-base text-slate-900 font-medium">{{ $sag->debitor()->first()?->navn ?? '[ Eksempel Navn ]' }}</div>
                                        @endif
                                        @if($visAdresse)
                                            <div class="text-slate-600">{{ $sag->debitor()->first()?->adresse ?? '[ Eksempel Adresse ]' }}</div>
                                            <div class="text-slate-600">
                                                {{ $sag->debitor()->first()?->postnr ?? '[ Postnr ]' }} &nbsp; {{ $sag->debitor()->first()?->postnummer?->by ?? '[ By ]' }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-300 italic">[ Modtagerfelt skjult ]</span>
                                @endif
                            </td>
                            <td style="width: 50%; border: none;"></td>
                        </tr>

                        {{-- RÆKKE 2: Dato og Sagsnr i højre side --}}
                        <tr style="border: none;">
                            <td style="width: 50%; border: none;"></td>
                            <td style="width: 50%; vertical-align: top; text-align: right; padding: 10px; padding-top: {{ 10 + ($right_margin_top ?? 0) }}px; border: none;">
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

                    {{-- Emne / Overskrift --}}
                    @if($visEmne && !empty($flettetEmne))
                        <div class="pt-1 pb-1 shrink-0">
                            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                                {!! $flettetEmne !!}
                            </h3>
                        </div>
                    @endif

                    {{-- Brevtekst med WYSIWYG-editor (tilpasset til at fylde resten af A4-højden) --}}
                    <div class="pt-1 flex-grow flex flex-col">
                        <div class="flex-grow flex flex-col">
                            <x-wysiwyg-editor 
                                wire:key="editor-vis-brev-{{ $selectedBrevId ?? 'default' }}"
                                wireModel="flettetTekst" 
                                :value="$flettetTekst"
                                :marginTop="$margin_top ?? 0" 
                                :marginLeft="$margin_left ?? 0" 
                                :marginRight="$margin_right ?? 0"
                                :linjeafstand="$linjeafstand ?? '1.5'" 
                                :beskyttet="$beskyttet ?? false"
                                height="320px" 
                            />
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>