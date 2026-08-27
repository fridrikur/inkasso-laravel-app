<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                🗑 Papirkurv
            </h1>

            <p class="text-sm text-gray-500">
                Soft deleted sager
            </p>
        </div>

        <div class="flex items-center gap-3">
            @if($sagers->total() > 0)
                <button
                    wire:click="confirmEmptyTrash"
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 transition cursor-pointer"
                >
                    🗑 Tøm papirkurv (Slet alt)
                </button>
            @endif

            <a
                href="{{ route('sager.index') }}"
                class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm hover:bg-gray-50 transition"
            >
                Tilbage til sager
            </a>
        </div>

    </div>

    <x-sager.table
        :sagers="$sagers"
        mode="trash"
    />

    <div class="mt-4">
        {{ $sagers->links() }}
    </div>

    {{-- MODAL: TØM PAPIRKURV --}}
    @if($showEmptyTrashModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 space-y-4">
                
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                        🗑️
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Tøm papirkurv helt?</h3>
                        <p class="text-xs text-slate-500">Denne handling kan ikke fortydes.</p>
                    </div>
                </div>

                <p class="text-sm text-slate-600">
                    Er du helt sikker på, at du vil slette <span class="font-bold text-slate-900">alle sager</span> i papirkurven permanent? Alle tilknyttede dokumenter, filer og historik vil blive slettet for evigt.
                </p>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        wire:click="cancelEmptyTrash"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                    >
                        Annuller
                    </button>
                    
                    <button
                        wire:click="emptyTrash"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-500 transition cursor-pointer"
                    >
                        Ja, slet alt permanent
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- 🟢 MODAL: GENDAN SAG --}}
    @if($showRestoreModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 space-y-4">
                
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                        ♻️
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Gendan sag</h3>
                        <p class="text-xs text-slate-500">Flyt sagen tilbage til aktive sager.</p>
                    </div>
                </div>

                <p class="text-sm text-slate-600">
                    Er du sikker på, at du vil gendanne denne sag fra papirkurven? Sagen vil igen blive tilgængelig i sagslisten.
                </p>

                <div class="flex justify-end gap-3 pt-2">
                    <button
                        wire:click="cancelRestoreSag"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                    >
                        Annuller
                    </button>
                    
                    <button
                        wire:click="executeRestore"
                        class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 transition cursor-pointer"
                    >
                        Ja, gendan sag
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>