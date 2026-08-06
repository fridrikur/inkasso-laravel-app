<div class="space-y-6">

    {{-- STATISTICS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total konsulenter</p>
            <p class="mt-2 text-3xl font-bold">{{ $konsulenter->total() }}</p>
        </div>

        <div class="rounded-2xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Hovedkonsulent</p>
            <p class="mt-2 text-xl font-bold text-indigo-600">
                {{ \App\Models\HovedKonsulent::current()?->navn ?? 'Ingen valgt' }}
            </p>
        </div>

        <div class="rounded-2xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Notifikation</p>
            <p class="mt-2 text-3xl font-bold">{{ \App\Models\NotifikationsKonsulent::count() }}</p>
        </div>

        <div class="rounded-2xl border bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">Skjulte</p>
            <p class="mt-2 text-3xl font-bold">{{ \App\Models\SkjultKonsulent::count() }}</p>
        </div>
    </div>

    {{-- LEGEND --}}
    <div class="rounded-2xl border bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Konsulentroller</h2>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl bg-indigo-50 p-4">
                <div class="font-semibold text-indigo-700">⭐ Hovedkonsulent</div>
                <p class="mt-1 text-sm text-slate-600">
                    Den primære ansvarlige konsulent. Der kan kun eksistere én.
                </p>
            </div>

            <div class="rounded-xl bg-emerald-50 p-4">
                <div class="font-semibold text-emerald-700">🔔 Notifikationskonsulent</div>
                <p class="mt-1 text-sm text-slate-600">
                    Modtager systemnotifikationer. Flere kan vælges.
                </p>
            </div>

            <div class="rounded-xl bg-slate-100 p-4">
                <div class="font-semibold text-slate-700">🙈 Skjult konsulent</div>
                <p class="mt-1 text-sm text-slate-600">
                    Vises ikke i normale valg. Flere kan vælges.
                </p>
            </div>
        </div>
    </div>

    {{-- 🟢 DATA TABLE (ERSTATTER HEADER, SØGNING, FANER OG TABEL-KORT) --}}
    <x-data-table 
        title="Konsulenter" 
        description="Administrer konsulenter, roller og ansvar."
        :headers="['Navn', 'Email', 'Roller']"
        :items="$konsulenter"
        wire:model.live="search"
    >
        {{-- TILPASSET HEADER ACTION MED ROLLE-FANER OG OPRET KNAP --}}
        <x-slot:action>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
                    @foreach([
                        'alle'   => 'Alle',
                        'hoved'  => '⭐ Hoved',
                        'notif'  => '🔔 Notifikation',
                        'skjult' => '🙈 Skjulte'
                    ] as $key => $label)
                        <button
                            type="button"
                            wire:click="setRoleTab('{{ $key }}')"
                            class="px-3 py-1.5 text-xs font-bold rounded-lg transition cursor-pointer {{ $activeRoleTab === $key ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <button 
                    type="button" 
                    wire:click="openCreateModal" 
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>Opret konsulent</span>
                </button>
            </div>
        </x-slot:action>

        {{-- TABEL RÆKKER --}}
        @forelse($konsulenter as $k)
            <tr wire:key="konsulent-{{ $k->id }}" class="hover:bg-slate-50/50 transition">
                <td class="px-6 py-4 font-semibold text-slate-900">
                    {{ $k->navn }}
                </td>

                <td class="px-6 py-4 text-slate-500">
                    {{ $k->email }}
                </td>

                <td class="px-6 py-4 space-x-1.5">
                    @foreach($k->visibleRoles() as $role)
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $role['class'] }}">
                            {{ $role['icon'] }} {{ $role['name'] }}
                        </span>
                    @endforeach
                </td>

                {{-- 🟢 BRUGER AUTOMATISK <X-TABLE-ACTIONS> --}}
                <x-table-actions :id="$k->id" />
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-6 py-10 text-center text-slate-400 text-xs">
                    Ingen konsulenter fundet.
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- 🟢 FORMULAR MODAL (OPRET / REDIGER) --}}
    @include('livewire.konsulenter.partials.modal')

    {{-- 🟢 GENBRUGELIG SLET-MODAL --}}
    <x-confirm-delete-modal 
        :show="$showDeleteModal" 
        title="Slet konsulent?" 
        message="Denne handling kan ikke fortrydes. Er du sikker på, at du vil slette denne konsulent?" 
    />

</div>