<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Sagsbehandlere for <span class="text-indigo-600">{{ $kreditor->navn }}</span></h1>
            <p class="text-sm text-gray-500 mt-1">{{ $sagsbehandlere->total() }} sagsbehandler{{ $sagsbehandlere->total() === 1 ? '' : 'e' }}</p>
        </div>

        <div class="flex items-center space-x-3">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Søg efter navn eller email..."
                class="border rounded-md px-3 py-2 w-72 focus:ring-indigo-500 focus:border-indigo-500"
            />

            <select wire:model.live="perPage" class="border rounded-md px-3 py-2">
                @foreach([5,10,25,50] as $size)
                    <option value="{{ $size }}">{{ $size }}</option>
                @endforeach
            </select>

            <button wire:click="openModal()" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Opret
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-100 rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm text-gray-600">
                    <th class="px-6 py-3">Navn</th>
                    <th class="px-6 py-3">Email</th>
                    <th class="px-6 py-3">Tlf</th>
                    <th class="px-6 py-3">Mobil</th>
                    <th class="px-6 py-3 text-right">Handling</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($sagsbehandlere as $sagsbehandler)
                    @php
                        // determines whether this sagsbehandler is a hovedsagsbehandler for the current kreditor
                        $isHoved = $kreditor->hovedsagsbehandler->contains('id', $sagsbehandler->id);
                    @endphp

                    <tr class="hover:bg-gray-50 transition-colors {{ $isHoved ? 'bg-indigo-50' : '' }}">
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-800">{{ $sagsbehandler->navn }}</span>

                                        @if($isHoved)
                                            <span class="inline-flex items-center text-xs font-semibold bg-indigo-600 text-white rounded-full px-2 py-0.5">
                                                HOVED
                                            </span>
                                        @endif
                                    </div>

                                    @if($sagsbehandler->email)
                                        <span class="text-xs text-gray-500">{{ $sagsbehandler->email }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 align-middle sm:table-cell">
                            <div class="text-sm text-gray-700">{{ $sagsbehandler->email }}</div>
                        </td>

                        <td class="px-6 py-4 align-middle">
                            <div class="text-sm text-gray-700">{{ $sagsbehandler->tlf ?? '—' }}</div>
                        </td>

                        <td class="px-6 py-4 align-middle">
                            <div class="text-sm text-gray-700">{{ $sagsbehandler->mobil ?? '—' }}</div>
                        </td>

                        <td class="px-6 py-4 text-right align-middle space-x-2">
                            <button wire:click="openModal({{ $sagsbehandler->id }})"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm">
                                Rediger
                            </button>

                            @if(! $isHoved)
                                <button wire:click="setHoved({{ $sagsbehandler->id }})"
                                        class="text-sm px-2 py-1 border rounded text-gray-600 hover:bg-gray-100">
                                    Sæt som hoved
                                </button>
                            @else
                                <button wire:click="unsetHoved({{ $sagsbehandler->id }})"
                                        class="text-sm px-2 py-1 border rounded text-red-600 hover:bg-red-50">
                                    Fjern hoved
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Ingen sagsbehandlere fundet. Prøv at ændre søgningen eller opret en ny.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-3 border-t bg-gray-50 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Visning {{ $sagsbehandlere->firstItem() ?: 0 }} – {{ $sagsbehandlere->lastItem() ?: 0 }} af {{ $sagsbehandlere->total() }}
            </div>

            <div>
                {{ $sagsbehandlere->links() }}
            </div>
        </div>
    </div>

    {{-- modal partial --}}
    @include('livewire.sagsbehandlere._form_modal')
</div>
