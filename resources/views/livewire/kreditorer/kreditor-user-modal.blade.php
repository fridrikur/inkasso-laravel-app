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
        <div class="relative w-full max-w-lg mx-4" x-show="show" x-transition.scale.origin.top.duration.250ms>
            <div class="bg-white rounded-xl shadow-2xl p-6 relative">

                <!-- Close Button -->
                <button wire:click="close" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>

                <!-- Title -->
                <h2 class="text-2xl font-semibold mb-4">
                    {{ $user ? 'Rediger Bruger' : 'Opret Bruger' }}
                </h2>

                <!-- Form -->
                <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Navn</label>
                        <input type="text" wire:model.defer="name" class="w-full border rounded-lg px-3 py-2">
                        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" wire:model.defer="email" class="w-full border rounded-lg px-3 py-2">
                        @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700">Password</label>
                        <input type="password" wire:model.defer="password" placeholder="{{ $user ? 'Lad stå for uændret' : '' }}" class="w-full border rounded-lg px-3 py-2">
                        @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
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
