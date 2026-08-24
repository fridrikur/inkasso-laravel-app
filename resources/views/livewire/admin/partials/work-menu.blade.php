{{-- resources/views/livewire/admin/partials/work-menu.blade.php --}}
<div 
    x-show="workMenuOpen" 
    x-cloak
    style="display: none;"
    class="fixed inset-0 z-[9999]"
>
    {{-- Backdrop / Overlay --}}
    <div
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity duration-300"
        @click="workMenuOpen = false"
    ></div>

    {{-- Flyout Panel (Placeret til VENSTRE) --}}
    <div class="absolute left-0 top-0 h-full w-full max-w-md bg-white shadow-2xl transition-transform duration-300 flex flex-col">
        
        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5 bg-slate-50/50">
            <div>
                <h2 class="text-lg font-bold text-slate-900">
                    Dagligt Arbejde
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Hurtig adgang til sager, kreditorer og konsulenter
                </p>
            </div>

            <button
                type="button"
                @click="workMenuOpen = false"
                class="rounded-xl p-2 text-slate-400 hover:bg-slate-200/60 hover:text-slate-700 transition"
            >
                ✕
            </button>
        </div>

        {{-- INDHOLD --}}
        <div class="p-6 overflow-y-auto space-y-6 flex-1">

            {{-- SEKTION: SAGER --}}
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sager & Behandling</span>
                    <a href="{{ route('sager.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Se alle →</a>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('sager.create') }}"
                       class="flex items-center gap-3 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Opret ny sag</span>
                    </a>

                    <a href="{{ route('sager.search') }}"
                       class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2.5">
                            🔍 Fri søgning
                        </span>
                        <span class="text-xs text-slate-400">Søg sager</span>
                    </a>

                    <a href="{{ route('search-constructor') }}"
                       class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2.5">
                            📁 Gemte søgninger
                        </span>
                    </a>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- SEKTION: KREDITORER --}}
            <div class="space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kreditorer</span>
                    <a href="{{ route('kreditorer.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Oversigt →</a>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('kreditorer.create') }}"
                       class="flex items-center gap-3 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Opret kreditor</span>
                    </a>

                    <a href="{{ route('kreditorer.index', ['view' => 'inactive']) }}"
                       class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <span>Inaktive kreditorer</span>
                    </a>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- SEKTION: KONSULENTER & BRUGERE --}}
            <div class="space-y-2.5">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Konsulenter & Brugere</span>

                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('manage-konsulenter') }}"
                       class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2.5">
                            👤 Administrer Konsulenter
                        </span>
                    </a>

                    <a href="{{ route('users.create') }}"
                       class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        <span class="flex items-center gap-2.5">
                            ➕ Opret Bruger
                        </span>
                    </a>
                </div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 text-center text-xs text-slate-400">
            System Administration • Værktøjspanel
        </div>
    </div>
</div>