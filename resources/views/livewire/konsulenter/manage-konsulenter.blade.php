<div class="space-y-6">

    {{-- STATISTICS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total konsulenter</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $konsulenter->total() }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Hovedkonsulent</p>
            <p class="mt-2 text-xl font-bold text-indigo-600 truncate">
                {{ \App\Models\HovedKonsulent::current()?->navn ?? 'Ingen valgt' }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Notifikation</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ \App\Models\NotifikationsKonsulent::count() }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-xs">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Skjulte</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ \App\Models\SkjultKonsulent::count() }}</p>
        </div>
    </div>

    {{-- LEGEND --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-xs">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4">Konsulentroller</h2>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-indigo-50/70 border border-indigo-100 p-4">
                <div class="font-bold text-indigo-700 text-xs uppercase tracking-wider">⭐ Hovedkonsulent</div>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Den primære ansvarlige konsulent. Der kan kun eksistere én.
                </p>
            </div>

            <div class="rounded-xl bg-emerald-50/70 border border-emerald-100 p-4">
                <div class="font-bold text-emerald-700 text-xs uppercase tracking-wider">🔔 Notifikationskonsulent</div>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Modtager systemnotifikationer. Flere kan vælges.
                </p>
            </div>

            <div class="rounded-xl bg-slate-100/70 border border-slate-200/60 p-4">
                <div class="font-bold text-slate-700 text-xs uppercase tracking-wider">🙈 Skjult konsulent</div>
                <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                    Vises ikke i normale valg. Flere kan vælges.
                </p>
            </div>
        </div>
    </div>

    {{-- 🟢 DATA TABLE MED PRÆCIS 3 HEADERS (+ AUTOMATISK HANDLINGSKOLONNE) --}}
    <x-data-table 
        title="Konsulenter" 
        description="Administrer konsulenter, roller og ansvar."
        :headers="['Navn', 'Email', 'Roller']"
        :items="$konsulenter"
        wire:model.live="search"
    >
        {{-- TILPASSET HEADER ACTION MED ROLLE-FANER OG OPRET KNAP --}}
        <x-slot:action>
            <div class="flex flex-col xl:flex-row items-stretch xl:items-center gap-3 w-full xl:w-auto">
                <div class="flex flex-wrap gap-1 bg-slate-100 p-1 rounded-xl">
                    @foreach([
                        'alle'   => 'Alle',
                        'hoved'  => '⭐ Hoved',
                        'notif'  => '🔔 Notifikation',
                        'skjult' => '🙈 Skjulte'
                    ] as $key => $label)
                        <button
                            type="button"
                            wire:click="setRoleTab('{{ $key }}')"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer select-none {{ $activeRoleTab === $key ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <button 
                    type="button" 
                    wire:click="openCreateModal" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Opret konsulent</span>
                </button>
            </div>
        </x-slot:action>

        {{-- TABEL RÆKKER --}}
        @forelse($konsulenter as $k)
            <tr wire:key="konsulent-{{ $k->id }}" class="hover:bg-slate-50/60 transition">
                <td class="px-6 py-4 font-bold text-slate-900 text-xs whitespace-nowrap">
                    {{ $k->navn }}
                </td>

                <td class="px-6 py-4 text-xs font-mono text-slate-500 whitespace-nowrap">
                    {{ $k->email }}
                </td>

                <td class="px-6 py-4">
                    <div class="flex flex-wrap items-center gap-1.5">
                        @foreach($k->visibleRoles() as $role)
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $role['class'] }}">
                                {{ $role['icon'] }} {{ $role['name'] }}
                            </span>
                        @endforeach
                    </div>
                </td>

                {{-- 🟢 INDPAK <X-TABLE-ACTIONS> KORREKT I EN <TD> --}}
                    {{-- 🟢 ENSEARTET HANDLINGSKOLONNE MED X-TABLE-ACTIONS --}}
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                    <x-table-actions 
                        :id="$k->id" 
                        editAction="openEditModal"
                        deleteAction="confirmDelete"
                        :showView="false"
                    />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">
                    Ingen konsulenter fundet med de valgte kriterier.
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- FORMULAR MODAL (OPRET / REDIGER) --}}
    @include('livewire.konsulenter.partials.modal')

    @if($showStandaloneTransferModal && $konsulentToTransferFrom)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
                <h3 class="text-lg font-bold text-slate-900">Overfør sager før sletning</h3>
                <p class="text-xs text-slate-500">
                    Konsulenten <strong>{{ $konsulentToTransferFrom->navn }}</strong> er tilknyttet <strong>{{ $userHasSagerCount }}</strong> aktiv(e) sag(er). Vælg hvem sagerne skal overføres til:
                </p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Overfør til konsulent</label>
                    <select wire:model="transferToKonsulentId" class="w-full rounded-xl border border-slate-200 text-xs p-2.5">
                        <option value="">Vælg konsulent...</option>
                        @foreach(\App\Models\Konsulenter::where('id', '!=', $konsulentToTransferFrom->id)->get() as $other)
                            <option value="{{ $other->id }}">{{ $other->navn }}</option>
                        @endforeach
                    </select>
                    @error('transferToKonsulentId') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="cancelTransfer" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        Annuller
                    </button>
                    <button type="button" wire:click="transferAndClose" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition cursor-pointer">
                        Overfør sager & slet
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- DIREKTE SLETTEMODAL (UAFHÆNGIG AF GLOBAL MODAL) --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
                <button type="button" wire:click="cancelDelete" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition">&times;</button>
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Slet konsulent?</h3>
                    </div>
                </div>

                <p class="text-xs text-slate-600">
                    Er du sikker på, at du vil slette denne konsulent? Denne handling kan ikke fortrydes.
                </p>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="cancelDelete" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">Annuller</button>
                    <button type="button" wire:click="deleteKonsulent" class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer">Slet konsulent</button>
                </div>
            </div>
        </div>
    @endif
</div>