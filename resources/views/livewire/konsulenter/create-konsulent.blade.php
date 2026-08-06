<div>
{{-- 
    <h1 style="font-weight: bold;font-size:20px;margin-bottom:1em">{{ $prompt }}</h1> --}}
    <x-toaster />
    <form wire:submit="save">
        <div class="flex flex-row">
            <div x-data="{ hsb: false, ssb:false, nsb:false }" class="basis-64">
                <div><label for="navn">Navn</label></div>
                <input type="text" wire:model.blur="form.navn" placeholder="navn" value="sdfsdf">
                @error('form.navn') <span class="error">{{ $message }}</span> @enderror
            <div><label for="email">E-mail</label></div>
            <input type="email" wire:model.blur="form.email" placeholder="email">
            @error('form.email') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="basis-64" >
                <div><label for="tlf">Telefon</label></div>
                <input type="tel" wire:model.blur="form.tlf" placeholder="tlf">
                @error('form.tlf') <span class="error">{{ $message }}</span> @enderror
                <div><label for="mobil">Mobil</label></div>
                <input type="tel" wire:model.blur="form.mobil" placeholder="mobil">
                @error('form.mobil') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="pt-2"><x-toggle-container label="Yderligere indstillinger">
                <div class="grid grid-cols-1 gap-40">
                        <div>
                            <label class="inline-flex items-center cursor-pointer">
                                <div>
                                {{-- NSB toggle: enable by default if HSB is also enabled --}}
                                <div><x-toggle-switch name="nsb" wire:model.defer="nsb" :checked="false"  /></div>
                                </div>
                                 <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Notifikationer</span>
                            </label>
                            @error('form.nsb') <span class="error">{{ $message }}</span> @enderror                
                        @if($hovedkonsulentvalgt !='true')
                        <label class="inline-flex items-center cursor-pointer">
                            <div><x-toggle-switch name="hsb" wire:model.defer="hsb" :checked="false"  /></div>
                        @else
                        <label class="inline-flex items-center cursor-not-allowed">
                            <button type="button" @click="on = !on" :class="on ? 'bg-green-500' : 'bg-gray-300'" class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors bg-green-500 cursor-not-allowed">
                                <span :class="on ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform translate-x-6"></span>
                            </button>
                        @endif
                        <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Hovedkonsulent</span>
                        </label>
                        @error('form.hsb') <span class="error">{{ $message }}</span> @enderror                
                    @if($hovedkonsulentvalgt !='true')
                    <label class="inline-flex items-center cursor-pointer">
                            <div><x-toggle-switch name="ssb" wire:model.defer="ssb" :checked="false"  /></div>
                            <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Skjult kons.</span>
                            </label>
                            @error('form.ssb') <span class="error">{{ $message }}</div> @enderror
                    @endif
                </div>
        </x-toggle-container></div>
            @if($errors->has('duplicate'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                    {{ $errors->first('duplicate') }}
                </div>
            @endif
            <div class="pt-2"><button type="button" wire:click="save" class="px-4 py-2 bg-blue-500 text-white rounded">
    Gem konsulent
</button></div>
        </form>
    </div>