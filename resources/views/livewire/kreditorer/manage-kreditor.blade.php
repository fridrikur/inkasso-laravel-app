<div>
@if($kreditor && $kreditor->exists)
<div class="space-y-6">

    {{-- TOP NAVIGATION / BREADCRUMB --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('kreditorer.index') }}" class="hover:text-slate-800 transition">Kreditorer</a>
            <span>/</span>
            <span class="text-slate-900 font-mono">#{{ $this->kreditor->id }}</span>
        </div>

        <div class="flex items-center gap-2">
            {{-- 🏷️ KNAP TIL KREDITORS STATUS OVERSIKT --}}
            <a 
                href="{{ route('admin.sager.status.show', ['status' => 1, 'kreditor_id' => $kreditor->id]) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer"
                title="Se sager for denne kreditor opdelt efter status"
            >
                <span>🏷️ Status oversigt</span>
            </a>

            <button 
                type="button"
                wire:click="$dispatch('edit-kreditor-modal', { id: {{ $this->kreditor->id }} })"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer"
            >
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Rediger stamdata</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- SIDEBAR: STAMDATA & SLET KNAP --}}
        <div class="lg:col-span-1 space-y-6">

            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6 sticky top-6">
                
                {{-- OVERSKRIFT --}}
                <div class="space-y-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100 text-[10px] font-bold uppercase tracking-wider">
                        Lotus ID: {{ $this->kreditor->lotusID ?? 'Mangler' }}
                    </span>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ $this->kreditor->navn }}
                    </h1>
                </div>

                {{-- NØGLETAL --}}
                <div class="space-y-2.5 text-xs border-t border-b border-slate-100 py-4">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Aktive sager</span>
                        <span class="font-bold text-slate-900 font-mono">{{ $this->kreditor->sager_count }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Brugere</span>
                        <span class="font-bold text-slate-900 font-mono">{{ $this->kreditor->users->count() }}</span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Sagsbehandlere</span>
                        <span class="font-bold text-slate-900 font-mono">{{ $this->kreditor->sagsbehandlere->count() }}</span>
                    </div>
                </div>

                {{-- GENVEJE & ACTIONS --}}
                <div class="space-y-2">
                    <a 
                        href="{{ route('admin.sager.status.show', ['status' => 1, 'kreditor_id' => $this->kreditor->id]) }}"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-indigo-50/60 hover:bg-indigo-100/80 border border-indigo-200/60 rounded-xl text-xs font-semibold text-indigo-900 transition"
                    >
                        <span class="flex items-center gap-2">
                            <span>🏷️</span>
                            <span>Sager pr. status</span>
                        </span>
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a 
                        href="{{ route('kreditorer.sager', $this->kreditor) }}"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 transition"
                    >
                        <span>Vis samtlige sager</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a 
                        href="{{ route('sager.import.form', $this->kreditor) }}"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/60 rounded-xl text-xs font-semibold text-slate-700 transition"
                    >
                        <span>Importér sager</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </a>
                </div>

                {{-- SLET KREDITOR KNAP --}}
                <div class="pt-4 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="requestDelete"
                        class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-xl text-xs font-semibold text-rose-700 transition cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Slet kreditor</span>
                    </button>
                </div>

            </div>

        </div>

        {{-- MAIN CONTENT --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- 1. BRUGERE --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tilknyttede Brugere</h2>
                        <p class="text-xs text-slate-500">Systembrugere med adgang til denne kreditors portal</p>
                    </div>

                    <button
                        type="button"
                        wire:click="openUserModal"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Ny bruger</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Navn</th>
                                <th class="px-6 py-3">E-mail</th>
                                <th class="px-6 py-3 text-right">Handlinger</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($this->kreditor->users as $user)
                                <tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-50/60 transition duration-150">
                                    <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $user->name }}</td>
                                    <td class="px-6 py-3.5 font-mono text-slate-500">{{ $user->email }}</td>
                                    <td class="px-6 py-3.5 text-right font-medium">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                wire:click="openUserModal({{ $user->id }})"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-semibold shadow-xs transition cursor-pointer"
                                            >
                                                Redigér
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="detachUser({{ $user->id }})"
                                                wire:confirm="Fjern brugeren fra denne kreditor?"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-200 font-semibold shadow-xs transition cursor-pointer"
                                            >
                                                Fjern
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-400">Ingen tilknyttede brugere endnu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. SAGSBEHANDLERE --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Tilknyttede Sagsbehandlere</h2>
                        <p class="text-xs text-slate-500">Kontaktpersoner og sagsbehandlere hos kreditoren</p>
                    </div>

                    <button
                        type="button"
                        wire:click="openSagsbehandlerModal"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Ny sagsbehandler</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Navn</th>
                                <th class="px-6 py-3">E-mail</th>
                                <th class="px-6 py-3">Telefon / Mobil</th>
                                <th class="px-6 py-3 text-right">Handlinger</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($this->kreditor->sagsbehandlere as $sagsbehandler)
                                <tr wire:key="sags-{{ $sagsbehandler->id }}" class="hover:bg-slate-50/60 transition duration-150">
                                    <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $sagsbehandler->navn }}</td>
                                    <td class="px-6 py-3.5 font-mono text-slate-500">{{ $sagsbehandler->email ?? '-' }}</td>
                                    <td class="px-6 py-3.5 font-mono text-slate-500">
                                        {{ implode(' / ', array_filter([$sagsbehandler->tlf, $sagsbehandler->mobil])) ?: '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-medium">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button
                                                type="button"
                                                wire:click="openSagsbehandlerModal({{ $sagsbehandler->id }})"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-semibold shadow-xs transition cursor-pointer"
                                            >
                                                Redigér
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="detachSagsbehandler({{ $sagsbehandler->id }})"
                                                wire:confirm="Fjern sagsbehandleren fra denne kreditor?"
                                                class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-200 font-semibold shadow-xs transition cursor-pointer"
                                            >
                                                Fjern
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">Ingen tilknyttede sagsbehandlere endnu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. SENESTE SAGER --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Seneste Sager</h2>
                        <p class="text-xs text-slate-500">Viser de seneste 10 sager for denne kreditor</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('admin.sager.status.show', ['status' => 1, 'kreditor_id' => $this->kreditor->id]) }}"
                            class="text-xs font-bold text-slate-600 hover:text-slate-900 transition"
                        >
                            🏷️ Status-opdelt &rarr;
                        </a>
                        <a
                            href="{{ route('kreditorer.sager', $this->kreditor) }}"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition"
                        >
                            Se alle {{ $this->kreditor->sager_count }} sager &rarr;
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Sagsnr</th>
                                <th class="px-6 py-3">Debitor</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Handling</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($this->kreditor->sager as $sag)
                                <tr wire:key="sag-{{ $sag->id }}" class="hover:bg-slate-50/60 transition duration-150">
                                    <td class="px-6 py-3.5 font-mono font-semibold text-slate-900">{{ $sag->sagsnr ?? '#' . $sag->id }}</td>
                                    <td class="px-6 py-3.5 font-medium text-slate-800">
                                        {{ $sag->sagerdebitor->first()?->navn ?? $sag->debitor_navn ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-semibold">
                                            {{ $sag->status ?? 'Aktiv' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-medium">
                                        <a
                                            href="{{ route('sager.edit', $sag) }}"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-900 transition"
                                        >
                                            <span>Åbn sag</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">Ingen sager fundet på denne kreditor.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    {{-- MODAL 1: SLET KREDITOR --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 top-0 left-0 z-50 w-screen h-screen bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5 animate-in fade-in zoom-in-95 duration-150 my-auto">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Slet kreditor</h3>
                        <p class="text-xs font-semibold text-slate-500 font-mono">{{ $this->kreditor->navn }}</p>
                    </div>
                </div>

                @if($this->kreditor->sager_count > 0)
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-amber-900 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Advarsel: {{ $this->kreditor->sager_count }} aktive sager</span>
                        </div>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Kreditoren har sager tilknyttet. Vælg venligst en modtager-kreditor og indtast sikkerhedskoden for at gennemføre overførslen og sletningen:
                        </p>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-amber-900">Vælg modtager-kreditor</label>
                            <select
                                wire:model="transferToKreditorId"
                                class="w-full rounded-xl border border-amber-200 bg-white px-3 py-2 text-xs text-slate-800 shadow-xs focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 focus:outline-hidden"
                            >
                                <option value="">-- Vælg ny kreditor til sagerne --</option>
                                @foreach($transferTargets as $target)
                                    <option value="{{ $target->id }}">{{ $target->navn }}</option>
                                @endforeach
                            </select>
                            @error('transferToKreditorId')
                                <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            Global sikkerhedskode
                        </label>
                        <input
                            type="password"
                            wire:model="securityCode"
                            placeholder="••••••••"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 shadow-xs focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:outline-hidden"
                        >
                        @error('securityCode')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                @else
                    <div class="p-4 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-600 leading-relaxed">
                        Er du sikker på, at du vil slette <strong class="font-semibold text-slate-900">{{ $this->kreditor->navn }}</strong>? Kreditoren og dens tilknyttede brugere/sagsbehandlere vil blive slettet permanent.
                    </div>
                @endif

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        wire:click="closeModals"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer"
                    >
                        Annuller
                    </button>

                    <button
                        type="button"
                        wire:click="confirmDelete"
                        class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer"
                    >
                        @if($this->kreditor->sager_count > 0)
                            Bekræft overførsel & slet
                        @else
                            Slet permanent
                        @endif
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- MODAL 2: BRUGER --}}
    @if($showUserModal)
        <div class="fixed inset-0 top-0 left-0 z-50 w-screen h-screen bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95 duration-150 my-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $activeUser ? 'Redigér bruger' : 'Opret ny bruger' }}
                    </h3>
                    <button type="button" wire:click="closeModals" class="text-slate-400 hover:text-slate-600 transition">&times;</button>
                </div>

                <form wire:submit.prevent="saveUser" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Navn</label>
                        <input type="text" wire:model="userName" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden">
                        @error('userName') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">E-mail</label>
                        <input type="email" wire:model="userEmail" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden">
                        @error('userEmail') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Adgangskode {{ $activeUser ? '(valgfri ved ændring)' : '' }}
                        </label>
                        <input type="password" wire:model="userPassword" placeholder="••••••••" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden">
                        @error('userPassword') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeModals" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer">Annullér</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition cursor-pointer">Gem bruger</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL 3: SAGSBEHANDLER --}}
    @if($showSagsModal)
        <div class="fixed inset-0 top-0 left-0 z-50 w-screen h-screen bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-4 animate-in fade-in zoom-in-95 duration-150 my-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ $activeSagsbehandler ? 'Redigér sagsbehandler' : 'Opret sagsbehandler' }}
                    </h3>
                    <button type="button" wire:click="closeModals" class="text-slate-400 hover:text-slate-600 transition">&times;</button>
                </div>

                <form wire:submit.prevent="saveSagsbehandler" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Navn</label>
                        <input type="text" wire:model="modalNavn" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden">
                        @error('modalNavn') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">E-mail</label>
                        <input type="email" wire:model="modalEmail" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden">
                        @error('modalEmail') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Telefon</label>
                            <input type="text" wire:model="modalTlf" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Mobil</label>
                            <input type="text" wire:model="modalMobil" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-hidden">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="closeModals" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer">Annullér</button>
                        <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition cursor-pointer">Gem sagsbehandler</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL: KREDITOR STAMDATA --}}
    @livewire('kreditor.kreditor-form-modal')

</div>
@endif
</div>