@if($showModal)
<div 
    x-data="{ show: false }"
    x-init="setTimeout(() => show = true, 5)"
    class="fixed inset-0 z-50 flex items-center justify-center"
>

    <!-- BACKDROP (real blur + fade) -->
    <div 
        class="fixed inset-0 bg-black/30 backdrop-blur-md transition-opacity duration-300"
        x-show="show"
        x-transition.opacity
        wire:click="closeModal"
    ></div>

    <!-- MODAL WRAPPER -->
    <div 
        class="relative w-full max-w-xl mx-4"
        x-show="show"
        x-transition.scale.origin.top.duration.250ms
    >

        <!-- MODAL BOX -->
        <div class="bg-white rounded-xl shadow-2xl p-6 relative">

            <!-- Close button -->
            <button 
                wire:click="closeModal"
                class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold"
            >
                ×
            </button>

            <!-- Title -->
            <h2 class="text-2xl font-semibold mb-4">
                {{ $activeSagsbehandler ? 'Rediger Sagsbehandler' : 'Opret ny Sagsbehandler' }}
            </h2>

            <!-- FORM -->
            <form wire:submit.prevent="save">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Navn -->
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Navn</label>
                        <input type="text" wire:model.live="form.navn" class="w-full border rounded-lg px-3 py-2">
                        @error('form.navn')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model.live="form.email" class="w-full border rounded-lg px-3 py-2">
                        @error('form.email')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tlf -->
                    <div>
                        <label class="text-sm font-medium text-gray-700">Tlf</label>
                        <input type="text" wire:model.live="form.tlf" class="w-full border rounded-lg px-3 py-2">
                        @error('form.tlf')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobil -->
                    <div>
                        <label class="text-sm font-medium text-gray-700">Mobil</label>
                        <input type="text" wire:model.live="form.mobil" class="w-full border rounded-lg px-3 py-2">
                        @error('form.mobil')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hovedsagsbehandler (checkbox) -->
                    <div class="col-span-2 pt-2">
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox"
                            wire:model="form.is_hoved"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-700 text-sm">
                                Sæt som Hovedsagsbehandler
                            </span>
                        </label>
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex justify-end mt-6 gap-3">
                    <button type="button" 
                            wire:click="closeModal"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                        Annuller
                    </button>

                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        Gem
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>
@endif