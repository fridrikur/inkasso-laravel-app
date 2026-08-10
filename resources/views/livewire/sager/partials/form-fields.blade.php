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
@endphp

{{-- ====================================================== --}}
{{-- SECTION 1: Generelt / Kreditor / Status                --}}
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

    {{-- ====================================================== --}}
    {{-- SECTION 2: Debitor                                     --}}
    {{-- ====================================================== --}}
    <div class="bg-white shadow rounded-lg p-5">
        <h2 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">Debitor</h2>

        <div class="grid grid-cols-3 gap-6">
            {{-- Column 1: Stamdata --}}
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Navn(e)</label>
                    <input type="text" wire:model.lazy="form.navn" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">c/o</label>
                    <input type="text" wire:model.lazy="form.co" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adresse</label>
                    <input type="text" wire:model.lazy="form.adresse" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Post nr</label>
                        <input type="text" wire:model.lazy="form.postnr" placeholder="xxxx" class="mt-1 w-full rounded-md border-gray-300" />
                    </div>

                    <div class="col-span-2 relative">
                        <label class="block text-sm font-medium text-gray-700">By</label>
                        <input
                            type="text"
                            wire:model.debounce.250ms="form.by"
                            wire:click="showByDropdown = true"
                            wire:click.away="showByDropdown = false"
                            placeholder="København"
                            autocomplete="off"
                            class="mt-1 w-full rounded-md border-gray-300"
                        />

                        @if($showByDropdown && count($bySuggestions))
                            <ul class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto">
                                @foreach($bySuggestions as $item)
                                    <li
                                        wire:click="selectBy('{{ addslashes($item['by']) }}', '{{ $item['postnr'] }}')"
                                        class="px-3 py-2 cursor-pointer hover:bg-gray-100 flex justify-between text-xs"
                                    >
                                        <span>{{ $item['by'] }}</span>
                                        <span class="text-gray-500 font-mono">{{ $item['postnr'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">CPR/CVR</label>
                    <input type="text" wire:model.lazy="form.pnr" placeholder="CPR/CVR" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adressesøgning</label>
                    <input type="date" wire:model.lazy="form.adropl" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
            </div>

            {{-- Column 2: Kontakter --}}
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mail #1</label>
                    <input type="email" wire:model.lazy="form.email" placeholder="Mail" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mail #2</label>
                    <input type="email" wire:model.lazy="form.email2" placeholder="Mail" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefon #1</label>
                    <input type="text" wire:model.lazy="form.tlf" placeholder="Telefon #1" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefon #2</label>
                    <input type="text" wire:model.lazy="form.tlf2" placeholder="Telefon #2" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Telefon #3</label>
                    <input type="text" wire:model.lazy="form.mobil" placeholder="Telefon #3" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
            </div>

            {{-- Column 3: Bemærkningsfelt --}}
            <div class="flex flex-col">
                <label class="block text-sm font-medium text-gray-700 mb-1">Bemærkningsfelt vedr. fx kontakt</label>
                <textarea wire:model.lazy="form.kontakt_bemaerkning" placeholder="TEKST" class="w-full flex-1 rounded-md border-gray-300 min-h-[220px]"></textarea>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- SECTION 3: Økonomi (M. Interaktiv Beregningshjælp)     --}}
    {{-- ====================================================== --}}
    <div class="bg-white shadow rounded-lg p-5" x-data="{ showCalcHelp: false }">
        <div class="border-b pb-2 mb-4 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-700">Økonomi</h2>
            
            {{-- TOGGLE KNAP TIL HJÆLPEVISNING --}}
            <button 
                type="button" 
                @click="showCalcHelp = !showCalcHelp" 
                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 transition shadow-sm cursor-pointer"
            >
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="showCalcHelp ? 'Skjul beregningshjælp' : 'Vis beregningshjælp'"></span>
            </button>
        </div>

        <div class="grid grid-cols-3 gap-4">

            {{-- Column 1: BEREGNINGSFELTER --}}
            <div class="space-y-4">
                @foreach([
                    'hovedstol'          => ['label' => 'Restance/gældpost',           'letter' => 'A', 'formula' => null],
                    'renter'             => ['label' => 'Renter',                      'letter' => 'B', 'formula' => null],
                    'gebyr'              => ['label' => 'Inkassosalær/gebyr',          'letter' => 'C', 'formula' => null],
                    'ialt'               => ['label' => 'I alt',                       'letter' => 'D', 'formula' => 'A + B + C'],
                    'resterende'         => ['label' => 'Resterende',                  'letter' => 'E', 'formula' => 'D ÷ F (D minus F)'],
                    'indbetalt'          => ['label' => 'Indbetalt',                   'letter' => 'F', 'formula' => null],
                    'restgaeld_kreditor' => ['label' => 'Restgæld kreditor',           'letter' => 'G', 'formula' => null],
                    'restgaeld_dkg'      => ['label' => 'Restgæld inkl. inkassosalær', 'letter' => 'H', 'formula' => 'G + C'],
                ] as $field => $info)

                    @php
                        $readonly = in_array($field, ['ialt', 'resterende', 'restgaeld_dkg']);
                    @endphp

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ $info['label'] }}
                            </label>
                            
                            {{-- BOGSTAV BOGMÆRKE (Viser A, B, C... H) --}}
                            <span 
                                x-show="showCalcHelp" 
                                x-transition
                                class="text-[10px] font-bold font-mono px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700 border border-indigo-200"
                            >
                                Felt {{ $info['letter'] }}
                            </span>
                        </div>

                        <input
                            type="text"
                            wire:model.debounce.400ms="form.{{ $field }}"
                            @if(!$readonly) wire:blur="formatOnBlur('{{ $field }}')" @endif
                            @if($readonly) readonly @endif
                            class="w-full rounded-md border-gray-300 text-right {{ $readonly ? 'bg-gray-100 cursor-not-allowed font-semibold text-gray-800' : '' }}"
                        />

                        {{-- BEREGNINGSFORMLER SOM VISES NÅR TOGGLE ER AKTIV --}}
                        @if($info['formula'])
                            <div 
                                x-show="showCalcHelp" 
                                x-transition 
                                class="text-[11px] text-indigo-600 font-medium mt-1 flex items-center gap-1"
                            >
                                <span>🧮 Formel:</span>
                                <span class="font-bold bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100">
                                    {{ $info['letter'] }} = {{ $info['formula'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                @endforeach
            </div>

            {{-- Column 2: Faktura, Startgebyr mm. --}}
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
                <div style="margin-top: 11.3em;">
                    <label class="block text-sm font-medium">Dato</label>
                    <input type="date" wire:model.lazy="form.dato" class="mt-1 w-full rounded-md border-gray-300" />
                </div>
            </div>

            {{-- Column 3: Kort bemærkning --}}
            <div>
                <label class="block text-sm font-medium">Kort bemærkning</label>
                <textarea wire:model.lazy="form.kort_bemaerkning" rows="25" class="mt-1 w-full rounded-md border-gray-300"></textarea>
            </div>

        </div>
    </div>
</div>