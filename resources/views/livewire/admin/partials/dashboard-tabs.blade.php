{{-- resources/views/livewire/admin/partials/dashboard-tabs.blade.php --}}
<div class="mt-6">
    <div class="flex items-center justify-between gap-4 overflow-x-auto pb-1">
        
        <nav class="inline-flex rounded-2xl bg-slate-200/60 p-1.5 text-slate-600 shadow-inner">
            
            {{-- OVERVIEW TAB --}}
            <button
                type="button"
                wire:click="setTab('overview')"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 select-none
                    {{ $activeTab === 'overview'
                        ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Overview</span>
            </button>

            {{-- SAGER TAB --}}
            <button
                type="button"
                wire:click="setTab('sager')"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 select-none
                    {{ $activeTab === 'sager'
                        ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <span>Sager</span>
                <span class="inline-flex items-center rounded-lg bg-indigo-50 border border-indigo-100 px-2 py-0.5 text-xs font-mono font-bold text-indigo-700">
                    {{ number_format($totalSager, 0, ',', '.') }}
                </span>
            </button>

            {{-- BRUGERE & ROLLER TAB --}}
            <button
                type="button"
                wire:click="setTab('users')"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 select-none
                    {{ $activeTab === 'users'
                        ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Brugere & roller</span>
            </button>

            {{-- SYSTEMSTATUS TAB --}}
            <button
                type="button"
                wire:click="setTab('warnings')"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all duration-200 select-none
                    {{ $activeTab === 'warnings'
                        ? 'bg-white text-indigo-600 shadow-sm ring-1 ring-black/5'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ count($systemWarnings) ? 'text-amber-500' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Systemstatus</span>
                @if(count($systemWarnings))
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                @endif
            </button>

        </nav>
    </div>
</div>