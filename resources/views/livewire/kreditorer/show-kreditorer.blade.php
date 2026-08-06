<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <h1 class="text-3xl font-bold text-gray-900">
                Kreditorer
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Oversigt over alle kreditorer i systemet
            </p>
        </div>

        <button
            wire:click="opretnykreditor"
            class="inline-flex items-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5 mr-2"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4v16m8-8H4" />
            </svg>

            Ny kreditor
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <div class="text-sm text-gray-500">
                Kreditorer
            </div>

            <div class="text-3xl font-bold text-gray-900 mt-1">
                {{ $kreditorer->count() }}
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="font-semibold text-gray-800">
                Kreditorliste
            </h2>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>
                    <tr class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">

                        <th class="px-6 py-4">
                            Navn
                        </th>

                        <th class="px-6 py-4 w-48">
                            Oprettet
                        </th>

                        <th class="px-6 py-4 w-32 text-right">
                            Handlinger
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse($kreditorer as $kreditor)

                        <tr
                            wire:key="kreditor-{{ $kreditor->id }}"
                            class="border-t hover:bg-indigo-50/50 transition group">

                            <td class="px-6 py-4">

                                <div class="font-medium text-gray-900">
                                    {{ $kreditor->navn }}
                                </div>

                                @if($kreditor->sager_count)
                                    <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs">
                                        {{ $kreditor->sager_count }} aktive sager
                                    </div>
                                @else
                                    <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs">
                                        Ingen sager
                                    </div>
                                @endif

                                <div class="text-sm text-gray-500">
                                    #{{ $kreditor->id }}
                                </div>

                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $kreditor->created_at?->format('d-m-Y') }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition">

                                <a href="{{ route('showkreditor', ['kreditor' => $kreditor]) }}"
                                class="px-3 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">
                                    Rediger
                                </a>

                                <button
                                    wire:click="requestDelete({{ $kreditor->id }})"
                                    @class([
                                        'px-3 py-2 text-sm rounded-lg transition',
                                        'bg-red-50 text-red-600 hover:bg-red-100' => $kreditor->sager_count == 0,
                                        'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-300' => $kreditor->sager_count > 0,
                                    ])
                                >
                                    @if($kreditor->sager_count)
                                        ⚠ {{ $kreditor->sager_count }} (sager)
                                    @else
                                        Slet
                                    @endif
                                </button>

                            </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="3" class="py-16 text-center">

                                <div class="text-gray-400 text-lg">
                                    Ingen kreditorer fundet
                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@livewire('kreditor.kreditor-form-modal')

@if($showDeleteModal)

<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">

        <h2 class="text-xl font-bold text-red-600 mb-4">
            Slet kreditor
        </h2>

        <div class="mb-4 font-medium">
            {{ $kreditorToDelete?->navn }}
        </div>

        {{-- ========================= --}}
        {{-- BLOCKED STATE --}}
        {{-- ========================= --}}
        @if(($kreditorToDelete?->sager_count ?? 0) > 0 && !$showTransferMode)

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">

                <div class="font-medium text-yellow-800">
                    Sletning er blokeret
                </div>

                <div class="text-sm text-yellow-700 mt-1">
                    Kreditoren har {{ $kreditorToDelete->sager_count }} aktive sager.
                </div>

                <button
                    wire:click="enableTransferMode"
                    class="mt-3 px-4 py-2 bg-yellow-600 text-white rounded-xl"
                >
                    Overfør sager
                </button>

            </div>

        @endif

        {{-- ========================= --}}
        {{-- TRANSFER MODE --}}
        {{-- ========================= --}}
        @if($showTransferMode)

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 space-y-3">

                <div class="font-medium text-blue-800">
                    Overfør sager
                </div>

                <select
                    wire:model="transferToKreditorId"
                    class="w-full border rounded-xl px-3 py-2"
                >
                    <option value="">Vælg kreditor</option>

                    @foreach($transferTargets as $k)
                        <option value="{{ $k->id }}">
                            {{ $k->navn }}
                        </option>
                    @endforeach
                </select>

                <button
                    wire:click="transferSager"
                    class="px-4 py-2 bg-blue-600 text-white rounded-xl"
                >
                    Bekræft overførsel
                </button>

            </div>

        @endif

        {{-- ========================= --}}
        {{-- DELETE CONFIRMATION --}}
        {{-- ========================= --}}
        @if(($kreditorToDelete?->sager_count ?? 0) === 0)

            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mt-4">

                <div class="text-sm text-red-600">
                    Indtast global sikkerhedskode
                </div>

                <input
                    type="password"
                    wire:model.defer="securityCode"
                    class="w-full border rounded-xl px-4 py-3 mt-2"
                >

                @error('securityCode')
                    <div class="text-red-500 text-sm mt-2">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        @endif

        {{-- ========================= --}}
        {{-- ACTIONS --}}
        {{-- ========================= --}}
        <div class="flex justify-end gap-3 mt-6">

            <button
                wire:click="cancelDelete"
                class="px-4 py-2 bg-gray-200 rounded-xl"
            >
                Annuller
            </button>

            @if(($kreditorToDelete?->sager_count ?? 0) === 0)

                <button
                    wire:click="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-xl"
                >
                    Slet permanent
                </button>

            @endif

        </div>

    </div>

</div>

@endif

