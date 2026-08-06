<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                Tekstadministration
            </h1>

            <p class="text-slate-500 mt-1">
                Administration af statustekster og autotekster
            </p>
        </div>

        <button
            wire:click="create"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow"
        >
            + Ny tekst
        </button>

    </div>

    @include('livewire.tekster.partials.status')

    {{-- Tabs --}}
    <div class="bg-white rounded-2xl shadow-sm p-2 mb-6 flex gap-2">

        <button
            wire:click="$set('tab','status')"
            class="px-4 py-2 rounded-xl transition
            {{ $tab === 'status'
                ? 'bg-indigo-600 text-white'
                : 'text-slate-600 hover:bg-slate-100'
            }}"
        >
            Status
        </button>

        <button
            wire:click="$set('tab','autotekst')"
            class="px-4 py-2 rounded-xl transition
            {{ $tab === 'autotekst'
                ? 'bg-indigo-600 text-white'
                : 'text-slate-600 hover:bg-slate-100'
            }}"
        >
            Autotekster
        </button>

    </div>

    @include('livewire.tekster.partials.toolbar')

    @if($tab === 'status')
        @include('livewire.tekster.partials.status-table')
    @endif

    @if($tab === 'autotekst')
        @include('livewire.tekster.partials.autotekst-table')
    @endif

    @include('livewire.tekster.partials.editor-modal')

    @include('livewire.tekster.partials.delete-modal')

</div>