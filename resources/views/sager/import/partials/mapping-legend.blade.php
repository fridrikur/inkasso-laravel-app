<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
    <details class="group">
        <summary class="flex items-center justify-between p-6 cursor-pointer select-none bg-slate-50/50 hover:bg-slate-100/60 transition">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base">
                    🗺️
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Kolonne-mapping & Feltforklaring</h3>
                    <p class="text-xs text-slate-500">Se hvordan Excel-kolonnerne automatisk kortlægges til Sager og Debitorer</p>
                </div>
            </div>
            <span class="text-slate-400 group-open:rotate-180 transition-transform duration-200">
                ▼
            </span>
        </summary>

        <div class="p-6 border-t border-slate-100 space-y-6 text-xs">
            
            {{-- GRID MED FELTER --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- HOVEDDEBITOR --}}
                <div class="space-y-2">
                    <h4 class="font-bold text-slate-800 flex items-center gap-2 text-xs uppercase tracking-wider">
                        <span>👤</span> Hoveddebitor
                    </h4>
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 space-y-1.5 font-mono text-[11px]">
                        <div class="flex justify-between"><span class="text-slate-500">Cpr_Hoveddebitor</span> <span class="text-indigo-600 font-semibold">→ debitor.pnr</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Navn_Hoveddebitor</span> <span class="text-indigo-600 font-semibold">→ debitor.navn</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Adresse_Hoveddebitor</span> <span class="text-indigo-600 font-semibold">→ debitor.adresse</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Postnummer</span> <span class="text-indigo-600 font-semibold">→ debitor.postnr</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Tlf / Mobil / Mailadr</span> <span class="text-indigo-600 font-semibold">→ debitor.tlf / mobil / email</span></div>
                    </div>
                </div>

                {{-- MEDDEBITOR --}}
                <div class="space-y-2">
                    <h4 class="font-bold text-slate-800 flex items-center gap-2 text-xs uppercase tracking-wider">
                        <span>👥</span> Meddebitor
                    </h4>
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 space-y-1.5 font-mono text-[11px]">
                        <div class="flex justify-between"><span class="text-slate-500">Cpr_Meddebitor</span> <span class="text-purple-600 font-semibold">→ meddebitor.pnr</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Navn_Meddebitor</span> <span class="text-purple-600 font-semibold">→ meddebitor.navn</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Adresse_Meddebitor</span> <span class="text-purple-600 font-semibold">→ meddebitor.adresse</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Postnummer</span> <span class="text-purple-600 font-semibold">→ meddebitor.postnr</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Tlf / Mobil / Mailadr</span> <span class="text-purple-600 font-semibold">→ meddebitor.tlf / mobil / email</span></div>
                    </div>
                </div>

                {{-- SAGS- & GENSTANDSDATA --}}
                <div class="space-y-2">
                    <h4 class="font-bold text-slate-800 flex items-center gap-2 text-xs uppercase tracking-wider">
                        <span>📂</span> Sagsoplysninger
                    </h4>
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 space-y-1.5 font-mono text-[11px]">
                        <div class="flex justify-between"><span class="text-slate-500">Kontraktnummer / Sagsnr</span> <span class="text-emerald-600 font-semibold">→ sag.sagsnr</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">StelNr (VIN)</span> <span class="text-emerald-600 font-semibold">→ sag.stelnr</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Fakturanummer</span> <span class="text-emerald-600 font-semibold">→ sag.fakturanr</span></div>
                    </div>
                </div>

                {{-- ØKONOMI --}}
                <div class="space-y-2">
                    <h4 class="font-bold text-slate-800 flex items-center gap-2 text-xs uppercase tracking-wider">
                        <span>💰</span> Økonomi & Restancer
                    </h4>
                    <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 space-y-1.5 font-mono text-[11px]">
                        <div class="flex justify-between"><span class="text-slate-500">Udestående Balance</span> <span class="text-amber-600 font-semibold">→ sag.hovedstol</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Total Restance</span> <span class="text-amber-600 font-semibold">→ sag.restgaeld_dkg</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Månedlig Ydelse / Afdrag</span> <span class="text-amber-600 font-semibold">→ sag.n_mdlydelse</span></div>
                    </div>
                </div>

            </div>

        </div>
    </details>
</div>