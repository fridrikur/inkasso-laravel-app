    @props([
            'form',
            'selectOptions' => [],
            'isSearchMode' => false,
        ])
        @php
            $isSearchMode = $isSearchMode ?? false;

            $selectOptions = is_array($selectOptions ?? null) ? $selectOptions : [];
            $kreditorSuggestions = is_array($kreditorSuggestions ?? null) ? $kreditorSuggestions : [];
            $showKreditorDropdown = $showKreditorDropdown ?? false;
        @endphp{{-- SECTION 1 --}}

    {{-- ====================================================== --}}
    {{-- SECTION 1: General / Kreditor / Status (Mockup layout) --}}
    {{-- 5 rows, 4 columns, Aktiv spans col 1–2               --}}
    {{-- ====================================================== --}}
    <div class="relative space-y-4" wire:keydown.enter.prevent>
        @if($isSearchMode)
            <div wire:loading.flex
                wire:target="form"
                class="absolute inset-0 bg-white/60 backdrop-blur-sm z-50 items-center justify-center rounded-xl pointer-events-none">

                <div class="flex flex-col items-center space-y-2">
                    <svg class="animate-spin h-6 w-6 text-slate-700" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                    </svg>
                    <span class="text-sm text-slate-600">Søger...</span>
                </div>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-5">
<h2 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Generelt</h2>


<div class="grid grid-cols-3 gap-4">

            {{-- Row 1 --}}
            <div>
                <label class="block text-sm font-medium">Sagsnummer</label>
                <input 
                    type="text" 
                    wire:model.live="form.sagsnr"
                    wire:keydown.enter.prevent
                    class="mt-1 w-full rounded-md border-gray-300" 
                />
            </div>

            <div>
                <label class="block text-sm font-medium">Kreditor nr</label>
                <input type="text" wire:model.lazy="form.kreditor_lotusID" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            <div class="relative">
                <label class="block text-sm font-medium">Kreditor / Firma</label>

                <input
                    type="text"
                    wire:model.live.debounce.300ms="form.kreditor_navn"
                    
                    @if(!$isSearchMode && $showKreditorDropdown && !empty($kreditorSuggestions))
                        wire:keydown="handleKreditorKeydown($event.key)"
                        wire:click="showKreditorDropdown = true"
                        wire:click.away="closeKreditorDropdown"
                    @endif

                    autocomplete="off"
                    class="mt-1 w-full rounded-md border-gray-300"
                />

                @if(!$isSearchMode && $showKreditorDropdown && !empty($kreditorSuggestions))
                    <ul class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto">
                        @foreach($kreditorSuggestions as $k)
                            <li
                                wire:click="selectKreditor({{ $k['id'] }})"
                                class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex justify-between"
                            >
                                <span>{{ $k['navn'] }}</span>
                                <span class="text-gray-500 text-sm">{{ $k['lotusID'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>


            <div>
                <label class="block text-sm font-medium">Sag modtaget</label>
                <input type="date" wire:model.lazy="form.modtaget" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            {{-- Row 2 --}}
            <div>
                <label class="block text-sm font-medium">Status</label>
                @if($isSearchMode)
                    <input 
                        type="text" 
                        wire:model.lazy="form.status"
                        placeholder="Søg status..."
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                @else
                    <select wire:model="form.status" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->status)
                            <option value="">Vælg status</option>
                        @endif 
                        @foreach(($selectOptions['status'] ?? []) as $id => $tekst)
                            <option value="{{ $id }}" @selected($form->status == $id)>
                                {{ $tekst }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium">Seneste rapport</label>
                <input type="date" wire:model.lazy="form.senesterapport" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            <div>
                <label class="block text-sm font-medium">Sagsbehandler</label>
                    
                @if($isSearchMode)
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="form.sagsbehandler"
                        placeholder="Søg sagsbehandler..."
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                @else
                    <select wire:model="form.sagsbehandler" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->sagsbehandler)
                            <option value="">Vælg sagsbehandler</option>
                        @endif
                        @foreach(($selectOptions['sagsbehandler'] ?? []) as $id => $navn)
                            <option value="{{ $id }}" @selected($form->sagsbehandler == $id)>
                                {{ $navn }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium">Afsluttet</label>
                <input type="date" wire:model.lazy="form.afsluttet" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            {{-- Row 3 --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium">Aktiv</label>
                <input type="text" wire:model.lazy="form.aktiv" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            <div>
                <label class="block text-sm font-medium">Stelnummer</label>
                <input type="text" wire:model.lazy="form.stelnr" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            <div>
                <label class="block text-sm font-medium">Betalt</label>
                <input type="date" wire:model.lazy="form.betalt" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            {{-- Row 4 --}}
            <div>
                <label class="block text-sm font-medium">Kontrakttype</label>
                @if($isSearchMode)
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="form.ktr"
                        placeholder="Søg kontrakttype..."
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                @else
                    <select wire:model="form.ktr" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->ktr)
                            <option value="">Vælg kontrakttype</option>
                        @endif
                        @foreach(($selectOptions['ktr'] ?? []) as $id => $tekst)
                            <option value="{{ $id }}" @selected($form->ktr == $id)>
                                {{ $tekst }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium">Normal mdl. ydelse</label>
                <input type="text" wire:model.lazy="form.n_mdlydelse" class="mt-1 w-full rounded-md border-gray-300 text-right" />
            </div>

            <div>
                <label class="block text-sm font-medium">Konsulent</label>
                @if($isSearchMode)
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="form.konsulent"
                        placeholder="Søg konsulent..."
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                @else
                    <select wire:model="form.konsulent" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->konsulent)
                            <option value="">Vælg konsulent</option>
                        @endif
                        @foreach(($selectOptions['konsulent'] ?? []) as $id => $navn)
                            <option value="{{ $id }}" @selected($form->konsulent == $id)>
                                {{ $navn }}
                            </option>
                        @endforeach
                    </select>
                @endif

                
            </div>

            <div>
                <label class="block text-sm font-medium">Faktureret</label>
                <input type="date" wire:model.lazy="form.faktureret" class="mt-1 w-full rounded-md border-gray-300" />
            </div>

            {{-- Row 5 --}}
            <div>
                <label class="block text-sm font-medium">Udlæg bilbogen</label>
                 @if($isSearchMode)
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="form.udlaeg"
                        placeholder="Søg udlæg..."
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                @else
                    <select wire:model="form.udlaeg" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->udlaeg)
                            <option value="">Vælg udlæg</option>
                        @endif
                        @foreach(($selectOptions['udlaeg'] ?? []) as $id => $tekst)
                            <option value="{{ $id }}" @selected($form->udlaeg == $id)>
                                {{ $tekst }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div></div>
            <div></div>

            <div>
                <label class="block text-sm font-medium">Afslutning</label>
                @if($isSearchMode)
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="form.afslutning"
                        placeholder="Søg afslutning..."
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                @else
                    <select wire:model="form.afslutning" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->afslutning)
                            <option value="">Vælg afslutning</option>
                        @endif 
                        @foreach(($selectOptions['afslutning'] ?? []) as $id => $tekst)
                            <option value="{{ $id }}" @selected($form->afslutning == $id)>
                                {{ $tekst }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <label class="block text-sm font-medium mt-2">Bemærkning</label>
                @if($isSearchMode)
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="form.bemaerkning"
                        placeholder="Søg bemærkning..."
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                @else
                    <select wire:model="form.bemaerkning" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->bemaerkning)
                            <option value="">Vælg bemærkning</option>
                        @endif
                        @foreach(($selectOptions['bemaerkning'] ?? []) as $id => $tekst)
                            <option value="{{ $id }}" @selected($form->bemaerkning == $id)>
                                {{ $tekst }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= --}}
{{-- SECTION 2: Debitor --}}
{{-- 6 rows, 3 columns per mockup --}}
{{-- ================= --}}
<div class="bg-white shadow rounded-lg p-5">
<h2 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Debitor</h2>


<div class="grid grid-cols-3 gap-4">


{{-- Column 1 --}}
<div class="space-y-4">
<div>
<label class="block text-sm font-medium">Navn</label>
<input type="text" wire:model.lazy="form.navn" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div>
<label class="block text-sm font-medium">c/o</label>
<input type="text" wire:model.lazy="form.co" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div>
<label class="block text-sm font-medium">Adresse</label>
<input type="text" wire:model.lazy="form.adresse" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Postnr</label>
        <input
            type="text"
            wire:model.lazy="form.postnr"
            class="mt-1 w-full rounded-md border-gray-300"
        />
    </div>

    <div class="relative">
        <label class="block text-sm font-medium">By</label>
        <input
            type="text"
            wire:model.debounce.250ms="form.by"
            wire:click="showByDropdown = true"
            wire:click.away="showByDropdown = false"
            autocomplete="off"
            class="mt-1 w-full rounded-md border-gray-300"
        />

        @if($showByDropdown && count($bySuggestions))
            <ul class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto">
                @foreach($bySuggestions as $item)
                    <li
                        wire:click="selectBy('{{ addslashes($item['by']) }}', '{{ $item['postnr'] }}')"
                        class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex justify-between"
                    >
                        <span>{{ $item['by'] }}</span>
                        <span class="text-gray-500 text-sm">{{ $item['postnr'] }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>
<div>
<label class="block text-sm font-medium">PNR</label>
<input type="text" wire:model.lazy="form.pnr" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div>
<label class="block text-sm font-medium">Seneste adressesøgning</label>
<input type="date" wire:model.lazy="form.adropl" class="mt-1 w-full rounded-md border-gray-300" />
</div>
</div>


{{-- Column 2 --}}
<div class="space-y-4">
<div>
<label class="block text-sm font-medium">Mail</label>
<input type="email" wire:model.lazy="form.email" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div>
<label class="block text-sm font-medium">Tlf</label>
<input type="text" wire:model.lazy="form.tlf" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div>
<label class="block text-sm font-medium">Mobil</label>
<input type="text" wire:model.lazy="form.mobil" class="mt-1 w-full rounded-md border-gray-300" />
</div>
</div>


{{-- Column 3 --}}
<div>
<label class="block text-sm font-medium">Kontakt bemærkning</label>
<textarea wire:model.lazy="form.kontakt_bemaerkning" rows="8" class="mt-1 w-full rounded-md border-gray-300"></textarea>
</div>


</div>
</div>

    
{{-- ================= --}}
{{-- SECTION 3: Økonomi --}}
{{-- 8 rows, 3 columns per mockup --}}
{{-- ================= --}}
<div class="bg-white shadow rounded-lg p-5">
<h2 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Økonomi</h2>


<div class="grid grid-cols-3 gap-4">


{{-- Column 1 --}}
{{-- Column 1 --}}

<div class="space-y-4">
@foreach([
    'hovedstol' => 'Restance/gældpost',
    'renter' => 'Renter',
    'gebyr' => 'Inkassosalær/gebyr',
    'ialt' => 'I alt',
    'resterende' => 'Resterende',
    'indbetalt' => 'Indbetalt',
    'restgaeld_kreditor' => 'Restgæld kreditor',
    'restgaeld_dkg' => 'Restgæld inkl. inkassosalær',
] as $field => $label)

@php
    $readonly = in_array($field, ['ialt','resterende','restgaeld_dkg']);
@endphp

<div>
    <label class="block text-sm font-medium">{{ $label }}</label>

    <input
        type="text"
        wire:model.debounce.400ms="form.{{ $field }}"
        @if(!$readonly) wire:blur="formatOnBlur('{{ $field }}')" @endif
        @if($readonly) readonly @endif
        class="mt-1 w-full rounded-md border-gray-300 text-right
               {{ $readonly ? 'bg-gray-100 cursor-not-allowed' : '' }}"
    />
</div>

@endforeach
</div>

{{-- Column 2 --}}
<div class="space-y-4">
<div>
<label class="block text-sm font-medium">Faktura dato</label>
<input type="date" wire:model.lazy="form.fakturadato" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div>
<label class="block text-sm font-medium">Fakturanr</label>
<input type="text" wire:model.lazy="form.fakturanr" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div>
<label class="block text-sm font-medium">Startgebyr</label>
<input type="text" wire:model.lazy="form.startgebyr" class="mt-1 w-full rounded-md border-gray-300 text-right" />
</div>
<div>
<label class="block text-sm font-medium">Kode</label>
<input type="text" wire:model.lazy="form.kode" class="mt-1 w-full rounded-md border-gray-300" />
</div>
<div class="mt-10" style="margin-top:11.3em">
<label class="block text-sm font-medium">Dato</label>
<input type="date" wire:model.lazy="form.dato" class="mt-1 w-full rounded-md border-gray-300" />
</div>
</div>


{{-- Column 3 --}}
<div>
<label class="block text-sm font-medium">Kort bemærkning</label>
<textarea wire:model.lazy="form.kort_bemaerkning" rows="25" class="mt-1 w-full rounded-md border-gray-300"></textarea>
</div>
</div>
</div>