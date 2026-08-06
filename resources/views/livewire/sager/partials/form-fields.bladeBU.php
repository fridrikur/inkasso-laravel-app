<div class="max-w-6xl mx-auto p-6 space-y-6">

    {{-- Header / Actions --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800">Rediger sag</h1>
    </div>
        
{{-- ================= --}}
    {{-- Card: Debitor --}}
    {{-- ================= --}}
    <div class="bg-white shadow rounded-lg p-5">
        <h2 class="text-lg font-medium text-gray-700 border-b pb-2 mb-4">
            Debitor
        </h2>

        <div class="grid grid-cols-3 gap-4">
            @foreach($debitorFieldSettings as $setting)
                @php $field = $setting->field_name; 
                        $fieldName = $setting->field_name;
                        $alias = $setting->alias ?? ucfirst($fieldName);
                        $type = strtolower($setting->field_type ?? 'text');
                    @endphp

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ $setting->alias }}
                        @if($setting->required)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    @if($type === 'select')
                            <select wire:model.lazy="form.{{ $fieldName }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                                <option value="">Vælg...</option>
                                @foreach($this->getSelectOptions($fieldName) as $id => $label)
                                    <option value="{{ $id }}" @selected((string)$id === (string)$form->{$fieldName})>{{ $label }}</option>
                                @endforeach
                            </select>

                        @elseif($type === 'date')
                            <input type="date" wire:model.lazy="form.{{ $fieldName }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">

                        @else
                            <input type="text" wire:model.lazy="form.{{ $fieldName }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 text-right">
                        @endif

                        @error('form.' . $fieldName) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                    @error('form.'.$field)
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
    </div>
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-9 grid grid-cols-3 gap-4">
                @foreach($debitorFields as $field => $props)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ $props['label'] }}</label>
                        <input
                            type="{{ $props['type'] }}"
                            maxlength="{{ $props['maxlength'] ?? '' }}"
                            wire:model.lazy="form.{{ $field }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                        >
                        @error('form.'.$field) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
    </div>

    {{-- Card: Relations & Kreditor --}}
    <div class="bg-white shadow rounded-lg p-5 grid grid-cols-12 gap-6">
        <div class="col-span-8 space-y-4">
            {{-- Kreditor LotusID --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Kreditor LotusID</label>
                <input
                    type="text"
                    wire:model.blur="form.kreditor_lotusID"
                    placeholder="Skriv LotusID og tab ud af feltet"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                >
            </div>

            {{-- Kreditor navn --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Kreditor navn</label>
                <input
                    type="text"
                    wire:model.lazy="form.kreditor_navn"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                >
            </div>

            {{-- Selects row (ktr, status, afslutning, bemaerkning) --}}
            <div class="grid grid-cols-2 gap-4">
                @if(!empty($selectOptions['ktr']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">KTR</label>
                    <select wire:model="form.ktr" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Vælg ktr...</option>
                        @foreach($selectOptions['ktr'] as $id => $navn)
                            <option value="{{ $id }}" @selected((string)$id === (string)$form->ktr)>{{ $navn }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(!empty($selectOptions['udlaeg']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Udlæg</label>
                    <select wire:model="form.udlaeg" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Vælg udlæg...</option>
                        @foreach($selectOptions['udlaeg'] as $id => $navn)
                            <option value="{{ $id }}" @selected((string)$id === (string)$form->udlaeg)>{{ $navn }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(!empty($selectOptions['status']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Vælg status...</option>
                        @foreach($selectOptions['status'] as $id => $navn)
                            <option value="{{ $id }}" @selected((string)$id === (string)$form->status)>{{ $navn }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                @if(!empty($selectOptions['afslutning']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Afslutning</label>
                    <select wire:model="form.afslutning" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Vælg afslutning...</option>
                        @foreach($selectOptions['afslutning'] as $id => $navn)
                            <option value="{{ $id }}" @selected((string)$id === (string)$form->afslutning)>{{ $navn }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if(!empty($selectOptions['bemaerkning']))
                <div>
                    <label class="block text-sm font-medium text-gray-700">Bemærkning</label>
                    <select wire:model="form.bemaerkning" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                        <option value="">Vælg bemærkning...</option>
                        @foreach($selectOptions['bemaerkning'] as $id => $navn)
                            <option value="{{ $id }}" @selected((string)$id === (string)$form->bemaerkning)>{{ $navn }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        {{-- Right column: konsulent + sagsbehandler --}}
        <div class="col-span-4 space-y-4">
            @if(!empty($selectOptions['konsulent']))
            <div>
                <label class="block text-sm font-medium text-gray-700">Konsulent</label>
                <select wire:model="form.konsulent" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                    <option value="">Vælg konsulent...</option>
                    @foreach($selectOptions['konsulent'] as $id => $navn)
                        <option value="{{ $id }}" @selected((string)$id === (string)$form->konsulent)>{{ $navn }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if(!empty($selectOptions['sagsbehandler']))
            <div>
                <label class="block text-sm font-medium text-gray-700">Sagsbehandler</label>
                <select wire:model="form.sagsbehandler" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                    <option value="">Vælg sagsbehandler...</option>
                    @foreach($selectOptions['sagsbehandler'] as $id => $navn)
                        <option value="{{ $id }}" @selected((string)$id === (string)$form->sagsbehandler)>{{ $navn }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- Card: Dynamic Fields (single rendition, no repeats) --}}
    <div class="bg-white shadow rounded-lg p-5">
        @foreach($fieldSections as $sectionName => $fields)
            <h3 class="text-lg font-medium text-gray-700 mt-4 mb-3">{{ ucfirst($sectionName) }}</h3>

            @php $cols = ($sectionName === 'financial') ? 3 : 4; @endphp
            <div class="grid grid-cols-{{ $cols }} gap-4 mb-6">
                @foreach($fields as $setting)
                    @php
                        $fieldName = $setting->field_name;
                        $alias = $setting->alias ?? ucfirst($fieldName);
                        $type = strtolower($setting->field_type ?? 'text');
                    @endphp

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $alias }}</label>

                        @if($type === 'select')
                            <select wire:model.lazy="form.{{ $fieldName }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">
                                <option value="">Vælg...</option>
                                @foreach($this->getSelectOptions($fieldName) as $id => $label)
                                    <option value="{{ $id }}" @selected((string)$id === (string)$form->{$fieldName})>{{ $label }}</option>
                                @endforeach
                            </select>

                        @elseif($type === 'date')
                            <input type="date" wire:model.lazy="form.{{ $fieldName }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400">

                        @else
                            <input type="text" wire:model.lazy="form.{{ $fieldName }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-400 text-right">
                        @endif

                        @error('form.' . $fieldName) <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
