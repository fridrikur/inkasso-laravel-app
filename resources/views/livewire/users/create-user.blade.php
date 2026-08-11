<div class="max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Opret Ny Bruger
            </h1>
            <p class="text-slate-500 mt-0.5 text-xs">
                Opret en ny brugerkonto og tildel adgangsrolle i systemet
            </p>
        </div>

        <a 
            href="{{ route('users.index') }}" 
            class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
        >
            ← Tilbage til oversigt
        </a>
    </div>

    {{-- FORMULAR CARD --}}
    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 p-6 space-y-6">

        {{-- ROLLE VÆLGER (FANER) --}}
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                Vælg Brugerrolle
            </label>
            <div class="bg-slate-100 p-1.5 rounded-2xl flex gap-1">
                <button 
                    type="button" 
                    wire:click="$set('role', 'Medarbejder')" 
                    class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition cursor-pointer {{ $role === 'Medarbejder' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}"
                >
                    💼 Medarbejder
                </button>

                <button 
                    type="button" 
                    wire:click="$set('role', 'Kreditor')" 
                    class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition cursor-pointer {{ $role === 'Kreditor' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}"
                >
                    🏢 Kreditor
                </button>

                <button 
                    type="button" 
                    wire:click="$set('role', 'Admin')" 
                    class="flex-1 py-2 px-3 rounded-xl text-xs font-bold transition cursor-pointer {{ $role === 'Admin' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-900' }}"
                >
                    👑 Admin
                </button>
            </div>
        </div>

        {{-- FORMULAR --}}
        <form wire:submit="save" class="space-y-5">

            {{-- NAVN OG EMAIL --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Navn *
                    </label>
                    <input 
                        type="text" 
                        wire:model.blur="name" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none" 
                        placeholder="Indtast fulde navn"
                    >
                    @error('name') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        E-mail *
                    </label>
                    <input 
                        type="email" 
                        wire:model.blur="email" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none" 
                        placeholder="mail@firma.dk"
                    >
                    @error('email') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            {{-- ADGANGSKODE & BEKRÆFTELSE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Adgangskode *
                    </label>
                    <input 
                        type="password" 
                        wire:model="password" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        placeholder="••••••••"
                    >
                    @error('password') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Bekræft adgangskode *
                    </label>
                    <input 
                        type="password" 
                        wire:model="password_confirmation" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none"
                        placeholder="••••••••"
                    >
                </div>
            </div>

            {{-- KREDITOR VÆLGER (KUN HVIS ROLLEN ER KREDITOR) --}}
            @if($role === 'Kreditor')
                <div class="pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Tilknyt Virksomhed / Kreditor *
                    </label>
                    <select 
                        wire:model="kreditor_id" 
                        class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 outline-none bg-white cursor-pointer"
                    >
                        <option value="">Vælg virksomhed...</option>
                        @foreach(\App\Models\Kreditorer::orderBy('navn')->get() as $kreditor)
                            <option value="{{ $kreditor->id }}">{{ $kreditor->navn }}</option>
                        @endforeach
                    </select>
                    @error('kreditor_id') 
                        <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            @endif

            {{-- ROLLE INFOBOX --}}
            <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-3.5 text-xs text-slate-600">
                @if($role === 'Admin')
                    <p><strong>👑 Admin:</strong> Fuld adgang til alle systemets funktioner, brugere og systemindstillinger.</p>
                @elseif($role === 'Kreditor')
                    <p><strong>🏢 Kreditor:</strong> Får direkte adgang til Kreditor Portalen for sin tilknyttede virksomhed.</p>
                @else
                    <p><strong>💼 Medarbejder:</strong> Får adgang til den daglige sagsbehandling og internt værktøjssæt.</p>
                @endif
            </div>

            {{-- KNAPPER --}}
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a 
                    href="{{ route('users.index') }}" 
                    class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition cursor-pointer"
                >
                    Annullér
                </a>

                <button 
                    type="submit" 
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition cursor-pointer"
                >
                    Opret {{ strtolower($role) }}
                </button>
            </div>

        </form>
    </div>

</div>