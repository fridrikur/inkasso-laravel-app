<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Brugere
            </h1>
            <p class="text-slate-500 mt-0.5 text-xs">
                Administration af brugerkonti, roller og adgangsrettigheder
            </p>
        </div>

        {{-- OPRET BRUGER KNAP --}}
        @if($roleFilter === 'Kreditor' && ! $hasKreditorer)
            <button 
                type="button" 
                wire:click="openModal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-300 text-slate-500 text-xs font-bold shadow-xs cursor-not-allowed shrink-0"
                title="Opret venligst en kreditor i systemet først"
            >
                <span>⚠️ Opret kreditor først</span>
            </button>
        @else
            <a 
                href="{{ route('users.create') }}" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition shrink-0 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Opret {{ $roleFilter ? strtolower($roleFilter) : 'bruger' }}</span>
            </a>
        @endif
    </div>

    {{-- ⚠️ ADVARSEL OM MANGLENDE KREDITORER --}}
    @if($roleFilter === 'Kreditor' && ! $hasKreditorer)
        <div class="bg-amber-50 rounded-2xl border border-amber-200/80 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
            <div class="flex items-start gap-3">
                <span class="text-2xl shrink-0">🏢</span>
                <div>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-amber-900">
                        Ingen kreditorer registreret i databasen
                    </h3>
                    <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                        For at oprette en portalbruger med rullen <strong>Kreditor</strong>, skal der først oprettes mindst én kreditorvirksomhed i systemet.
                    </p>
                </div>
            </div>

            <a 
                href="{{ route('kreditorer.index') }}" 
                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition shrink-0 cursor-pointer text-center"
            >
                + Opret første kreditor
            </a>
        </div>
    @endif
                
    {{-- STATS KORT --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Samtlige Brugere</div>
                <div class="text-2xl font-bold mt-1 text-slate-900">{{ $totalUsers }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-base">
                👥
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Administratorer</div>
                <div class="text-2xl font-bold mt-1 text-slate-900">{{ $adminCount }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base">
                👑
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Medarbejdere</div>
                <div class="text-2xl font-bold mt-1 text-slate-900">{{ $medarbejderCount }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base">
                💼
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 shadow-xs border border-slate-200/80 flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kreditorer</div>
                <div class="text-2xl font-bold mt-1 text-slate-900">{{ $kreditorCount }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-base">
                🏢
            </div>
        </div>
    </div>

    {{-- KREDITOR VIRKSOMHEDSFILTER --}}
    @if($roleFilter === 'Kreditor' && $hasKreditorer)
        <div class="bg-amber-50/50 rounded-2xl border border-amber-200/60 p-4 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                        <span>🏢</span> Filtrér på Kreditorvirksomhed
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Vis kun brugere tilknyttet en bestemt kreditor.</p>
                </div>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="kreditorSearch"
                    class="w-full sm:w-64 rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs outline-none focus:border-amber-500"
                    placeholder="Søg virksomhed..."
                >
            </div>

            <div class="flex gap-1.5 overflow-x-auto pt-1">
                <button
                    type="button"
                    wire:click="$set('kreditor_id', null)"
                    class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ !$kreditor_id ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
                >
                    Alle virksomheder
                </button>

                @forelse($kreditors as $kreditor)
                    <button
                        type="button"
                        wire:click="$set('kreditor_id', {{ $kreditor->id }})"
                        class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $kreditor_id === $kreditor->id ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
                    >
                        {{ $kreditor->navn }}
                    </button>
                @empty
                    <div class="text-xs text-slate-400 py-1">
                        Ingen virksomheder fundet.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- TABEL OG FILTRE --}}
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden relative">
        
        {{-- CONTROLS HEADER --}}
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 space-y-4">
            
            {{-- TITEL OG SØGNING --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                    <span>🔍</span> Brugere & Filtrering
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative w-full sm:w-80">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Søg på navn eller e-mail..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 pl-9 text-xs text-slate-800 shadow-xs transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 focus:outline-none"
                        >
                        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <select
                        wire:model.live="perPage"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-xs outline-none cursor-pointer"
                    >
                        <option value="10">10 / side</option>
                        <option value="25">25 / side</option>
                        <option value="50">50 / side</option>
                    </select>
                </div>
            </div>

            {{-- ROLLE FANER --}}
            <div class="flex flex-wrap gap-1.5 pt-2 border-t border-slate-200/40">
                <button
                    type="button"
                    wire:click="setRoleFilter(null)"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer {{ empty($roleFilter) ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>Samtlige brugere</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ empty($roleFilter) ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-300 text-slate-700' }}">{{ $totalUsers }}</span>
                </button>

                <button
                    type="button"
                    wire:click="setRoleFilter('Medarbejder')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer {{ $roleFilter === 'Medarbejder' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>💼 Medarbejder</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $roleFilter === 'Medarbejder' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-300 text-slate-700' }}">{{ $medarbejderCount }}</span>
                </button>

                <button
                    type="button"
                    wire:click="setRoleFilter('Kreditor')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer {{ $roleFilter === 'Kreditor' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>🏢 Kreditor</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $roleFilter === 'Kreditor' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-300 text-slate-700' }}">{{ $kreditorCount }}</span>
                </button>

                <button
                    type="button"
                    wire:click="setRoleFilter('Admin')"
                    class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 select-none cursor-pointer {{ $roleFilter === 'Admin' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>👑 Admin</span>
                    <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $roleFilter === 'Admin' ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-300 text-slate-700' }}">{{ $adminCount }}</span>
                </button>
            </div>

        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200/60">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Bruger / Navn</th>
                        <th scope="col" class="px-6 py-3.5">E-mail</th>
                        <th scope="col" class="px-6 py-3.5">Rolle</th>

                        @if($roleFilter === 'Kreditor')
                            <th scope="col" class="px-6 py-3.5">Tilknyttet Virksomhed</th>
                        @endif

                        <th scope="col" class="px-6 py-3.5 text-right whitespace-nowrap">Handling</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-50/60 transition duration-150">
                            
                            {{-- NAVN & AVATAR --}}
                            <td class="px-6 py-3.5 font-semibold text-slate-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('users.user.manage', $user) }}" class="font-bold text-slate-900 text-xs hover:text-indigo-600 transition">
                                            {{ $user->name }}
                                        </a>
                                        <span class="block text-[10px] font-mono text-slate-400">#{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- EMAIL --}}
                            <td class="px-6 py-3.5 text-xs text-slate-600 font-medium">
                                {{ $user->email }}
                            </td>

                            {{-- ROLLE BADGE --}}
                            <td class="px-6 py-3.5 text-xs">
                                @if($user->roles->first()?->name === 'Admin')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                        👑 Admin
                                    </span>
                                @elseif($user->roles->first()?->name === 'Kreditor')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                        🏢 Kreditor
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                        💼 Medarbejder
                                    </span>
                                @endif
                            </td>

                            {{-- VIRKSOMHED --}}
                            @if($roleFilter === 'Kreditor')
                                <td class="px-6 py-3.5 text-xs text-slate-600">
                                    @if($user->kreditorer->isNotEmpty())
                                        <span class="font-semibold text-slate-800">{{ $user->kreditorer->pluck('navn')->join(', ') }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Ikke tilknyttet</span>
                                    @endif
                                </td>
                            @endif

                            {{-- 🟢 HANDLINGER: INTEGRERET X-TABLE-ACTIONS + ADMINISTRER KNAP --}}
                            <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    
                                    {{-- 1. FULD BEHANDLING (DEDIKERET MANAGE-USER SIDE) --}}
                                    <a 
                                        href="{{ route('users.user.manage', $user) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200/60 text-xs font-bold rounded-xl transition cursor-pointer"
                                        title="Fuld behandling af brugerprofil"
                                    >
                                        <span>Administrer</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>

                                    {{-- 2. X-TABLE-ACTIONS (ØJE TIL SIDE, REDIGER TIL MODAL, SLET) --}}
                                    <x-table-actions 
                                        :id="$user->id" 
                                        :viewUrl="route('users.user.manage', $user)"
                                        editAction="openModal"
                                        deleteAction="confirmDelete"
                                        :showDelete="$user->id !== 1"
                                    />
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $roleFilter === 'Kreditor' ? '5' : '4' }}" class="px-6 py-12 text-center text-slate-400 text-xs">
                                Ingen brugere fundet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-t border-slate-100 bg-slate-50/30">
            {{ $users->links() }}
        </div>
    </div>

    {{-- EDIT MODAL (REDIERING I MODAL) --}}
    @if($showFormModal)
        @include('livewire.users.partials.edit-user-modal')
    @endif

    {{-- SLETTEMODAL (DEAKTIVATION BEKRÆFTELSE) --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
                <button type="button" wire:click="closeModals" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">&times;</button>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Deaktiver bruger / konsulent?</h3>
                    </div>
                </div>

                <p class="text-xs text-slate-600">
                    {{ $userHasSagerCount > 0 
                        ? 'Denne bruger er registreret som konsulent på ' . $userHasSagerCount . ' sag(er). Deaktiveringen vil SoftDelete brugeren, så vedkommende mister sin adgang, men alle historiske sagsdata og aktiviteter bevares uændret.' 
                        : 'Er du sikker på, at du vil deaktivere denne bruger? Brugeren mister sin adgang til systemet, men historiske data bevares.' }}
                </p>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeModals" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">Annuller</button>
                    <button type="button" wire:click="deleteUser" class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer">Deaktiver bruger</button>
                </div>
            </div>
        </div>
    @endif

</div>