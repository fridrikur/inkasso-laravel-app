{{-- resources/views/livewire/sager/partials/table.blade.php --}}

<div class="overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        
        {{-- HEADER --}}
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th wire:click="sortBy('sagers.id')" class="px-6 py-3 cursor-pointer">ID</th>
                <th wire:click="sortBy('sagers.sagsnr')" class="px-6 py-3 cursor-pointer">Sagsnr</th>
                <th wire:click="sortBy('debitor_navn')" class="px-6 py-3 cursor-pointer">Debitor</th>
                <th wire:click="sortBy('kreditor_navn')" class="px-6 py-3 cursor-pointer">Kreditor</th>
                <th class="px-6 py-3">Handling</th>
            </tr>
        </thead>

        {{-- BODY --}}
        <tbody>
            @forelse($sagers as $sag)
                <tr wire:key="sag-{{ $sag->id }}" class="border-b hover:bg-gray-50">
                    
                    <td class="px-6 py-4">{{ $sag->id }}</td>
                    <td class="px-6 py-4">{{ $sag->sagsnr }}</td>
                    <td class="px-6 py-4">{{ $sag->debitor_navn }}</td>
                    <td class="px-6 py-4">{{ $sag->kreditor_navn }}</td>

                    <td class="px-6 py-4 flex space-x-2">
                        
                        {{-- EDIT --}}
                        <a href="{{ route('sager.edit', $sag) }}"
                           class="text-green-600 hover:text-green-900">
                            Redigér
                        </a>

                        {{-- DELETE --}}
                        <button wire:click="deleteSag({{ $sag->id }})"
                                class="text-red-600 hover:text-red-900">
                            Slet
                        </button>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-6 text-gray-400">
                        Ingen sager fundet
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- PAGINATION --}}
    <div class="p-4">
        {{ $sagers->links() }}
    </div>
</div>