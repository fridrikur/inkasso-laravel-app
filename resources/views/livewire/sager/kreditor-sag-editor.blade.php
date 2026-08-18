<div>

{{-- =========================
FORM MODE
========================= --}}
@if(!$reviewMode)

<form wire:submit.prevent="save" id="formContainer" class="space-y-4">

    <h2 class="text-lg font-semibold mb-4">
        Opret sag
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

            <div>
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

                {{-- Danish number fields --}}
                @elseif(in_array($field, ['hovedstol', 'renter', 'gebyr', 'indbetalt']))

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
                        wire:model.defer="form.{{ $field }}"
                        class="mt-1 w-full rounded-md border-gray-300"
                    />

                @endif
            </div>

        @endforeach
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

    <h3 class="review-section">Sag oplysninger</h3>

    <div class="review-row">
        <div class="review-label">Sagsnummer</div>
        <div class="review-value">{{ $form->sagsnr }}</div>
    </div>

    <div class="review-row">
        <div class="review-label">Sagsbehandler</div>
        <div class="review-value">
            {{ $sagsbehandlerOptions[$form->sagsbehandler] ?? '' }}
        </div>
    </div>

    <h3 class="review-section">Debitor</h3>

    <div class="review-row">
        <div class="review-label">Navn</div>
        <div class="review-value">{{ $form->navn }}</div>
    </div>

    <div class="review-row">
        <div class="review-label">Adresse</div>
        <div class="review-value">{{ $form->adresse }}</div>
    </div>

    <div class="review-row">
        <div class="review-label">Postnr</div>
        <div class="review-value">{{ $form->postnr }}</div>
    </div>

    <div class="review-row">
        <div class="review-label">By</div>
        <div class="review-value">{{ $form->by }}</div>
    </div>

    <h3 class="review-section">Økonomi</h3>

    <div class="review-row">
        <div class="review-label">Hovedstol</div>
        <div class="review-value">{{ $form->hovedstol }}</div>
    </div>

    <div class="review-row">
        <div class="review-label">Renter</div>
        <div class="review-value">{{ $form->renter }}</div>
    </div>

    <div class="review-row">
        <div class="review-label">Gebyr</div>
        <div class="review-value">{{ $form->gebyr }}</div>
    </div>

    <div class="review-row">
        <div class="review-label">Indbetalt</div>
        <div class="review-value">{{ $form->indbetalt }}</div>
    </div>

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

    <div class="stamp-header">
        ✅ BEKRÆFTET
    </div>

    <div class="stamp-sub">
        Sagen er sendt til DKG
    </div>

    <div class="stamp-body">

        <div class="review-row">
            <div class="review-label">Sagsnummer</div>
            <div class="review-value font-bold">
                {{ $this->sag->sagsnr ?? '-' }}
            </div>
        </div>

        <div class="review-row">
            <div class="review-label">Debitor</div>
            <div class="review-value">{{ $form->navn }}</div>
        </div>

        <div class="review-row">
            <div class="review-label">Sagsbehandler</div>
            <div class="review-value">
                {{ $sagsbehandlerOptions[$form->sagsbehandler] ?? '' }}
            </div>
        </div>

        <div class="review-row">
            <div class="review-label">Hovedstol</div>
            <div class="review-value">{{ $form->hovedstol }}</div>
        </div>

    </div>

    <div class="stamp-footer">
        Sendt: {{ now()->format('d-m-Y H:i') }}
    </div>
    <div class="text-sm text-gray-500 mt-3 text-right">
    Viderestilles om <span id="countdown">30</span> sekunder...
    </div>

</div>

</div>

@endif


{{-- =========================
SCRIPTS
========================= --}}
<script>
    let countdownInterval;
    let seconds = 30;
    let isPaused = false;

    document.addEventListener('livewire:init', () => {

        Livewire.on('startRedirectTimer', () => {

            clearInterval(countdownInterval);
            seconds = 30;
            isPaused = false;

            countdownInterval = setInterval(() => {

                if (isPaused) return;

                const el = document.getElementById('countdown');

                if (el) {
                    el.innerText = seconds;
                }

                seconds--;

                if (seconds < 0) {
                    clearInterval(countdownInterval);
                    // startFadeOutAndRedirect();
                    window.location.href = "{{ route('kreditor.sager.index') }}?created=1&sag_id={{ $this->sag->id }}";
                }

            }, 1000);

            // 👇 Pause on hover
            setTimeout(() => {
                const modal = document.getElementById('successModal');

                if (modal) {
                    modal.addEventListener('mouseenter', () => isPaused = true);
                    modal.addEventListener('mouseleave', () => isPaused = false);
                }
            }, 100); // wait for DOM render

        });

        Livewire.on('stopRedirectTimer', () => {
            clearInterval(countdownInterval);
        });

    });
    function startFadeOutAndRedirect() {

        const modal = document.getElementById('successModal');

        if (modal) {
            modal.classList.add('fade-out');

            setTimeout(() => {
                window.location.href = "{{ route('kreditor.sager.index') }}?created=1";
            }, 400);
        } else {
            window.location.href = "{{ route('kreditor.sager.index') }}?created=1";
        }
    }
</script>


{{-- =========================
STYLES
========================= --}}
<style>

    .fade-out{
    animation: fadeOut 0.4s ease forwards;
    }

    @keyframes fadeOut{
        from{
            opacity:1;
            transform:scale(1);
        }
        to{
            opacity:0;
            transform:scale(0.95);
        }
    }

    .review-paper{
        background:#f8f8f8;
        border:1px solid #dcdcdc;
        padding:30px;
        max-width:800px;
    }

.review-section{
    margin-top:20px;
    font-weight:700;
    border-bottom:2px solid #dcdcdc;
    padding-bottom:4px;
    margin-bottom:10px;
}

.review-row{
    display:flex;
    border-bottom:1px solid #e5e5e5;
    padding:8px 0;
}

.review-label{
    width:220px;
    font-weight:600;
    color:#444;
}

.review-value{
    flex:1;
}

/* MODAL */
.modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:999;
}

.modal-box{
    background:white;
    padding:30px;
    border-radius:10px;
    width:100%;
    max-width:600px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    animation:fadeIn 0.2s ease;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.stamp-container{
    animation:stampPop 0.25s ease;
}

@keyframes stampPop{
    from{
        transform:scale(0.9) rotate(-2deg);
        opacity:0;
    }
    to{
        transform:scale(1) rotate(0);
        opacity:1;
    }
}
</style>

</div>