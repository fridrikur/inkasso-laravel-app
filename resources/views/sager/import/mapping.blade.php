<x-layouts.app title="Par kolonne-mapping">
    <div 
        x-data="{ 
            selectedTemplate: '', 
            showMapping: true, 
            isSubmitting: false,
            applyTemplate(jsonMapping) {
                if (!jsonMapping) {
                    this.showMapping = true;
                    return;
                }
                const mapping = JSON.parse(jsonMapping);
                document.querySelectorAll('.mapping-dropdown').forEach(select => select.value = '');
                Object.keys(mapping).forEach(field => {
                    const index = mapping[field];
                    const select = document.querySelector(`.mapping-dropdown[data-header-index='${index}']`);
                    if (select) select.value = field;
                });
                this.showMapping = false; // 🟢 Skjul oversigten og gem-skabelon boksen
            }
        }"
        class="max-w-5xl mx-auto py-10 px-4 sm:px-6 space-y-8 relative"
    >

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">🗺️ Parring af Kolonner</h1>
                <p class="text-xs text-slate-500 mt-1">
                    Fil: <span class="font-semibold text-slate-700">{{ basename($filePath) }}</span> 
                    • Samlet antal sager til import: <span class="font-bold text-indigo-600 font-mono">{{ number_format($totalRows, 0, ',', '.') }}</span>
                </p>
            </div>
        </div>

        {{-- GEMTE SKABELONER OG REDIGERING --}}
        @include('sager.import.partials.templates-manager')

        <form 
            method="POST" 
            action="{{ route('sager.import.run', $kreditor) }}" 
            @submit="isSubmitting = true" 
            class="space-y-6"
        >
            @csrf
            <input type="hidden" name="file_path" value="{{ $filePath }}">
            <input type="hidden" name="duplicate_action" value="{{ $duplicateAction ?? 'keep' }}">

            <!-- SKABELON VÆLGER -->
            @if(!empty($templates) && count($templates) > 0)
                <div class="bg-indigo-50/70 border border-indigo-100 rounded-2xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">📋</span>
                        <div>
                            <div class="text-xs font-bold text-indigo-950">Brug en gemt skabelon</div>
                            <div class="text-[11px] text-indigo-700">Vælg et tidligere gemt format for {{ $kreditor->navn }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <select 
                            x-model="selectedTemplate" 
                            @change="applyTemplate($event.target.value)" 
                            class="w-full sm:w-auto rounded-xl border border-indigo-200 bg-white px-3.5 py-2.5 text-xs font-bold text-indigo-900 outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer shadow-sm"
                        >
                            <option value="">-- Manuel parring (ingen skabelon) --</option>
                            @foreach($templates as $tpl)
                                <option value="{{ json_encode($tpl->mapping) }}">{{ $tpl->navn }}</option>
                            @endforeach
                        </select>

                        <!-- KNAP TIL AT VIS/SKJUL MAPPING IGEN -->
                        <button 
                            type="button" 
                            @click="showMapping = !showMapping" 
                            x-show="selectedTemplate !== ''" 
                            class="px-3 py-2.5 rounded-xl border border-indigo-200 bg-white hover:bg-indigo-50 text-indigo-700 font-bold text-xs transition whitespace-nowrap"
                        >
                            <span x-text="showMapping ? '👁️ Skjul feltliste' : '⚙️ Tilpas felter'"></span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- MAPPING TABEL (Skjules automatisk når skabelon vælges) -->
            <div 
                x-show="showMapping" 
                x-transition:enter="transition ease-out duration-200" 
                x-transition:enter-start="opacity-0 -translate-y-2" 
                x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden"
            >
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Filens Kolonner ({{ count($headers) }})</h3>
                    <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Matcher Felt I Systemet</h3>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($headers as $index => $header)
                        <div class="p-4 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                            <!-- FILENS KOLONNE -->
                            <div class="flex items-center gap-3 sm:w-1/2">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-mono text-[11px] font-bold text-slate-500">#{{ $index + 1 }}</span>
                                <span class="font-bold text-xs text-slate-800">{{ $header }}</span>
                            </div>

                            <!-- DROPDOWN FELT -->
                            <div class="sm:w-1/2">
                                <select 
                                    name="mapping[{{ $index }}]" 
                                    data-header-index="{{ $index }}" 
                                    class="mapping-dropdown w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-medium text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition"
                                >
                                    <option value="">-- Ignorer denne kolonne --</option>
                                    
                                    <optgroup label="📂 SAGS DATA">
                                        <option value="sagsnr" {{ ($autoMapping['sagsnr'] ?? null) === $index ? 'selected' : '' }}>Sagsnummer / Kontraktnr (*påkrævet)</option>
                                        <option value="ktr" {{ ($autoMapping['ktr'] ?? null) === $index ? 'selected' : '' }}>Kontrakttype (KTR)</option>
                                        <option value="aktiv" {{ ($autoMapping['aktiv'] ?? null) === $index ? 'selected' : '' }}>Aktiv / Bilmærke (f.eks. Bil/Reg.nr.)</option>
                                        <option value="reg_nr" {{ ($autoMapping['reg_nr'] ?? null) === $index ? 'selected' : '' }}>Reg. nummer / Nummerplade</option>
                                        <option value="kort_bemaerkning" {{ ($autoMapping['kort_bemaerkning'] ?? null) === $index ? 'selected' : '' }}>Bemærkninger</option>
                                        <option value="hovedstol" {{ ($autoMapping['hovedstol'] ?? null) === $index ? 'selected' : '' }}>Hovedstol / Udestående balance</option>
                                        <option value="restgaeld_dkg" {{ ($autoMapping['restgaeld_dkg'] ?? null) === $index ? 'selected' : '' }}>Total Restance</option>
                                        <option value="n_mdlydelse" {{ ($autoMapping['n_mdlydelse'] ?? null) === $index ? 'selected' : '' }}>Månedlig Ydelse / Afdrag</option>
                                        <option value="renter" {{ ($autoMapping['renter'] ?? null) === $index ? 'selected' : '' }}>Renter</option>
                                        <option value="gebyr" {{ ($autoMapping['gebyr'] ?? null) === $index ? 'selected' : '' }}>Gebyrer</option>
                                        <option value="indbetalt" {{ ($autoMapping['indbetalt'] ?? null) === $index ? 'selected' : '' }}>Indbetalt</option>
                                        <option value="stelnr" {{ ($autoMapping['stelnr'] ?? null) === $index ? 'selected' : '' }}>Stelnummer (VIN)</option>
                                        <option value="fakturanr" {{ ($autoMapping['fakturanr'] ?? null) === $index ? 'selected' : '' }}>Fakturanummer</option>
                                    </optgroup>

                                    <optgroup label="👤 HOVEDDEBITOR">
                                        <option value="debitor_cpr" {{ ($autoMapping['debitor_cpr'] ?? null) === $index ? 'selected' : '' }}>CPR / CVR-nummer</option>
                                        <option value="debitor_navn" {{ ($autoMapping['debitor_navn'] ?? null) === $index ? 'selected' : '' }}>Navn</option>
                                        <option value="debitor_adresse" {{ ($autoMapping['debitor_adresse'] ?? null) === $index ? 'selected' : '' }}>Adresse</option>
                                        <option value="debitor_postnr" {{ ($autoMapping['debitor_postnr'] ?? null) === $index ? 'selected' : '' }}>Postnummer</option>
                                        <option value="debitor_by" {{ ($autoMapping['debitor_by'] ?? null) === $index ? 'selected' : '' }}>By</option>
                                        <option value="debitor_tlf" {{ ($autoMapping['debitor_tlf'] ?? null) === $index ? 'selected' : '' }}>Telefon (Fastnet/Hoved)</option>
                                        <option value="debitor_mobil" {{ ($autoMapping['debitor_mobil'] ?? null) === $index ? 'selected' : '' }}>Mobilnummer</option>
                                        <option value="debitor_email" {{ ($autoMapping['debitor_email'] ?? null) === $index ? 'selected' : '' }}>E-mailadresse</option>
                                    </optgroup>

                                    <optgroup label="👥 MEDDEBITOR">
                                        <option value="meddebitor_cpr" {{ ($autoMapping['meddebitor_cpr'] ?? null) === $index ? 'selected' : '' }}>CPR / CVR-nummer</option>
                                        <option value="meddebitor_navn" {{ ($autoMapping['meddebitor_navn'] ?? null) === $index ? 'selected' : '' }}>Navn</option>
                                        <option value="meddebitor_adresse" {{ ($autoMapping['meddebitor_adresse'] ?? null) === $index ? 'selected' : '' }}>Adresse</option>
                                        <option value="meddebitor_postnr" {{ ($autoMapping['meddebitor_postnr'] ?? null) === $index ? 'selected' : '' }}>Postnummer</option>
                                        <option value="meddebitor_by" {{ ($autoMapping['meddebitor_by'] ?? null) === $index ? 'selected' : '' }}>By</option>
                                        <option value="meddebitor_tlf" {{ ($autoMapping['meddebitor_tlf'] ?? null) === $index ? 'selected' : '' }}>Telefon</option>
                                        <option value="meddebitor_mobil" {{ ($autoMapping['meddebitor_mobil'] ?? null) === $index ? 'selected' : '' }}>Mobilnummer</option>
                                        <option value="meddebitor_email" {{ ($autoMapping['meddebitor_email'] ?? null) === $index ? 'selected' : '' }}>E-mailadresse</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- GEM SKABELON VALG (Skjules automatisk når skabelon er valgt) -->
            <div 
                x-show="showMapping" 
                x-transition
                class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3"
            >
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="save_template" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs font-bold text-slate-800">Gem denne kolonne-parring som en skabelon til fremtiden</span>
                </label>
                <input 
                    type="text" 
                    name="template_name" 
                    placeholder="Skabelonnavn (f.eks. Santander Standard Format)" 
                    class="w-full max-w-md px-3.5 py-2 rounded-xl border border-slate-200 text-xs outline-none focus:border-indigo-500"
                >
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="flex justify-end gap-3">
                <button 
                    type="submit" 
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-8 py-3.5 rounded-xl shadow-sm transition cursor-pointer"
                >
                    <span>🚀 Start Import Med Denne Parring</span>
                </button>
            </div>
        </form>

        <!-- SAGS-IMPORT LOADING OVERLAY -->
        <div 
            x-show="isSubmitting" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4"
            style="display: none;"
        >
            <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl text-center space-y-6 border border-slate-100">
                
                <!-- SAGS-IMPORT IKON & ANIMATION -->
                <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin"></div>
                    <span class="text-3xl animate-bounce">📥</span>
                </div>

                <!-- TEKST OG STATUS -->
                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-slate-900">Importerer sager...</h3>
                    <p class="text-xs text-slate-500">
                        Behandler <span class="font-bold text-indigo-600 font-mono text-sm">{{ number_format($totalRows, 0, ',', '.') }}</span> sager og opretter tilknyttede debitorer.
                    </p>
                </div>

                <!-- SLANK PROGRESS-BAR -->
                <div class="space-y-2">
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200/80 shadow-inner">
                        <div class="bg-indigo-600 h-full rounded-full animate-pulse w-full"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-400 font-mono font-bold uppercase">
                        <span>0%</span>
                        <span class="text-indigo-600 animate-pulse">Importerer...</span>
                        <span>100%</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 italic">
                    Vent venligst – luk eller genindlæs ikke siden.
                </p>
            </div>
        </div>

    </div>
</x-layouts.app>