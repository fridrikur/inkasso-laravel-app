<div>
    <form wire:submit.prevent="save" class="space-y-8">

        {{-- BASIC INFO --}}
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">
                    Brugeroplysninger
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Opdater navn, e-mail og eventuelt adgangskode.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- NAME --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Navn
                    </label>
                    <input
                        type="text"
                        wire:model.defer="form.name"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('form.name')
                        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        E-mail
                    </label>
                    <input
                        type="email"
                        wire:model.defer="form.email"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('form.email')
                        <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- PASSWORD --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Nyt password
                </label>
                <input
                    type="password"
                    wire:model.defer="form.password"
                    placeholder="Lad stå tomt for at beholde nuværende password"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                @error('form.password')
                    <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- ROLE SECTION --}}
        <div class="rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-900">Rolle & adgang</h3>
                    <p class="text-sm text-slate-500 mt-1">
                        Rollen er skjult som standard for at holde formularen enkel.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="$toggle('showRoleEditor')"
                    class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-300 hover:bg-white text-sm font-medium text-slate-700 transition"
                >
                    {{ $showRoleEditor ? 'Skjul rollevalg' : 'Redigér rolle' }}
                </button>
            </div>

            <div class="p-5 space-y-5">
                {{-- CURRENT ROLE --}}
                <div>
                    <div class="text-sm text-slate-500 mb-2">Nuværende rolle</div>

                    @php
                        $currentRole = $user->roles->first()?->name ?? 'Ingen rolle';
                    @endphp

                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                        @class([
                            'bg-red-100 text-red-700' => $currentRole === 'Admin',
                            'bg-blue-100 text-blue-700' => $currentRole === 'Medarbejder',
                            'bg-green-100 text-green-700' => $currentRole === 'Kreditor',
                            'bg-slate-100 text-slate-700' => !in_array($currentRole, ['Admin', 'Medarbejder', 'Kreditor']),
                        ])
                    ">
                        {{ $currentRole }}
                    </span>
                </div>

                {{-- ROLE EDITOR --}}
                @if($showRoleEditor)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Rolle
                        </label>

                        <select
                            wire:model.live="form.role"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Vælg rolle</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>

                        @error('form.role')
                            <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                {{-- KREDITOR --}}
                @php
                    $shouldShowKreditor =
                        ($showRoleEditor && $form['role'] === 'Kreditor')
                        || (!$showRoleEditor && $user->hasRole('Kreditor'));
                @endphp

                @if($shouldShowKreditor)
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Kreditor
                        </label>

                        <select
                            wire:model.defer="form.kreditor_id"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Vælg kreditor</option>

                            @foreach($kreditors as $kreditor)
                                <option value="{{ $kreditor->id }}">
                                    {{ $kreditor->navn }}
                                </option>
                            @endforeach
                        </select>

                        @error('form.kreditor_id')
                            <div class="text-sm text-red-600 mt-2">{{ $message }}</div>
                        @enderror

                        @if($user->kreditorer->isNotEmpty())
                            <p class="text-sm text-slate-500 mt-2">
                                Nuværende tilknytning:
                                <span class="font-medium text-slate-700">
                                    {{ $user->kreditorer->pluck('navn')->join(', ') }}
                                </span>
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <button
                type="button"
                wire:click="$parent.closeModals"
                class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 transition"
            >
                Luk
            </button>
                
            <button
                type="submit"
                class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-sm transition"
            >
                Gem ændringer
            </button>
        </div>
    </form>
</div>