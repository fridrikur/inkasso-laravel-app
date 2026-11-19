<div class="relative pb-24">

    {{-- 1. FANER ØVERST --}}
    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-1 flex-1">
            @foreach ($breveList as $brev)
                <button
                    type="button"
                    wire:click="loadBrev({{ $brev['id'] }})"
                    class="px-4 py-2 rounded-t-xl rounded-b-none text-xs font-bold transition cursor-pointer whitespace-nowrap flex items-center gap-2 border-t border-x {{ $brevId === $brev['id'] ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}"
                >
                    <span>✉️</span> {{ $brev['titel'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- 2. SKABELONINDHOLD --}}
    <div class="bg-white border border-slate-200 rounded-b-2xl rounded-tr-2xl p-6 shadow-sm flex flex-col space-y-4" style="border:1px solid blue;height:100%">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Emne-felt --}}
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Emne / overskrift:</label>
                <input 
                    type="text" 
                    wire:model="emne" 
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900" 
                    placeholder="Skriv emne..."
                >
            </div>

            {{-- Left Margin --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Skub indholdet til højre eller venstre">Venstre-margin (px):</label>
                <input type="number" wire:model.live="margin_left" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
            </div>

            {{-- Right Margin --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Juster højre margin">Højre-margin (px):</label>
                <input type="number" wire:model.live="margin_right" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5" title="Afstand mellem linjer (f.eks. 1.2, 1.5, 2.0)">Linjeafstand:</label>
                <select wire:model.live="linjeafstand" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-slate-50/50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900 cursor-pointer">
                    <option value="1.0">Tæt (1.0)</option>
                    <option value="1.2">Normal (1.2)</option>
                    <option value="1.3">Standard brev (1.3)</option>
                    <option value="1.5">Luftig (1.5)</option>
                    <option value="2.0">Dobbelt (2.0)</option>
                </select>
            </div>

            {{-- Beskyttet Checkbox --}}
            <div class="flex flex-col justify-end md:col-span-2 gap-2">
                <label class="flex items-center gap-2 h-[42px] px-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                    <input type="checkbox" wire:model.live="beskyttet" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4 cursor-pointer">
                    <span class="text-xs font-bold text-slate-700">🔒 Beskyttet</span>
                </label>
                <button 
                    type="button" 
                    wire:click="nulstilAlleMarginer" 
                    wire:confirm="Er du sikker på, at du vil nulstille marginer til 0 på ALLE brevskabeloner?"
                    class="bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 w-fit"
                >
                    🔄 Nulstil alle marginer (Globalt)
                </button>
            </div>
        </div>

        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 space-y-3">
            <h4 class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Firmaoplysninger i brevhoved</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Firmanavn</label>
                    <input type="text" wire:model.live="firma_navn" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white focus:outline-none focus:border-indigo-500 font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Adresse</label>
                    <input type="text" wire:model.live="firma_adresse" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white focus:outline-none focus:border-indigo-500 font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Telefon</label>
                    <input type="text" wire:model.live="firma_telefon" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white focus:outline-none focus:border-indigo-500 font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mail</label>
                    <input type="text" wire:model.live="firma_mail" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white focus:outline-none focus:border-indigo-500 font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Web</label>
                    <input type="text" wire:model.live="firma_web" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white focus:outline-none focus:border-indigo-500 font-semibold text-slate-900">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">CVR. nr.</label>
                    <input type="text" wire:model.live="firma_cvr" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-white focus:outline-none focus:border-indigo-500 font-semibold text-slate-900">
                </div>
            </div>
        </div>
        
        {{-- AVANCEREDE LAYOUT- OG SKRIFTINDSTILLINGER --}}
        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-4 space-y-4">
            <h3 class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Avancerede layout- og skriftindstillinger</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- 1. Navn top-margin --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Navn top-margin (cm):</label>
                    <input 
                        type="text" 
                        inputmode="decimal"
                        wire:model.live="navn_top_margin" 
                        placeholder="5.45" 
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900"
                    >
                </div>

                {{-- 2. Dato top-margin --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dato top-margin (cm):</label>
                    <input 
                        type="text" 
                        inputmode="decimal"
                        wire:model.live="dato_margin_top" 
                        placeholder="7.57" 
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900"
                    >
                </div>

                {{-- 3. Emne top-margin --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Emne top-margin (cm):</label>
                    <input 
                        type="text" 
                        inputmode="decimal"
                        wire:model.live="emne_top_margin" 
                        placeholder="8.91" 
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900"
                    >
                </div>

                {{-- 4. Body top-margin (afstand under emne i em) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Body top-margin (em):</label>
                    <input 
                        type="text" 
                        inputmode="decimal"
                        wire:model.live="body_margin_top" 
                        placeholder="2" 
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Brevhoved top-margin (cm):</label>
                    <input 
                        type="text" 
                        inputmode="decimal"
                        wire:model.live="brevhoved_top_margin" 
                        placeholder="1.50" 
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900"
                    >
                </div>
            
                {{-- Skrifttype --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Skrifttype:</label>
                    <select wire:model.live="font_family" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900 cursor-pointer">
                        <option value="'Century Schoolbook', Georgia, serif">Century Schoolbook</option>
                        <option value="'Times New Roman', Times, serif">Times New Roman</option>
                        <option value="Arial, Helvetica, sans-serif">Arial</option>
                        <option value="Calibri, sans-serif">Calibri</option>
                    </select>
                </div>

                {{-- Navn skriftstørrelse --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Navn skriftstørrelse (pt):</label>
                    <input type="number" step="0.5" wire:model.live="navn_font_size" placeholder="9.5" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
                </div>

                {{-- Emne skriftstørrelse --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Emne skriftstørrelse (pt):</label>
                    <input type="number" step="0.5" wire:model.live="emne_font_size" placeholder="10.5" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
                </div>

                {{-- Body skriftstørrelse --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Body skriftstørrelse (pt):</label>
                    <input type="number" step="0.5" wire:model.live="body_font_size" placeholder="9.5" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
                </div>
                {{-- Brevhoved venstre-margin (cm) --}}
<div>
    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Brevhoved venstre-margin (cm):</label>
    <input
        type="text"
        inputmode="decimal"
        wire:model.live="firma_left_margin"
        placeholder="11.80"
        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900"
    >
</div>

{{-- Brevhoved skriftstørrelse (pt) --}}
<div>
    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Brevhoved skriftstørrelse (pt):</label>
    <input
        type="number"
        step="0.5"
        wire:model.live="firma_font_size"
        placeholder="8.5"
        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs sm:text-sm bg-white focus:outline-none focus:border-indigo-500 font-bold text-slate-900"
    >
</div>
            </div>
        </div>

        {{-- OVERSKRIFT OG BREVHOVED-KNAP SAMLET --}}
        <div class="flex items-center justify-between pb-2 pt-2">
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Skabelonens layout og indhold:</label>
            
            {{-- Brevhoved Indstillinger (Dropdown) --}}
            <div class="relative" x-data="{ openHeaderMenu: false }">
                <button 
                    type="button" 
                    @if(!$beskyttet) @click="openHeaderMenu = !openHeaderMenu" @else @click="alert('🚫 Denne skabelon er beskyttet. Brevhovedet kan ikke ændres.');" @endif
                    class="bg-white text-slate-800 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-2xs {{ $beskyttet ? 'opacity-60 cursor-not-allowed bg-slate-100' : 'hover:bg-slate-100 cursor-pointer' }}"
                >
                    <span>📄 Brevhoved indstillinger</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ ($filter['skjulalle'] ?? false) ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $beskyttet ? 'Låst' : (($filter['skjulalle'] ?? false) ? 'Skjult' : 'Aktiv') }}
                    </span>
                </button>

                {{-- Dropdown boks (Kun tilgængelig hvis ikke beskyttet) --}}
                @if(!$beskyttet)
                    <div 
                        x-show="openHeaderMenu" 
                        @click.outside="openHeaderMenu = false"
                        class="absolute right-0 mt-2 w-64 bg-white border border-slate-200 rounded-xl shadow-xl p-4 z-50 space-y-3 text-xs"
                        style="display: none;"
                    >
                        <div class="font-bold text-slate-700 border-b border-slate-100 pb-2 flex justify-between items-center gap-2">
                            <span>Styr brevhoved visning</span>
                            <label class="flex items-center gap-1.5 cursor-pointer font-medium text-slate-800">
                                <input
                                    type="checkbox"
                                    wire:model.live="visfirmabrevhoved"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5"
                                >
                                <span>Firmabrevhoved</span>
                            </label>
                        </div>

                        {{-- Master kontrol --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-800">
                                <input type="checkbox" wire:model.live="filter.visalle" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-3.5 h-3.5">
                                <span>Vis alle felter i top</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-800">
                                <input type="checkbox" wire:model.live="filter.skjulalle" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 w-3.5 h-3.5">
                                <span>Skjul hele brevhovedet</span>
                            </label>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Detaljerede felter --}}
                        <div class="space-y-1.5 text-slate-600">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.navn" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Navn</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.adresse" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Adresse</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.dato" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Dato</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.sagsnr" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Sagsnr.</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model.live="filter.emne" class="rounded border-slate-300 text-indigo-600 w-3.5 h-3.5">
                                <span>Vis Emne</span>
                            </label>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @php
            $skjulAlt = $filter['skjulalle'] ?? false;
            $visAlt   = $filter['visalle'] ?? false;

            $visNavn    = $visAlt || (!$skjulAlt && ($filter['navn'] ?? true));
            $visAdresse = $visAlt || (!$skjulAlt && ($filter['adresse'] ?? true));
            $visDato    = $visAlt || (!$skjulAlt && ($filter['dato'] ?? true));
            $visSagsnr  = $visAlt || (!$skjulAlt && ($filter['sagsnr'] ?? true));

            $navnTop      = str_replace(',', '.', ($navn_top_margin ?: 5.45));
            $datoTop      = str_replace(',', '.', ($dato_margin_top ?: 7.57));
            $emneTop      = str_replace(',', '.', ($emne_top_margin ?: 8.91));
            $bodyTop      = str_replace(',', '.', ($body_margin_top ?: 2));
            $brevhovedTop = str_replace(',', '.', ($brevhoved_top_margin ?: 1.50));

            $firmaLeft      = str_replace(',', '.', ($brev->firma_left_margin ?? $firma_left_margin ?? 11.80));
            $firmaFontSize  = (float) str_replace(',', '.', ($brev->firma_font_size ?? $firma_font_size ?? 8.5));
            $firmaTitleSize = round($firmaFontSize * 1.55, 1);

            $firmaLeftCm = (float) str_replace(',', '.', ($firmaLeft ?? 11.0));
        @endphp

        {{-- DYNAMISK TOP-HEADER (Viser præcis de valgte eksempler) --}}
        @if($skjulAlt)
            <div class="w-full text-xs text-slate-400 bg-slate-50 border border-dashed border-slate-300 rounded-t-2xl p-4 text-center italic">
                🚫 Brevhovedet er skjult for denne skabelon
            </div>
        @else
            <div style="position: relative; width: 100%; min-height: 500px; margin: 0;" id="brev_preview">
                
                {{-- Ensartet firmabrevhoved-blok til både brev-manager og vis-brev --}}
                @if(($brev->visfirmabrevhoved ?? ($visfirmabrevhoved ?? true)))
                    <div
                        x-data="{
                            previewWidth: 0,
                            firmaLeftCm: {{ $firmaLeftCm }},
                            get leftPx() {
                                // 1 cm i CSS = 96px / 2.54 ≈ 37.7953px
                                const pxPerCm = 37.79527559;
                                const a4WidthPx = 21 * pxPerCm; // ~793.7px
                                const desiredLeftOnA4Px = this.firmaLeftCm * pxPerCm;

                                if (!this.previewWidth) {
                                    return desiredLeftOnA4Px;
                                }

                                // Skaler forholdsmæssigt i forhold til et 21 cm A4-ark
                                return (desiredLeftOnA4Px / a4WidthPx) * this.previewWidth;
                            }
                        }"
                        x-init="
                            previewWidth = $el.parentElement.clientWidth;
                            const ro = new ResizeObserver(() => {
                                if ($el.parentElement) previewWidth = $el.parentElement.clientWidth;
                            });
                            ro.observe($el.parentElement);
                        "
                        :wire:key="`firma-brevhoved-{{ $brevId ?? 'ny' }}-{{ $brevhovedTop }}-{{ $firmaLeft }}-{{ $firmaFontSize }}`"
                        :style="`
                            position: absolute;
                            top: {{ $brevhovedTop }}cm;
                            left: ${leftPx}px;
                            border-left: 2.5px solid #1e293b;
                            padding-left: 10px;
                            font-family: Arial, Helvetica, sans-serif;
                            line-height: 1.35;
                            box-sizing: border-box;
                        `"
                        class="text-left text-slate-900"
                    >
                        <div
                            style="font-size: {{ $firmaTitleSize }}pt; line-height: 1.15; letter-spacing: -0.01em;"
                            class="font-extrabold uppercase text-slate-950 mb-1.5"
                        >
                            {{ $brev->firma_navn ?? ($this->firma_navn ?? 'DANSK KONSULENT GRUPPE A/S') }}
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
            
                {{-- 1. Navn og adresse sektion --}}
                <div style="position: absolute; top: {{ $navnTop }}cm; left: 0; right: 0;">
                    <table class="w-full text-xs sm:text-sm text-slate-700 font-sans" style="width: 100%; border-collapse: collapse; border: none;" cellspacing="0" cellpadding="0" border="0">
                        <tr style="border: none;">
                            <td style="width: 50%; vertical-align: top; text-align: left; padding: 0; border: none;">
                                @if($visNavn || $visAdresse)
                                    <div class="space-y-1">
                                        @if($visNavn)
                                            <div class="font-bold text-base text-slate-900">[ Eksempel Navn ]</div>
                                        @endif
                                        @if($visAdresse)
                                            <div class="text-slate-600">[ Eksempel Adresse ]</div>
                                            <div class="text-slate-600">[ Postnr By ]</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-300 italic">[ Modtagerfelt skjult ]</span>
                                @endif
                            </td>
                            <td style="width: 50%; border: none;"></td>
                        </tr>
                    </table>
                </div>

                {{-- 2. Dato og Sagsnr sektion --}}
                <div style="position: absolute; top: {{ $datoTop }}cm; left: 0; right: 0;">
                    <table class="w-full text-xs sm:text-sm text-slate-700 font-sans" style="width: 100%; border-collapse: collapse; border: none;" cellspacing="0" cellpadding="0" border="0">
                        <tr style="border: none;">
                            <td style="width: 50%; border: none;"></td>
                            <td style="width: 50%; vertical-align: top; text-align: right; padding: 0; border: none;">
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
                </div>

                {{-- 3 + 4. EMNE OG BREVTEKST I NATURLIGT FLOW (Skubber automatisk bundbjælken ned) --}}
<div style="position: relative; margin-top: {{ $emneTop }}cm;" class="flex flex-col space-y-3 pb-4">
    <div class="font-bold text-slate-900 border-b border-slate-100 pb-2" style="font-size: {{ $emne_font_size ?? 10.5 }}pt;">
        {{ $emne }}
    </div>

    <div style="margin-top: {{ $bodyTop }}em;" class="w-full">
        <x-wysiwyg-editor 
            wire:key="editor-{{ $brevId }}-{{ $beskyttet ? 'prot' : 'unprot' }}"
            wireModel="tekst" 
            :value="$tekst"
            :navnTopMargin="$navn_top_margin"
            :datoMarginTop="$dato_margin_top"
            :emneTopMargin="$emne_top_margin"
            :bodyMarginTop="0"
            :navnFontSize="$navn_font_size"
            :emneFontSize="$emne_font_size"
            :bodyFontSize="$body_font_size"
            :fontFamily="$font_family"
            :marginLeft="$margin_left" 
            :marginRight="$margin_right"
            :linjeafstand="$linjeafstand" 
            :beskyttet="$beskyttet"
            height="420px" 
        />
    </div>

                </div>
            </div>
        @endif
    </div>

    {{-- 3. BUND MED ALLE KNAPPER --}}
    <div class="mt-4 bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-xl flex flex-wrap items-center justify-between gap-4 text-white">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2">Handlinger:</span>
            @if ($brevId)
                <button type="button" wire:click="openRenameModal" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 border border-slate-700">
                    ✏️ Omdøb
                </button>
                <button type="button" wire:click="confirmDeleteBrev({{ $brevId }})" class="px-3.5 py-2 bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 border border-rose-900/50">
                    🗑️ Slet
                </button>
            @endif
            <button type="button" wire:click="openCreateModal" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 shadow-sm">
                <span>+</span> Opret ny skabelon
            </button>
        </div>

        <div>
            <button type="button" wire:click="saveTemplate" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold transition cursor-pointer shadow-lg flex items-center gap-2">
                💾 Gem skabelon
            </button>
        </div>
    </div>

    {{-- 4. TRÆKBAR BOKS MED FELTER --}}
    <div 
        x-data="{
            open: true,
            dragging: false,
            x: window.innerWidth - 320,
            y: 120,
            startX: 0,
            startY: 0,
            init() {
                if (this.x > window.innerWidth - 320) this.x = window.innerWidth - 320;
            }
        }"
        @mousemove.window="
            if (dragging) {
                x = Math.max(10, Math.min(window.innerWidth - 300, startX + ($event.clientX - mouseX)));
                y = Math.max(10, Math.min(window.innerHeight - 200, startY + ($event.clientY - mouseY)));
            }
        "
        @mouseup.window="dragging = false"
        :style="`left: ${x}px; top: ${y}px; position: fixed;`" 
        class="w-72 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 overflow-hidden select-none"
    >
        <div 
            @mousedown="
                dragging = true; 
                mouseX = $event.clientX; 
                mouseY = $event.clientY; 
                startX = x; 
                startY = y;
            "
            class="cursor-grab active:cursor-grabbing bg-slate-900 text-white px-4 py-3 flex justify-between items-center"
        >
            <span class="font-bold text-xs flex items-center gap-1.5">
                <span>⠿</span> Tilgængelige felter
            </span>
            <button type="button" @click="open = !open" class="text-xs text-slate-400 hover:text-white cursor-pointer"><span x-text="open ? '–' : '+'"></span></button>
        </div>
        
        <div x-show="open" class="p-3 max-h-80 overflow-y-auto bg-slate-50 space-y-2">
            <p class="text-[10px] text-slate-400 font-medium px-1">Træk felterne ind i editoren:</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($this->validTokens as $token)
                    <div draggable="true" @dragstart="$event.dataTransfer.setData('text/plain', '{{ '{'.$token.'}' }}')" class="px-2.5 py-1 rounded-lg cursor-grab text-xs font-mono font-bold bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-600 hover:text-white transition shadow-2xs">
                        {{ '{'.$token.'}' }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODALER (Opret, Omdøb, Slet) --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div wire:click.away="closeCreateModal" class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Opret ny brevskabelon</h3>
                    <button type="button" wire:click="closeCreateModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700">Skabelontitel</label>
                    <input type="text" wire:model.blur="newBrevTitle" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-slate-50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900" placeholder="F.eks. Rykker 1...">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeCreateModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Annuller</button>
                    <button type="button" wire:click="createNewBrev" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold cursor-pointer">Opret</button>
                </div>
            </div>
        </div>
    @endif

    @if($showRenameModal)
        <div class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div wire:click.away="closeRenameModal" class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Omdøb skabelon</h3>
                    <button type="button" wire:click="closeRenameModal" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700">Ny titel</label>
                    <input type="text" wire:model.blur="editTitle" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs bg-slate-50 focus:outline-none focus:border-indigo-500 font-bold text-slate-900">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="closeRenameModal" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Annuller</button>
                    <button type="button" wire:click="updateBrevTitle" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold cursor-pointer">Gem navn</button>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteBrevModal)
        <div class="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl space-y-4 border border-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-rose-600">Slet skabelon</h3>
                    <button type="button" wire:click="cancelDeleteBrev" class="text-slate-400 hover:text-slate-600 text-sm font-bold cursor-pointer">✕</button>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Er du sikker på, at du vil slette skabelonen <strong class="text-slate-900">{{ $brevToDeleteTitle }}</strong>? Denne handling kan ikke angres.
                </p>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" wire:click="cancelDeleteBrev" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold cursor-pointer">Annuller</button>
                    <button type="button" wire:click="executeDeleteBrev" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold cursor-pointer">Slet permanent</button>
                </div>
            </div>
        </div>
    @endif

</div>