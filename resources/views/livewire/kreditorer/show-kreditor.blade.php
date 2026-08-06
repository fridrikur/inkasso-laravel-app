<div class="max-w-7xl mx-auto px-6 py-8">

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- ===================================================== --}}
        {{-- SIDEBAR --}}
        {{-- ===================================================== --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kreditor card --}}
            <div class="bg-white rounded-xl shadow p-6 sticky top-6">

                <div class="flex items-start justify-between gap-2">

    <h1 class="text-2xl font-bold text-gray-900">
        {{ $kreditor->navn }}
    </h1>

    <button
        wire:click="$dispatch('open-kreditor-modal', { kreditorId: {{ $kreditor->id }} })"
        class="text-gray-500 hover:text-gray-700 mt-1"
        title="Redigér kreditor"
    >
        <x-heroicon-o-pencil class="h-5 w-5"/>
    </button>

</div>

                <div class="mt-4 space-y-3 text-sm">

                    <div class="flex justify-between">
                        <span>Sager</span>
                        <span class="font-semibold">
                            {{ $kreditor->sagerkreditor_count }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span>Brugere</span>
                        <span class="font-semibold">
                            {{ $kreditor->users->count() }}
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span>Sagsbehandlere</span>
                        <span class="font-semibold">
                            {{ $kreditor->sagsbehandlere->count() }}
                        </span>
                    </div>

                </div>

                <hr class="my-5">

                <div class="space-y-2">

                    {{-- <button
                        wire:click="saveKreditor"
                        class="w-full px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">

                        Gem kreditor
                    </button> --}}

                    <button
                        wire:click="openUserModal"
                        class="w-full px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">

                        Ny bruger
                    </button>

                    <button
                        wire:click="openSagsbehandlerModal"
                        class="w-full px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">

                        Ny sagsbehandler
                    </button>

                </div>

            </div>

        </div>

        {{-- ===================================================== --}}
        {{-- CONTENT --}}
        {{-- ===================================================== --}}
        <div class="lg:col-span-3 space-y-8">

            {{-- ===================================================== --}}
            {{-- KREDITOR INFO --}}
            {{-- ===================================================== --}}
            <section id="info" class="bg-white rounded-xl shadow p-6">

                <h2 class="text-xl font-semibold mb-6">
        Kreditor oplysninger
    </h2>

    <div class="grid md:grid-cols-2 gap-6 text-sm">

        <div>
            <p class="text-gray-500">Navn</p>
            <p class="font-medium">{{ $kreditor->navn }}</p>
        </div>

        <div>
            <p class="text-gray-500">Sager</p>
            <p class="font-medium">{{ $kreditor->sagerkreditor_count }}</p>
        </div>

        <div>
            <p class="text-gray-500">Brugere</p>
            <p class="font-medium">{{ $kreditor->users->count() }}</p>
        </div>

        <div>
            <p class="text-gray-500">Sagsbehandlere</p>
            <p class="font-medium">{{ $kreditor->sagsbehandlere->count() }}</p>
        </div>

        @livewire('kreditor.kreditor-form-modal')
            </section>

            {{-- ===================================================== --}}
            {{-- USERS --}}
            {{-- ===================================================== --}}
            <section
                id="users"
                class="bg-white rounded-xl shadow">

                <div class="flex justify-between items-center p-6 border-b">

                    <h2 class="text-xl font-semibold">
                        Brugere
                    </h2>

                    <button
                        wire:click="openUserModal"
                        class="px-4 py-2 rounded-lg bg-green-600 text-white">

                        Opret bruger
                    </button>

                </div>

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr class="text-left">

                            <th class="px-4 py-3">Navn</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3 w-48">Handlinger</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($kreditor->users as $user)

                            <tr
                                wire:key="user-{{ $user->id }}"
                                class="odd:bg-white even:bg-gray-50 border-t">

                                <td class="px-4 py-3">
                                    {{ $user->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $user->email }}
                                </td>

                                <td class="px-4 py-3">

                                    <div class="flex gap-2">

                                        <button
                                            wire:click="openUserModal({{ $user->id }})"
                                            class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded">

                                            Rediger
                                        </button>

                                        <button
                                            wire:click="detachUser({{ $user->id }})"
                                            wire:confirm="Fjern bruger fra kreditor?"
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded">

                                            Fjern
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="p-6 text-center text-gray-500">

                                    Ingen brugere

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </section>

            {{-- ===================================================== --}}
            {{-- SAGSBEHANDLERE --}}
            {{-- ===================================================== --}}
            <section
                id="sagsbehandlere"
                class="bg-white rounded-xl shadow">

                <div class="flex justify-between items-center p-6 border-b">

                    <h2 class="text-xl font-semibold">
                        Sagsbehandlere
                    </h2>

                    <button
                        wire:click="openSagsbehandlerModal"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white">

                        Opret sagsbehandler
                    </button>

                </div>

                <table class="w-full">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-4 py-3 text-left">Navn</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Telefon</th>
                            <th class="px-4 py-3 text-left">Mobil</th>
                            <th class="px-4 py-3 text-left">Handlinger</th>

                        </tr>

                    </thead>

                    <tbody>

                        @foreach($kreditor->sagsbehandlere as $sags)

                            <tr
                                wire:key="sags-{{ $sags->id }}"
                                class="odd:bg-white even:bg-gray-50 border-t">

                                <td class="px-4 py-3">
                                    {{ $sags->navn }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $sags->email }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $sags->tlf }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $sags->mobil }}
                                </td>

                                <td class="px-4 py-3">

                                    <div class="flex gap-2">

                                        <button
                                            wire:click="openSagsbehandlerModal({{ $sags->id }})"
                                            class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded">

                                            Rediger
                                        </button>

                                        <button
                                            wire:click="detachSagsbehandler({{ $sags->id }})"
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded">

                                            Fjern
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </section>

            {{-- ===================================================== --}}
            {{-- SAGER --}}
            {{-- ===================================================== --}}
            <section
                id="sager"
                class="bg-white rounded-xl shadow">

                <div class="p-6 border-b">

                    <h2 class="text-xl font-semibold">
                        Sager
                    </h2>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Debitor</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-left">Handlinger</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($kreditor->sager as $sag)

                                <tr class="odd:bg-white even:bg-gray-50 border-t">

                                    <td class="px-4 py-3">
                                        {{ $sag->id }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $sag->debitor?->navn }}
                                    </td>

                                    <td class="px-4 py-3">
                                        {{ $sag->status ?? 'Aktiv' }}
                                    </td>

                                    <td class="px-4 py-3">

                                        <a
                                            href="{{ route('sager.edit', $sag) }}"
                                            class="text-indigo-600 hover:underline">

                                            Åbn sag

                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </section>

        </div>

    </div>


<div class="space-y-6">
    
    <div x-data="{ navn: '{{ $kreditornavn }}' }"
        x-on:kreditor-saved.window="
            console.log('kreditor-saved payload:', $event);
            navn = $event.detail[0].navn;
        "
    >
        
        @livewire('kreditor.kreditor-form-modal')
    </div>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- USERS --}}
        <div class="bg-white rounded-lg p-6 shadow space-y-3">
            <strong>Brugere</strong>
            <hr>

            @forelse ($kreditor->users as $user)
                <div class="flex justify-between">
                    <span>{{ $user->name }}</span>
                    <button wire:click="openUserModal({{ $user->id }})"
                        class="text-sm text-indigo-600 hover:underline">
                        Rediger
                    </button>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ingen brugere</p>
            @endforelse

            <button wire:click="openUserModal"
                class="text-sm text-indigo-600 hover:underline">
                + Opret bruger
            </button>
        </div>

        {{-- SAGSBHANDLERE --}}
        <div class="bg-white rounded-lg p-6 shadow space-y-3">
            <strong>Sagsbehandlere</strong>
            <hr>

            @foreach ($kreditor->sagsbehandlere as $sb)
                <div class="flex justify-between">
                    <span>{{ $sb->navn }}</span>
                    <button wire:click="openSagsbehandlerModal({{ $sb->id }})"
                        class="text-sm text-indigo-600 hover:underline">
                        Rediger
                    </button>
                </div>
            @endforeach

            <button wire:click="openSagsbehandlerModal"
                class="text-sm text-indigo-600 hover:underline">
                + Opret sagsbehandler
            </button>
        </div>

        {{-- ACTIONS --}}
        <div class="bg-white rounded-lg p-6 shadow">
            <strong>Handlinger</strong>
            <hr class="my-2">

            <a href="{{ route('kreditorer.sager', $kreditor) }}"
               class="text-sm text-indigo-600 hover:underline">
                Vis samtlige sager ({{ $kreditor->sager_count }})
            </a><br>
            
            <a href="{{route('sager.import.form', $kreditor)}}" class="text-sm text-indigo-600 hover:underline">
                Import
            </a>
            
        </div>
        
    </div>

    {{-- USER MODAL --}}
    @if($showUserModal)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-lg">

            <h2 class="text-lg font-semibold mb-4">
                {{ $activeUser ? 'Rediger bruger' : 'Opret bruger' }}
            </h2>

            <form wire:submit.prevent="saveUser" class="space-y-4">

                {{-- Navn --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Navn</label>
                    <input
                        wire:model.defer="userName"
                        class="w-full border rounded px-3 py-2 @error('userName') border-red-500 @enderror"
                    >
                    @error('userName')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input
                        wire:model.defer="userEmail"
                        class="w-full border rounded px-3 py-2 @error('userEmail') border-red-500 @enderror"
                    >
                    @error('userEmail')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Password</label>
                    <input
                        type="password"
                        wire:model.defer="userPassword"
                        class="w-full border rounded px-3 py-2 @error('userPassword') border-red-500 @enderror"
                    >
                    @error('userPassword')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="px-4 py-2 bg-gray-200 rounded"
                    >
                        Annuller
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded"
                    >
                        Gem
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif



    {{-- SAGSBHANDLER MODAL --}}
    @if($showSagsModal)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-xl">

            <h2 class="text-lg font-semibold mb-4">
                {{ $activeSagsbehandler ? 'Rediger sagsbehandler' : 'Opret sagsbehandler' }}
            </h2>

            <form wire:submit.prevent="saveSagsbehandler" class="grid grid-cols-2 gap-4">

                {{-- Navn --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Navn</label>
                    <input
                        wire:model.defer="modalNavn"
                        class="w-full border rounded px-3 py-2 @error('modalNavn') border-red-500 @enderror"
                    >
                    @error('modalNavn')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input
                        wire:model.defer="modalEmail"
                        class="w-full border rounded px-3 py-2 @error('modalEmail') border-red-500 @enderror"
                    >
                    @error('modalEmail')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Telefon --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Telefon</label>
                    <input
                        wire:model.defer="modalTlf"
                        class="w-full border rounded px-3 py-2"
                    >
                    @error('modalTlf')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mobil --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Mobil</label>
                    <input
                        wire:model.defer="modalMobil"
                        class="w-full border rounded px-3 py-2"
                    >
                    @error('modalMobil')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2 flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="closeModal"
                        class="px-4 py-2 bg-gray-200 rounded"
                    >
                        Annuller
                    </button>

                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded"
                    >
                        Gem
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif
</div>
