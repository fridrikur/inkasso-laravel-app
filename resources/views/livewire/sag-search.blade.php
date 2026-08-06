<div class="relative">

    <x-search-breadcrumbs />

    {{-- 🔄 LOADING OVERLAY --}}
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


    {{-- 🧠 SEARCH FORM --}}
    @if(!$showResults)
        @include('livewire.sager.partials.form-fields', [
            'form' => $form,
            'isSearchMode' => true,
            'selectOptions' => $selectOptions ?? [],
        ])
    @endif


    {{-- ========================= --}}
    {{-- 📦 SEARCH RESULTS (TABS) --}}
    {{-- ========================= --}}
    {{-- ========================= --}}
{{-- 📦 SEARCH RESULTS --}}
{{-- ========================= --}}
@if($showResults && collect($results ?? [])->isNotEmpty())

    @php
        $results = collect($results);
        $activeSag = $results->firstWhere('id', $activeSagId) ?? $results->first();
    @endphp


    {{-- 🔖 TABS (clean, single row) --}}
    <div class="mt-6 border-b flex gap-1 overflow-x-auto">

        @foreach($results as $sag)

            <button
                type="button"
                wire:click="setActiveSag({{ $sag->id }})"
                class="px-3 py-2 text-xs border-t border-l border-r rounded-t whitespace-nowrap transition
                    {{ $activeSagId === $sag->id
                        ? 'bg-white font-semibold'
                        : 'bg-slate-100 hover:bg-slate-200' }}"
            >
                {{ $sag->sagsnr }}
            </button>

        @endforeach

    </div>


    {{-- 🧾 ACTIVE FORM CARD --}}
    @if($activeSag)

        <div class="mt-6 flex justify-center">
            <div class="w-full max-w-5xl bg-white shadow-xl rounded-xl border">

                {{-- HEADER --}}
                <div class="p-4 border-b bg-slate-50 rounded-t-xl flex justify-between items-center">

                    <div class="text-sm font-semibold text-slate-700">
                        Sag {{ $activeSag->sagsnr }}
                    </div>

                    {{-- ✏️ EDIT BUTTON (inside form, correct UX) --}}
                    <a
                        href="{{ url('/sager/' . $activeSag->id . '/edit') }}"
                        class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
                    >
                        Edit
                    </a>

                </div>

                {{-- FORM --}}
                <div class="p-4">
                    @include('livewire.sager.partials.form-fields', [
                        'form' => $this->mapSagToForm($activeSag),
                        'isSearchMode' => true,
                        'selectOptions' => $selectOptions ?? [],
                    ])
                </div>

            </div>
        </div>

    @endif

@endif

</div>