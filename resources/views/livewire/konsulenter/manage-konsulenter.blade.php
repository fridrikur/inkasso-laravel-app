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
                <td class="px-6 py-4 text-right whitespace-nowrap">
                    <x-table-actions :id="$k->id" />
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

    {{-- GENBRUGELIG SLET-MODAL --}}
    {{-- GENBRUGELIG SLET-MODAL --}}
    <x-confirm-delete-modal 
        :show="$showDeleteModal" 
        title="Slet konsulent?" 
        message="Denne handling kan ikke fortrydes. Er du sikker på, at du vil slette denne konsulent?" 
        wire:click="confirmDelete" 
        @confirm="$wire.confirmDelete()"
        @cancel="$wire.cancelDelete()"
    />

</div>