<div class="space-y-6">

    {{-- BREADCRUMB & TOP ACTIONS --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('users.index') }}" class="hover:text-slate-800 transition">Brugere</a>
            <span>/</span>
            <span class="text-slate-900 font-mono">#{{ $user->id }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if($user->trashed())
                {{-- GENSKAB KNAP TIL DEAKTIVEREDE BRUGERE --}}
                <button 
                    type="button"
                    wire:click="restoreUser"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer"
                >
                    <span>⚡ Genskab / Aktiver Bruger</span>
                </button>
            @else
                <button 
                    type="button"
                    wire:click="openPasswordModal"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer"
                >
                    🔑 Nulstil adgangskode
                </button>

                <button 
                    type="button"
                    wire:click="openEditModal"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-xl shadow-xs transition cursor-pointer"
                >
                    ✏️ Redigér profildata
                </button>
            @endif
        </div>
    </div>

    {{-- HVIS BRUGEREN ER DEAKTIVERET (SOFT-DELETED BANNER) --}}
    @if($user->trashed())
        <div class="bg-rose-50 border border-rose-200 rounded-3xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
            <div class="flex items-start gap-3">
                <span class="text-2xl shrink-0">⚠️</span>
                <div>
                    <h3 class="font-bold text-sm text-rose-900">Brugeren er deaktiveret (Soft-deleted)</h3>
                    <p class="text-xs text-rose-700 mt-0.5">
                        Denne brugerkonto er markeret som inaktiv og kan ikke logge ind i systemet.
                    </p>
                </div>
            </div>

            <button 
                type="button"
                wire:click="restoreUser"
                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer shrink-0"
            >
                Genskab adgang nu
            </button>
        </div>
    @endif

    {{-- HOVEDGRID: SIDEBAR & INDHOLD --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- SIDEBAR: KORT MED STAMDATA --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-6 sticky top-6">
                
                {{-- AVATAR OG NAVN --}}
                <div class="text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-600 font-black text-2xl flex items-center justify-center mx-auto shadow-inner">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div>
                        <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                            {{ $user->name }}
                        </h1>
                        <p class="text-xs font-mono text-slate-500 mt-0.5">
                            {{ $user->email }}
                        </p>
                    </div>

                    <div>
                        @if($user->roles->first()?->name === 'Admin')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                👑 Admin
                            </span>
                        @elseif($user->roles->first()?->name === 'Kreditor')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                🏢 Kreditor
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                💼 Medarbejder
                            </span>
                        @endif
                    </div>
                </div>

                {{-- NØGLETAL & HISTORIK --}}
                <div class="space-y-2.5 text-xs border-t border-b border-slate-100 py-4">
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Status</span>
                        <span class="font-bold {{ $user->trashed() ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $user->trashed() ? 'Deaktiveret' : 'Aktiv' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-slate-600">
                        <span>Oprettet dato</span>
                        <span class="font-mono text-slate-900">{{ $user->created_at?->format('d-m-Y') ?? '-' }}</span>
                    </div>

                    @if($user->kreditorer->isNotEmpty())
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Kreditor</span>
                            <span class="font-bold text-slate-900">{{ $user->kreditorer->first()->navn }}</span>
                        </div>
                    @endif
                </div>

                {{-- DEAKTIVER KNAP (KUN FOR IKKE-SLETTEDE OG IKKE-BRUGER #1) --}}
                @if(!$user->trashed() && $user->id !== 1)
                    <div class="pt-2">
                        <button
                            type="button"
                            wire:click="requestDeactivate"
                            class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-xl text-xs font-semibold text-rose-700 transition cursor-pointer"
                        >
                            <span>Deaktiver brugerkonto</span>
                        </button>
                    </div>
                @endif

            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- 1. TILKNYTTET VIRKSOMHED (HVIS KREDITOR) --}}
            @if($user->roles->first()?->name === 'Kreditor')
                <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span>🏢</span> Tilknyttet Kreditorvirksomhed
                    </h2>

                    @if($user->kreditorer->isNotEmpty())
                        @foreach($user->kreditorer as $kreditor)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200/60">
                                <div>
                                    <h3 class="font-bold text-sm text-slate-900">{{ $kreditor->navn }}</h3>
                                    <p class="text-xs font-mono text-slate-400 mt-0.5">Lotus ID: {{ $kreditor->lotusID }}</p>
                                </div>

                                <a 
                                    href="{{ route('kreditor.manage', $kreditor) }}" 
                                    class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-bold rounded-xl transition"
                                >
                                    Åbn Kreditor &rarr;
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 bg-amber-50 text-amber-800 text-xs rounded-2xl border border-amber-200">
                            Brugeren har Kreditor-rollen, men er endnu ikke tilknyttet en kreditorvirksomhed.
                        </div>
                    @endif
                </div>
            @endif

            {{-- 2. AKTIVITETS & SIKKERHEDS INFO --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <span>🛡️</span> Sikkerhed & Rettigheder
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">To-faktor godkendelse</span>
                        <p class="font-bold text-slate-800">{{ $user->two_factor_secret ? 'Tilsluttet' : 'Ikke aktiveret' }}</p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                        <span class="text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Tildelt systemrolle</span>
                        <p class="font-bold text-indigo-600">{{ $user->roles->first()?->name ?? 'Ingen rolle' }}</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- MODAL 1: REDIGÉR STAMDATA --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Redigér brugeroplysninger</h3>

                <form wire:submit.prevent="saveStamdata" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Navn</label>
                        <input type="text" wire:model="name" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 outline-none focus:border-indigo-500">
                        @error('name') <p class="text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">E-mail</label>
                        <input type="email" wire:model="email" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 outline-none focus:border-indigo-500">
                        @error('email') <p class="text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Rolle</label>
                        <select wire:model.live="selectedRole" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-800 outline-none">
                            @foreach($allRoles as $roleOption)
                                <option value="{{ $roleOption->name }}">{{ $roleOption->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedRole === 'Kreditor')
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Tilknyt Kreditorvirksomhed</label>
                            <select wire:model="assignedKreditorId" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-800 outline-none">
                                <option value="">-- Vælg kreditor --</option>
                                @foreach($allKreditorer as $kred)
                                    <option value="{{ $kred->id }}">{{ $kred->navn }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showEditModal', false)" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Annuller</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl">Gem ændringer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- MODAL 2: NULSTIL ADGANGSKODE --}}
    @if($showPasswordModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Skift adgangskode</h3>

                <form wire:submit.prevent="updatePassword" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Ny adgangskode</label>
                        <input type="password" wire:model="newPassword" placeholder="••••••••" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 outline-none focus:border-indigo-500">
                        @error('newPassword') <p class="text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider mb-1">Gentag ny adgangskode</label>
                        <input type="password" wire:model="newPassword_confirmation" placeholder="••••••••" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-800 outline-none focus:border-indigo-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" wire:click="$set('showPasswordModal', false)" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-semibold">Annuller</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl">Opdater adgangskode</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- 🔴 MODAL 3: DEAKTIVER BRUGER (DIT MODAL DESIGN) --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-slate-100 space-y-2 animate-in fade-in zoom-in-95 duration-150">
                <h2 class="text-lg font-bold text-slate-900" id="modal-title">
                    Deaktiver bruger / konsulent?
                </h2>

                <p class="mt-2 text-sm text-slate-600">
                    Er du sikker på, at du vil deaktivere denne bruger? Brugeren mister sin adgang til systemet, men historiske data bevares.
                </p>

                <div class="mt-6 flex justify-end gap-3 pt-2">
                    <button 
                        type="button" 
                        wire:click="cancelDelete" 
                        class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer"
                    >
                        Annuller
                    </button>

                    <button 
                        type="button" 
                        wire:click="confirmDelete" 
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-sm cursor-pointer"
                    >
                        Slet
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>