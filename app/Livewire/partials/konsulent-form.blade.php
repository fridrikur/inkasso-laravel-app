<div x-data="{ 
        hsb: @entangle('form.hsb'), 
        nsb: @entangle('form.nsb')
    }"
>
    {{-- Text Fields --}}
    @foreach (['navn' => 'Navn', 'tlf' => 'Telefon', 'email' => 'Email', 'mobil' => 'Mobil'] as $field => $label)
        <div class="mb-4">
            <label for="{{ $field }}" class="block text-sm font-medium">{{ $label }}</label>
            <input type="text" id="{{ $field }}" wire:model.defer="form.{{ $field }}" 
                   class="border rounded p-2 w-full">
            @error("form.$field") 
                <span class="text-red-600 text-sm">{{ $message }}</span> 
            @enderror
        </div>
    @endforeach

    {{-- Toggles --}}
    <div class="space-y-4">
        {{-- HSB --}}
        <label class="flex items-center space-x-2">
            <input type="checkbox" wire:model="form.hsb" value="true" 
                   class="form-checkbox h-5 w-5" x-model="hsb">
            <span>Hovedkonsulent (HSB)</span>
        </label>

        {{-- SSB --}}
        <label class="flex items-center space-x-2" x-show="hsb !== 'true'">
            <input type="checkbox" wire:model="form.ssb" value="true" 
                   class="form-checkbox h-5 w-5">
            <span>Skjult Konsulent (SSB)</span>
        </label>

        {{-- NSB --}}
        <label class="flex items-center space-x-2"
               :class="{'opacity-50 cursor-not-allowed': hsb === 'true'}">
            <input type="checkbox" wire:model="form.nsb" value="true" 
                   class="form-checkbox h-5 w-5" x-model="nsb" 
                   :disabled="hsb === 'true'">
            <span>Notifikations Konsulent (NSB)</span>
        </label>
    </div>

    {{-- Save Button --}}
    <div class="mt-6">
        <button wire:click="save"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Gem
        </button>
    </div>
</div>