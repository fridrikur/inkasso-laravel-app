<div>
    @if ($showQuickMenu)
        <div class="fixed inset-0 z-50 overflow-hidden">
            {{-- Backdrop / Overlay --}}
            <div
                class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity"
                wire:click="closeQuickMenu"
            ></div>

            {{-- Flyout Panel --}}
            <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl transition-transform border-l border-slate-100 flex flex-col justify-between">
                <div>
                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 bg-slate-50/50">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">⚡ Quick Menu</h2>
                            <p class="text-xs text-slate-500">Hurtige handlinger og genveje</p>
                        </div>

                        <button
                            type="button"
                            wire:click="closeQuickMenu"
                            class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                        >
                            ✕
                        </button>
                    </div>

                    {{-- Indhold --}}
                    <div class="p-5">
                        @if ($quickMenuScreen === 'main')
                            <div class="grid grid-cols-1 gap-3 text-sm">
                                <button
                                    type="button"
                                    wire:click="goToCreateKreditor"
                                    class="rounded-xl border border-slate-200 px-4 py-3 text-left font-semibold text-slate-700 hover:bg-slate-50 hover:border-indigo-500 transition shadow-sm"
                                >
                                    🏢 Opret kreditor
                                </button>

                                <button
                                    type="button"
                                    wire:click="goToCreateBrev"
                                    class="rounded-xl border border-slate-200 px-4 py-3 text-left font-semibold text-slate-700 hover:bg-slate-50 hover:border-indigo-500 transition shadow-sm"
                                >
                                    📨 Opret brev
                                </button>

                                <button
                                    type="button"
                                    wire:click="goToFindSag"
                                    class="rounded-xl border border-slate-200 px-4 py-3 text-left font-semibold text-slate-700 hover:bg-slate-50 hover:border-indigo-500 transition shadow-sm"
                                >
                                    🔍 Find sag
                                </button>

                                <button
                                    type="button"
                                    wire:click="goToGdprScan"
                                    class="rounded-xl border border-slate-200 px-4 py-3 text-left font-semibold text-slate-700 hover:bg-slate-50 hover:border-indigo-500 transition shadow-sm"
                                >
                                    🛡️ GDPR scan
                                </button>

                                <button
                                    type="button"
                                    wire:click="goToCreateUser"
                                    class="rounded-xl border border-slate-200 px-4 py-3 text-left font-semibold text-slate-700 hover:bg-slate-50 hover:border-indigo-500 transition shadow-sm"
                                >
                                    👤 Opret bruger
                                </button>

                                <button
                                    type="button"
                                    wire:click="goToCreateKonsulent"
                                    class="rounded-xl border border-slate-200 px-4 py-3 text-left font-semibold text-slate-700 hover:bg-slate-50 hover:border-indigo-500 transition shadow-sm"
                                >
                                    👤 Opret konsulent
                                </button>

                                <button
                                    type="button"
                                    wire:click="openImportSagerMenu"
                                    class="rounded-xl bg-indigo-600 px-4 py-3 text-left font-semibold text-white hover:bg-indigo-700 transition shadow-sm mt-2 flex items-center justify-between"
                                >
                                    <span>📥 Importér sager</span>
                                    <span>→</span>
                                </button>
                            </div>
                        @endif

                        @if ($quickMenuScreen === 'import-sager')
                            <div>
                                <button
                                    type="button"
                                    wire:click="$set('quickMenuScreen', 'main')"
                                    class="mb-4 text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 transition uppercase tracking-wider"
                                >
                                    ← Tilbage til menu
                                </button>

                                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Vælg kreditor for sagsimport
                                </h3>

                                <div class="space-y-2 max-h-[70vh] overflow-y-auto pr-1">
                                    @forelse ($kreditors as $kreditor)
                                        <button
                                            type="button"
                                            wire:click="goToImportSager('{{ $kreditor->lotusID ?? $kreditor->id }}')"
                                            class="flex w-full items-center justify-between rounded-xl border border-slate-200 px-4 py-3 text-left text-sm hover:bg-indigo-50/50 hover:border-indigo-200 transition"
                                        >
                                            <span class="font-bold text-slate-800">
                                                {{ $kreditor->navn }}
                                            </span>

                                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">
                                                {{ $kreditor->sager_count ?? 0 }} sager
                                            </span>
                                        </button>
                                    @empty
                                        <p class="text-sm text-slate-500 italic">
                                            Ingen kreditorer fundet.
                                        </p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 text-center text-xs text-slate-400">
                    InkassoApp • Sønderborg Servicedesk
                </div>
            </div>
        </div>
    @endif
</div>