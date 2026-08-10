<x-layouts.app title="Importer sager">
    <div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 space-y-6">

        {{-- OVERSKRIFT --}}
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Importer sager til {{ $kreditor->navn }}
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Upload en Excel (.xlsx) eller CSV fil for at starte importprocessen.
                </p>
            </div>
            <a href="{{ route('sager.import.log') }}" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                📋 Se Importlog
            </a>
        </div>

        {{-- FEJLMEDDELELSER --}}
        @if(session('error') || !empty($error))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold px-4 py-3.5 rounded-2xl flex items-center gap-3">
                <span>⚠️</span>
                <span>{{ session('error') ?? $error }}</span>
            </div>
        @endif

        {{-- UPLOAD FORMULAR --}}
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-sm space-y-6">
            <form method="POST" action="{{ route('sager.import.mapping', $kreditor) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <input type="hidden" name="lotusID" value="{{ request('lotusID') }}">

                {{-- DRAG & DROP / FILE INPUT --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Vælg importfil (.xlsx / .csv)
                    </label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-2xl p-6 text-center transition bg-slate-50/50 hover:bg-indigo-50/30">
                        <div class="text-3xl mb-2">📄</div>
                        <input
                            type="file"
                            name="file"
                            accept=".xlsx, .csv"
                            required
                            class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer"
                        >
                        <p class="text-[11px] text-slate-400 mt-2">
                            Maksimal filstørrelse: 10 MB
                        </p>
                    </div>
                </div>

                {{-- DUBLETHÅNDTERING --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Håndtering af eksisterende sagsnumre
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="border border-slate-200 rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:border-indigo-500 transition text-xs font-medium text-slate-700">
                            <input type="radio" name="duplicate_action" value="keep" checked class="text-indigo-600 focus:ring-indigo-500">
                            <span>Opret altid (Behold)</span>
                        </label>
                        <label class="border border-slate-200 rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:border-indigo-500 transition text-xs font-medium text-slate-700">
                            <input type="radio" name="duplicate_action" value="replace" class="text-indigo-600 focus:ring-indigo-500">
                            <span>Opdatér eksisterende</span>
                        </label>
                        <label class="border border-slate-200 rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:border-indigo-500 transition text-xs font-medium text-slate-700">
                            <input type="radio" name="duplicate_action" value="skip" class="text-indigo-600 focus:ring-indigo-500">
                            <span>Spring dubletter over</span>
                        </label>
                    </div>
                </div>

                {{-- ACTION BUTTON --}}
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-8 py-3.5 rounded-xl shadow-sm transition cursor-pointer"
                    >
                        <span>Næste: Par kolonner &rarr;</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-layouts.app>