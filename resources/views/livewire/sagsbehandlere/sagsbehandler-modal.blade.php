<div>
    @if($show)
    <div 
        x-data="{ show: false }"
        x-init="setTimeout(() => show = true, 5)"
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
        <!-- Backdrop -->
        <div 
            class="fixed inset-0 bg-black/30 backdrop-blur-md transition-opacity duration-300"
            x-show="show"
            x-transition.opacity
            wire:click="close"
        ></div>

        <!-- Modal Wrapper -->
        <div class="relative w-full max-w-xl mx-4" x-show="show" x-transition.scale.origin.top.duration.250ms>
            <div class="bg-white rounded-xl shadow-2xl p-6 relative">

                <!-- Close Button -->
                <button wire:click="close" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>

                <!-- Title -->
                <h2 class="text-2xl font-semibold mb-4">
                    {{ $sags ? 'Rediger Sagsbehandler' : 'Opret ny Sagsbehandler' }}
                </h2>

                <!-- Form -->
                <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Navn</label>
                        <input type="text" wire:model.defer="modalNavn" class="w-full border rounded-lg px-3 py-2">
                        @error('modalNavn')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model.defer="modalEmail" class="w-full border rounded-lg px-3 py-2">
                        @error('modalEmail')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Tlf</label>
                        <input type="text" wire:model.defer="modalTlf" class="w-full border rounded-lg px-3 py-2">
                        @error('modalTlf')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Mobil</label>
                        <input type="text" wire:model.defer="modalMobil" class="w-full border rounded-lg px-3 py-2">
                        @error('modalMobil')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-2 pt-2">
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" wire:model.defer="modalIsHoved" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-700 text-sm">Sæt som Hovedsagsbehandler</span>
                        </label>
                    </div>

                    <div class="col-span-2 flex justify-end gap-3 mt-4">
                        <button type="button" wire:click="close" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">Annuller</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">Gem</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    @endif
</div>
