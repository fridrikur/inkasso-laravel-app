<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                Brugere
            </h1>
            <p class="text-slate-500 mt-1 text-sm">
                Administration af brugere og roller
            </p>
        </div>

        {{-- OPRET BRUGER KNAP --}}
        <button 
            type="button" 
            wire:click="openModal()" 
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition shrink-0 cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Opret {{ $roleFilter ? strtolower($roleFilter) : 'bruger' }}</span>
        </button>
    </div>
                
    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Brugere</div>
            <div class="text-3xl font-bold mt-2 text-slate-900">{{ $totalUsers }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Administratorer</div>
            <div class="text-3xl font-bold mt-2 text-slate-900">{{ $adminCount }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Medarbejdere</div>
            <div class="text-3xl font-bold mt-2 text-slate-900">{{ $medarbejderCount }}</div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kreditorbrugere</div>
            <div class="text-3xl font-bold mt-2 text-slate-900">{{ $kreditorCount }}</div>
        </div>
    </div>

    {{-- KREDITOR FILTER --}}
    @if($roleFilter === 'Kreditor')
        <div class="bg-indigo-50/40 rounded-2xl border border-indigo-100 p-5 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-xs uppercase tracking-wider text-indigo-900">Filtrér på virksomhed</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Vis kun kreditorbrugere tilknyttet en specifik virksomhed.</p>
                </div>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="kreditorSearch"
                    class="w-full sm:w-64 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs outline-none focus:border-indigo-500"
                    placeholder="Søg virksomhed..."
                >
            </div>

            <div class="flex gap-1.5 overflow-x-auto pt-1">
                <button
                    type="button"
                    wire:click="$set('kreditor_id', null)"
                    class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ !$kreditor_id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
                >
                    Alle kreditorer
                </button>

                @forelse($kreditors as $kreditor)
                    <button
                        type="button"
                        wire:click="$set('kreditor_id', {{ $kreditor->id }})"
                        class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer {{ $kreditor_id === $kreditor->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}"
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
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden relative">
        
        {{-- CONTROLS HEADER --}}
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 space-y-4">
            
            {{-- TITEL OG SØGNING --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 w-full">
                <div class="text-xs font-bold uppercase tracking-wider text-slate-700">
                    Filtrér brugere
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative w-full sm:w-80">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Søg på navn eller e-mail..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 pl-10 text-xs text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none"
                        >
                        <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <select
                        wire:model.live="perPage"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 shadow-sm outline-none cursor-pointer"
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
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none cursor-pointer {{ empty($roleFilter) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                >
                    <span>Samtlige brugere</span>
                </button>

                @foreach(['Admin' => 'Admin', 'Medarbejder' => 'Medarbejder', 'Kreditor' => 'Kreditor'] as $roleKey => $roleLabel)
                    <button
                        type="button"
                        wire:click="setRoleFilter('{{ $roleKey }}')"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all flex items-center gap-2 select-none cursor-pointer {{ $roleFilter === $roleKey ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-200/60 text-slate-600 hover:bg-slate-200' }}"
                    >
                        <span>{{ $roleLabel }}</span>
                    </button>
                @endforeach
            </div>

        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/60">
                    <tr>
                        <th scope="col" class="px-6 py-4">Navn</th>
                        <th scope="col" class="px-6 py-4">E-mail</th>

                        @if($roleFilter === 'Kreditor')
                            <th scope="col" class="px-6 py-4">Kreditor</th>
                        @endif

                        <th scope="col" class="px-6 py-4 text-right w-32">Handling</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-50/60 transition duration-150">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $user->name }}
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                {{ $user->email }}
                            </td>

                            @if($roleFilter === 'Kreditor')
                                <td class="px-6 py-4 text-slate-600">
                                    @if($user->kreditorer->isNotEmpty())
                                        {{ $user->kreditorer->pluck('navn')->join(', ') }}
                                    @else
                                        <span class="text-slate-400 italic">Ikke tilknyttet</span>
                                    @endif
                                </td>
                            @endif

                            {{-- SVG ACTION KNAPPER --}}
                            <x-table-actions 
                                :id="$user->id" 
                                :canDelete="true" 
                            />
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="{{ $roleFilter === 'Kreditor' ? 4 : 3 }}"
                                class="px-6 py-16 text-center text-slate-400"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-8 w-8 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="block text-sm font-semibold text-slate-900">Ingen brugere fundet</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-5 border-t border-slate-100 bg-slate-50/30">
            {{ $users->links() }}
        </div>
    </div>

    {{-- EDIT / CREATE MODAL --}}
    @if($showUserModal)
        @include('livewire.users.partials.edit-user-modal')
    @endif

    {{-- SLETTEMODAL --}}
    <x-confirm-delete-modal 
        :show="$showDeleteModal" 
        title="Deaktiver bruger / konsulent?" 
        :message="$userHasSagerCount > 0 
            ? 'Denne bruger er registreret som konsulent på ' . $userHasSagerCount . ' sag(er). Deaktiveringen vil SoftDelete brugeren, så vedkommende mister sin adgang, men alle historiske sagsdata og aktiviteter bevares uændret.' 
            : 'Er du sikker på, at du vil deaktivere denne bruger? Brugeren mister sin adgang til systemet, men historiske data bevares.'" 
    />

</div>