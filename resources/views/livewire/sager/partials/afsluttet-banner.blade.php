<div class="mb-4 p-4 rounded-2xl border bg-slate-100/90 border-slate-200 text-slate-700 text-xs font-medium flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-base shrink-0">
            🏁
        </div>
        <div>
            <span class="font-bold text-slate-900 block sm:inline">Denne sag er markeret som afsluttet</span>
            <span class="text-slate-500">
                pr. <strong class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($form->afsluttet ?? $sag->afsluttet)->format('d-m-Y') }}</strong>
            </span>
            
            {{-- VISER ÅRSAG HVIS DEN ER VALGT --}}
            @if(!empty($form->afslutning) && isset($selectOptions['afslutning'][$form->afslutning]))
                <span class="hidden md:inline text-slate-400"> • </span>
                <span class="block md:inline text-slate-600 mt-0.5 md:mt-0">
                    Årsag: <span class="italic font-semibold text-slate-800">{{ $selectOptions['afslutning'][$form->afslutning] }}</span>
                </span>
            @endif
        </div>
    </div>

    <span class="inline-flex items-center justify-center self-start sm:self-center text-[10px] bg-slate-200/80 text-slate-700 px-3 py-1 rounded-lg font-bold uppercase tracking-wider border border-slate-300/60 shrink-0">
        Afsluttet sag
    </span>
</div>