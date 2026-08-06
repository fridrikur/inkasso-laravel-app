<div class="p-8 space-y-8 bg-white rounded-xl shadow-sm">
    <!-- Section: Basic Case Info -->
    <div>
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Sagsinformation</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach(['sagsnr' => 'Sagsnr', 'stelnr' => 'Stelnr', 'afdragsordning' => 'Afdragsordning', 'fakturanr' => 'Fakturanr'] as $field => $label)
                <div>
                    <label class="block font-medium text-gray-600 mb-1">{{ $label }}</label>
                    <input type="text" wire:model.defer="form.{{ $field }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('form.'.$field) <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section: Dates -->
    <div>
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Datoer</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach(['afsluttet','faktureret','betalt','fakturadato','modtaget','senesterapport','opgivet'] as $date)
                <div>
                    <label class="block font-medium text-gray-600 mb-1">{{ ucfirst($date) }}</label>
                    <input type="date" wire:model.defer="form.{{ $date }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('form.'.$date) <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            @endforeach
        </div>
    </div>

  <!-- Section: Amounts -->
<div>
    <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Beløb</h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
   @foreach(['hovedstol','renter','gebyr','ialt','startgebyr','restgaeld','restgaeld_dkg','indbetalt','mdlydelse','n_mdlydelse'] as $field)
    <div>
        <label class="block font-medium text-gray-600 mb-1">{{ ucfirst($field) }}</label>
        <input
            type="text"
            wire:model.lazy="form.{{ $field }}"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
        >
        @error('form.'.$field)
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>
@endforeach



    </div>
</div>


    <!-- Section: Status and Checkboxes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block font-medium text-gray-600 mb-1">Status</label>
            <select wire:model="form.status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Vælg status</option>
                @foreach($selectOptions['status'] ?? [] as $id => $text)
                    <option value="{{ $id }}">{{ $text }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center mt-6 space-x-2">
            <input type="checkbox" wire:model.defer="form.aktiv" id="aktiv" class="text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="aktiv" class="text-gray-700 font-medium">Aktiv</label>
        </div>
    </div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Kreditor --}}
    <div class="flex flex-col">
        <label for="kreditor" class="font-medium text-gray-700 mb-2">Kreditor</label>
        <select
            wire:model="form.kreditor"
            wire:change="loadSagsbehandlere($event.target.value)"
            id="kreditor"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
        >
            <option value="">Vælg kreditor</option>
            @foreach ($selectOptions['kreditor'] ?? [] as $id => $text)
                <option value="{{ $id }}">{{ $text }}</option>
            @endforeach
        </select>
    </div>

    {{-- Sagsbehandler --}}
    <div class="flex flex-col">
        <label for="sagsbehandler" class="font-medium text-gray-700 mb-2">Sagsbehandler</label>
        <select
            wire:model="form.sagsbehandler"
            id="sagsbehandler"
            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
        >
            <option value="">Vælg sagsbehandler</option>
            @foreach ($selectOptions['sagsbehandler'] ?? [] as $id => $text)
                <option value="{{ $id }}">{{ $text }}</option>
            @endforeach
        </select>

        {{-- Loading indicator --}}
        <span wire:loading wire:target="loadSagsbehandlere" class="text-sm text-gray-500 mt-1">
            Indlæser sagsbehandlere...
        </span>
    </div>
</div>

    <!-- Section: Relations -->
    <div>
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">Relationer</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach(['konsulent' => 'Konsulent', 'ktr' => 'KTR', 'afslutning' => 'Afslutning', 'bemaerkning' => 'Bemærkning'] as $field => $label)
                <div>
                    <label class="block font-medium text-gray-600 mb-1">{{ $label }}</label>
                    <select
                        wire:model="form.{{ $field }}"
                        id="{{ $field }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                        @php
                            $value = data_get($form, $field); // safely get dynamic property from form object
                        @endphp

                        @if (empty($value))
                            <option value="">Vælg {{ strtolower($label) }}</option>
                        @endif
                        <option value="">Vælg {{ strtolower($label) }}</option>
                        @foreach ($selectOptions[$field] ?? [] as $id => $text)
                            <option value="{{ $id }}">{{ $text }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section: Debitor Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($debitorFields as $field => $props)
                <div>
                    <label for="{{ $field }}" class="block font-medium text-gray-600 mb-1">{{ $props['label'] }}</label>

                    <input
                        id="{{ $field }}"
                        type="{{ $props['type'] }}"
                        wire:model.lazy="form.{{ $field }}"
                        maxlength="{{ $props['maxlength'] }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        @if(!empty($props['numeric']))
                            pattern="[0-9]*" inputmode="numeric"
                        @endif
                    >

                    @error("form.$field")
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
            </div>
    </div>
