<div>

{{-- =========================
FORM MODE
========================= --}}
@if(!$reviewMode)

<form wire:submit.prevent="save" id="formContainer" class="space-y-4">

    <h2 class="text-lg font-semibold mb-4">
        Opret ny sag
    </h2>

    {{-- Sagsbehandler --}}
    <div>
        <label class="block text-sm font-medium">
            Sagsbehandler *
        </label>

        <select
            wire:model="form.sagsbehandler"
            class="mt-1 w-full rounded-md border-gray-300"
            required
        >
            <option value="">Vælg sagsbehandler</option>

            @foreach($sagsbehandlerOptions as $id => $navn)
                <option value="{{ $id }}">
                    {{ $navn }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Dynamic fields --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($allowedFields as $field)

            <div class="{{ in_array($field, ['navn', 'adresse']) ? 'md:col-span-2' : '' }}">
                <label class="block text-sm font-medium">
                    {{ $fieldLabels[$field] ?? ucfirst($field) }}
                </label>

                {{-- Postnr --}}
                @if($field === 'postnr')
                    <input
                        type="text"
                        wire:model.live="form.postnr"
                        class="mt-1 w-full rounded-md border-gray-300"
                    />
                    @if(!empty($form->postnr) && empty($form->by))
                        <p class="mt-1 text-sm text-red-500">
                            Postnummer ikke fundet
                        </p>
                    @endif

                {{-- By --}}
                @elseif($field === 'by')
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.250ms="form.by"
                            wire:click="$set('showByDropdown', true)"
                            wire:click.away="$set('showByDropdown', false)"
                            autocomplete="off"
                            class="mt-1 w-full rounded-md border-gray-300 bg-gray-100 text-gray-700"
                        />
                        @if(!empty($showByDropdown) && !empty($bySuggestions))
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

                {{-- Udlaeg Dropdown --}}
                @elseif(strtolower($field) === 'udlaeg')
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

                {{-- KTR Dropdown --}}
                @elseif(strtolower($field) === 'ktr')
                    <select wire:model="form.ktr" class="mt-1 w-full rounded-md border-gray-300">
                        @if(!$form->ktr)
                            <option value="">Vælg KTR</option>
                        @endif
                        @foreach(($selectOptions['ktr'] ?? []) as $id => $tekst)
                            <option value="{{ $id }}" @selected($form->ktr == $id)>
                                {{ $tekst }}
                            </option>
                        @endforeach
                    </select>

                {{-- Danish number fields --}}
                @elseif(in_array($field, ['hovedstol', 'renter', 'gebyr', 'indbetalt', 'N_ydelse']))
                    <input
                        type="text"
                        wire:model.blur="form.{{ $field }}"
                        class="mt-1 w-full rounded-md border-gray-300"
                        inputmode="decimal"
                    />

                {{-- Everything else --}}
                @else
                    <input
                        type="text"
                        wire:model="form.{{ $field }}"
                        class="mt-1 w-full rounded-md border-gray-300"
                        @if($field === 'cvr' || $field === 'co')
                            placeholder="f.eks. c/o navn eller CVR-nr."
                        @endif
                    />
                @endif
            </div>

        @endforeach
    </div>

    {{-- Upload dokument ved oprettelse --}}
    <div class="border-t pt-4 mt-4">
        <label class="block text-sm font-medium mb-1">
            Vedhæft dokument (valgfrit)
        </label>
        
        <input 
            type="file" 
            wire:model="newDokument" 
            class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
        >

        @error('newDokument') 
            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> 
        @enderror

        @if ($newDokument)
            <div class="mt-2 flex items-center justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                <p class="text-xs text-emerald-600 font-semibold flex items-center gap-1.5">
                    📎 Valgt fil: {{ $newDokument->getClientOriginalName() }}
                </p>
                <button 
                    type="button" 
                    wire:click="$set('newDokument', null)"
                    class="text-xs text-rose-600 hover:text-rose-800 font-bold px-2 py-1 bg-rose-50 hover:bg-rose-100 rounded-lg transition"
                >
                    ✕ Slet / Skift
                </button>
            </div>
        @endif
    </div>

    <div class="flex gap-3 pt-6">
        <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded shadow"
        >
            Gem
        </button>

        @if($readyForReview)
            <button
                type="button"
                wire:click="goToReview"
                class="bg-gray-700 hover:bg-gray-800 text-white px-5 py-2 rounded shadow"
            >
                Gennemse oplysninger
            </button>
        @endif
    </div>

</form>

@endif


{{-- =========================
REVIEW MODE
========================= --}}
@if($reviewMode)

<h2 class="text-lg font-semibold mb-2">
    Bekræft indsendelse
</h2>

<p class="text-gray-600 mb-6">
    Bekræft at alle oplysninger er korrekte før sagen sendes til DKG.
</p>

<div id="reviewContainer" tabindex="-1" class="review-paper">

    <h3 class="review-section">Sag- og debitoroplysninger</h3>

    {{-- Sagsbehandler --}}
    <div class="review-row">
        <div class="review-label">Sagsbehandler</div>
        <div class="review-value">
            {{ $sagsbehandlerOptions[$form->sagsbehandler] ?? '-' }}
        </div>
    </div>

    {{-- Dynamiske felter med tekst-opslag for ktr og udlaeg --}}
    @foreach($allowedFields as $field)
        @if(!in_array($field, ['hovedstol', 'renter', 'gebyr', 'indbetalt', 'N_ydelse']))
            <div class="review-row">
                <div class="review-label">
                    {{ $fieldLabels[$field] ?? ucfirst($field) }}
                </div>
                <div class="review-value">
                    @if(strtolower($field) === 'ktr')
                        {{ $selectOptions['ktr'][$form->ktr] ?? '-' }}
                    @elseif(strtolower($field) === 'udlaeg')
                        {{ $selectOptions['udlaeg'][$form->udlaeg] ?? '-' }}
                    @else
                        {{ $form->$field ?? '-' }}
                    @endif
                </div>
            </div>
        @endif
    @endforeach

    <h3 class="review-section">Økonomi</h3>

    <div class="review-row">
        <div class="review-label">Hovedstol</div>
        <div class="review-value">{{ $form->hovedstol ?? '0,00' }} kr.</div>
    </div>

    <div class="review-row">
        <div class="review-label">Renter</div>
        <div class="review-value">{{ $form->renter ?? '0,00' }} kr.</div>
    </div>

    <div class="review-row">
        <div class="review-label">Gebyr</div>
        <div class="review-value">{{ $form->gebyr ?? '0,00' }} kr.</div>
    </div>

    <div class="review-row">
        <div class="review-label">Nye ydelse</div>
        <div class="review-value">{{ $form->N_ydelse ?? '0,00' }} kr.</div>
    </div>

    <div class="review-row">
        <div class="review-label">Indbetalt</div>
        <div class="review-value">{{ $form->indbetalt ?? '0,00' }} kr.</div>
    </div>

    {{-- Vis vedhæftet dokument under review, hvis der er valgt et --}}
    @if($newDokument)
        <h3 class="review-section">Vedhæftet bilag</h3>
        <div class="review-row items-center">
            <div class="review-label">Filnavn</div>
            <div class="review-value flex items-center justify-between">
                <span class="text-indigo-600 font-semibold text-xs flex items-center gap-1.5">
                    📎 {{ $newDokument->getClientOriginalName() }}
                </span>
            </div>
        </div>
    @endif

</div>

<div class="mt-6 flex gap-4">
    <button
        wire:click="editAgain"
        class="bg-gray-600 text-white px-4 py-2 rounded"
    >
        Ret oplysninger
    </button>

    <button
        wire:click="confirmSave"
        class="bg-green-600 text-white px-4 py-2 rounded"
    >
        Bekræft og send
    </button>
</div>

@endif


{{-- =========================
SUCCESS MODAL
========================= --}}
@if($showSuccessModal)

<div
    class="modal-overlay"
    wire:click.self="$set('showSuccessModal', false)"
>

    <div class="modal-box" id="successModal">
        <div class="stamp-container">

            {{-- Officielt stempel top-header --}}
            <div class="stamp-header">
                <span class="stamp-badge">✅ BEKRÆFTET & REGISTRERET</span>
            </div>

            <div class="stamp-sub">
                Sagen er sendt til DKG
            </div>

            {{-- Scrollbar-sikret indhold, så knapperne altid er synlige i bunden --}}
            <div class="stamp-body">

                {{-- Sagsbehandler --}}
                <div class="review-row">
                    <div class="review-label">Sagsbehandler</div>
                    <div class="review-value">
                        {{ $sagsbehandlerOptions[$form->sagsbehandler] ?? '-' }}
                    </div>
                </div>

                {{-- Dynamiske felter --}}
                @foreach($allowedFields as $field)
                    @if(!in_array($field, ['hovedstol', 'renter', 'gebyr', 'indbetalt', 'N_ydelse']))
                        <div class="review-row">
                            <div class="review-label">
                                {{ $fieldLabels[$field] ?? ucfirst($field) }}
                            </div>
                            <div class="review-value {{ $field === 'sagsnr' ? 'font-bold' : '' }}">
                                @if(strtolower($field) === 'ktr')
                                    {{ $selectOptions['ktr'][$form->ktr] ?? '-' }}
                                @elseif(strtolower($field) === 'udlaeg')
                                    {{ $selectOptions['udlaeg'][$form->udlaeg] ?? '-' }}
                                @else
                                    {{ $form->$field ?? '-' }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Økonomi felter --}}
                <div class="review-row">
                    <div class="review-label">Hovedstol</div>
                    <div class="review-value">{{ $form->hovedstol ?? '0,00' }} kr.</div>
                </div>

                <div class="review-row">
                    <div class="review-label">Renter</div>
                    <div class="review-value">{{ $form->renter ?? '0,00' }} kr.</div>
                </div>

                <div class="review-row">
                    <div class="review-label">Gebyr</div>
                    <div class="review-value">{{ $form->gebyr ?? '0,00' }} kr.</div>
                </div>

                <div class="review-row">
                    <div class="review-label">Nye ydelse</div>
                    <div class="review-value">{{ $form->N_ydelse ?? '0,00' }} kr.</div>
                </div>

                <div class="review-row">
                    <div class="review-label">Indbetalt</div>
                    <div class="review-value">{{ $form->indbetalt ?? '0,00' }} kr.</div>
                </div>

            </div>

            <div class="stamp-footer">
                Sendt tidspunkt: {{ now()->format('d-m-Y H:i') }}
            </div>
            
            {{-- Fastlåst bund med timer og knapper --}}
            <div 
                x-data="{
                    seconds: 30,
                    isPaused: false,
                    timer: null,
                    init() {
                        this.timer = setInterval(() => {
                            if (this.isPaused) return;
                            
                            if (this.seconds > 0) {
                                this.seconds--;
                            } else {
                                clearInterval(this.timer);
                                window.location.href = '{{ route('kreditor.sager.index') }}?created=1&sag_id={{ $this->sag?->id ?? '' }}';
                            }
                        }, 1000);
                    },
                    destroy() {
                        clearInterval(this.timer);
                    }
                }"
                @mouseenter="isPaused = true"
                @mouseleave="isPaused = false"
                class="mt-4 pt-3 border-t border-slate-200 flex items-center justify-between shrink-0 bg-white"
            >
                <div class="text-xs text-slate-500">
                    Viderestilles om <span x-text="seconds" class="font-bold font-mono text-indigo-600">30</span> sekunder...
                </div>

                <div class="flex gap-2">
                    <a 
                        href="{{ route('kreditor.sager.index') }}?created=1&sag_id={{ $this->sag?->id ?? '' }}"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer shadow-xs"
                    >
                        Til sager
                    </a>
                    
                    <a 
                        href="{{ route('kreditor.dashboard') }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-1.5 cursor-pointer"
                    >
                        <span>🏠 Dashboard</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

@endif

{{-- =========================
STYLES
========================= --}}
<style>
    .fade-out {
        animation: fadeOut 0.4s ease forwards;
    }

    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.95); }
    }

    .review-paper {
        background: #f8f8f8;
        border: 1px solid #dcdcdc;
        padding: 30px;
        max-width: 800px;
    }

    .review-section {
        margin-top: 20px;
        font-weight: 700;
        border-bottom: 2px solid #dcdcdc;
        padding-bottom: 4px;
        margin-bottom: 10px;
    }

    .review-row {
        display: flex;
        border-bottom: 1px solid #e5e5e5;
        padding: 8px 0;
    }

    .review-label {
        width: 220px;
        font-weight: 600;
        color: #444;
    }

    .review-value {
        flex: 1;
    }

    /* MODAL DESIGN */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 1rem;
    }

    .modal-box {
        background: white;
        padding: 24px 30px;
        border-radius: 20px;
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 2px dashed #059669; /* Professionel stempel-kant */
        animation: stampHeavyDrop 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    /* Scrollbar kun for indholdet i midten, så knapperne bagerst ikke forsvinder */
    .stamp-body {
        overflow-y: auto;
        max-height: 50vh;
        padding-right: 4px;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .stamp-body::-webkit-scrollbar {
        width: 6px;
    }

    .stamp-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .stamp-header {
        text-align: center;
        margin-bottom: 2px;
    }

    .stamp-badge {
        display: inline-block;
        background-color: #ecfdf5;
        color: #047857;
        font-size: 11px;
        font-weight: 800;
        padding: 4px 12px;
        border-radius: 9999px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border: 1px solid #a7f3d0;
    }

    .stamp-sub {
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
    }

    .stamp-footer {
        text-align: right;
        font-size: 11px;
        color: #94a3b8;
        font-family: monospace;
        padding-top: 4px;
    }

    /* Ægte stempel-nedslag animation */
    @keyframes stampHeavyDrop {
        0% {
            opacity: 0;
            transform: scale(1.15) translateY(-20px) rotate(1deg);
        }
        70% {
            opacity: 1;
            transform: scale(0.98) translateY(4px) rotate(-0.5deg);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0) rotate(0);
        }
    }
</style>

</div>