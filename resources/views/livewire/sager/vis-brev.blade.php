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

        {{-- Selve A4-arket --}}
        <div class="flex justify-center">
            <div 
                id="print-container" 
                class="bg-white shadow-xl rounded-lg w-full max-w-[210mm] min-h-[297mm] px-[35mm] py-[30mm] text-slate-800 font-sans text-sm leading-relaxed space-y-8 relative box-border"
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

                {{-- TOP SEKTION: 2-rækket rammefri tabel, hvor højre side starter hvor venstre side slutter --}}
                <table class="w-full text-xs sm:text-sm text-slate-700 font-sans" style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 20px;" cellspacing="0" cellpadding="0" border="0">
                    
                    {{-- RÆKKE 1: Kun modtageradresse i venstre side --}}
                    <tr style="border: none;">
                        <td style="width: 50%; vertical-align: top; text-align: left; padding: 20px; padding-bottom: 0; border: none;">
                            @if($visNavn || $visAdresse)
                                <div class="space-y-1">
                                    @if($visNavn)
                                        <div class="font-bold text-base text-slate-900">{{ $sag->debitor()->first()?->navn ?? '[ Eksempel Navn ]' }}</div>
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

                {{-- Emne / Overskrift --}}
                @if($visEmne && !empty($flettetEmne))
                    <div class="pt-2 pb-2">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                            {!! $flettetEmne !!}
                        </h3>
                    </div>
                @endif

                {{-- Brevtekst (Redigerbar med rigelig plads og luft) --}}
                <div class="pt-2">
                    <div 
                        contenteditable="true" 
                        @input="$wire.set('flettetTekst', $event.target.innerHTML, false)"
                        class="outline-none focus:ring-1 focus:ring-indigo-100 rounded-lg p-2 min-h-[450px] space-y-4"
                    >
                        {!! $flettetTekst ?? '<p>Ingen brevtekst tilgængelig...</p>' !!}
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>