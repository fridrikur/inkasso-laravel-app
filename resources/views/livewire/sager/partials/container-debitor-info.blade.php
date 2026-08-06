<div class="space-y-6">

    <h2 class="text-xl font-semibold text-gray-700 border-b pb-2 mb-4">Debitor</h2>

    <div class="grid grid-cols-4 gap-4">
        <div class="col-span-3 grid grid-cols-3 gap-4">
            @php
                $debitorFields = [
                    'navn'=>'Navn','email'=>'Email','co'=>'C/O','mail2'=>'Mail #2','adresse'=>'Adresse',
                    'tlf'=>'Tlf','postnr'=>'Postnr','by'=>'By','tlf2'=>'Telefon #2',
                    'pnr'=>'Cpr/Cvr','tlf3'=>'Telefon #3','adressesogning'=>'Adressesøgning'
                ];
            @endphp
            @foreach($debitorFields as $field => $label)
                <div>
                    <label class="block font-medium text-gray-700 mb-1">{{ $label }}</label>
                    <input type="text" wire:model.lazy="form.{{ $field }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('form.'.$field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach
        </div>

        {{-- Column 4: Debitor bemærkning spanning 7 rows --}}
        <div class="row-span-7">
            <label class="block font-medium text-gray-700 mb-1">Debitor bemærkning</label>
            <textarea wire:model.lazy="form.debitor_bemaerkning"
                      class="w-full h-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('form.debitor_bemaerkning') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
