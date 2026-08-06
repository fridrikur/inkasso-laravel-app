{{-- Table --}}
    <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Navn</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Handling</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($konsulenter as $konsulent)
                    @php
                        $isHS = $konsulent->hovedkonsulent->isNotEmpty();
                        $statuses = collect([
                            $isHS ? 'HS' : null,
                            $konsulent->skjultkonsulent->isNotEmpty() ? 'SK' : null,
                            $konsulent->notifikationskonsulent->isNotEmpty() ? 'NK' : null,
                        ])->filter()->values()->toArray();
                    @endphp

                    <tr 
                        x-show="isVisible({{ json_encode($statuses) }})"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="border-b transition {{ $isHS ? 'bg-green-50 border-l-4 border-green-500 animate-pulse' : 'hover:bg-gray-50' }}"
                    >
                        <td class="px-4 py-2 font-medium {{ $isHS ? 'text-green-700' : '' }}">
                                        {{ $konsulent->navn }}
                        </td>
                        <td class="px-4 py-2 space-x-1">
                            @if ($isHS)
                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs font-bold">HS</span>
                            @endif

                            @foreach ($konsulent->skjultkonsulent as $sk)
                                <span class="px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 text-xs font-medium">SK</span>
                            @endforeach

                            @foreach ($konsulent->notifikationskonsulent as $nk)
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">NK</span>
                            @endforeach
                        </td>
                        <td><x-global-modal title="Redigér konsulent" size="lg">
                                <x-slot name="trigger">
                                    <button class="text-blue-600 hover:underline font-medium" @click="modalIsOpen = true">
                                        Redigér
                                    </button>
                                </x-slot>

                                <liveWire:konsulenter.update-konsulent :konsulent="$konsulent" />

                                <x-slot name="footer">
                                    <button @click="modalIsOpen = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                        Luk
                                    </button>
                                </x-slot>
                            </x-global-modal>
                            <a href="#" wire:click.prevent="delete({{ $konsulent->id }})" class="text-red-600 hover:text-red-900 ml-4">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    