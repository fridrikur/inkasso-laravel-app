<div>
    <h1>{{ $kreditornavn }}: {{ $message }}</h1>
    <form wire:submit="save">
        <div class="flex flex-row gap-4">
            <div>
                <div><label for="navn">Navn</label></div>
                <input type="text" wire:model.blur="form.navn" placeholder="navn">
                @error('form.navn') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <div><label for="email">E-mail</label></div>
                <input type="email" wire:model.blur="form.email" placeholder="email">
                @error('form.email') <span class="error">{{ $message }}</span> @enderror
            </div>            
        </div>
        <div class="flex flex-row gap-4">
            <div>
                <div><label for="tlf">Telefon</label></div>
                <input type="tel" wire:model.blur="form.tlf" placeholder="tlf">
                @error('form.tlf') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <div><label for="mobil">Mobil</label></div>
                <input type="tel" wire:model.blur="form.mobil" placeholder="mobil">
                @error('form.mobil') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <div class="pt-2 ml-1">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="hsb" class="sr-only peer" wire:model.defer="hsb">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Hovedsagsbehandler</span>
                    </label>
                    @error('form.hsb') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Save</button>
        </div>
    </form>
</div>