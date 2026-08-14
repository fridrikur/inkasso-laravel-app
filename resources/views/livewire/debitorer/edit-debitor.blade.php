<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    
    <!-- TOAST NOTIFIKATION (Alpine.js) -->
    <div 
        x-data="{ show: false, message: '' }"
        @notify.window="message = $event.detail.message; show = true; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        style="display: none;"
        class="fixed top-5 right-5 z-50 flex items-center bg-emerald-600 text-white px-5 py-3 rounded-lg shadow-xl border border-emerald-500 space-x-3"
    >
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-medium text-sm" x-text="message"></span>
    </div>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-200">
        
        <!-- Header -->
        <div class="bg-slate-800 px-6 py-4 flex justify-between items-center text-white">
            <h2 class="text-xl font-semibold">Rediger Debitor: {{ $form->navn ?? '' }}</h2>
            <a href="{{ route('debitorer.index') }}" class="px-3 py-1 bg-slate-700 hover:bg-slate-600 rounded text-sm transition">
                Tilbage til oversigt
            </a>
        </div>

        <!-- Formular -->
        <form wire:submit.prevent="save" class="p-6 space-y-6">

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                
                <!-- Navn -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Navn</label>
                    <input type="text" wire:model.defer="form.navn" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.navn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- C/O -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">C/O</label>
                    <input type="text" wire:model.defer="form.co" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.co') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Adresse -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" wire:model.defer="form.adresse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.adresse') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Postnr -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Postnummer</label>
                    <input type="text" wire:model.defer="form.postnr" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.postnr') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- CPR / PNR -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">CPR / PNR</label>
                    <input type="text" wire:model.defer="form.pnr" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.pnr') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" wire:model.defer="form.email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Tlf -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefon</label>
                    <input type="text" wire:model.defer="form.tlf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.tlf') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Mobil -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mobil</label>
                    <input type="text" wire:model.defer="form.mobil" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.mobil') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Adropl -->
                <!-- Adropl (Datofelt) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adropl (Dato)</label>
                    <input type="date" wire:model.defer="form.adropl" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2">
                    @error('form.adropl') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Kontakt bemærkning -->
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Kontakt bemærkning</label>
                    <textarea wire:model.defer="form.kontakt_bemaerkning" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2"></textarea>
                    @error('form.kontakt_bemaerkning') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

            </div>

            <!-- Handlingsknapper -->
            <div class="flex justify-end space-x-4 border-t pt-4">
                <a href="{{ route('debitorer.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition">
                    Annuller
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    Gem ændringer
                </button>
            </div>

        </form>

    </div>
</div>