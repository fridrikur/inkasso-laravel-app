<div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow space-y-6">

    <h2 class="text-2xl font-bold">
        Sag: {{ $sag->id }} 
    </h2>

    {{-- Tabs --}}
    <div class="flex space-x-6 border-b pb-2">

        @include('components.sag-tabs.klientinformation-tab', [
            'sag' => $sag,
            'klientinformationUnread' => $klientinformationUnread
        ])
        {{-- Dokumenter --}}
        <a href="{{ route('kreditor.sager.dokumenter.index', $sag->id) }}"
        class="{{ request()->routeIs('sager.dokumenter.*')
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
        whitespace-nowrap pb-2 border-b-2 font-medium text-sm flex items-center gap-2">

            <span>Dokumenter</span>

            @if($this->dokumenterCount > 0)
                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                    {{ $this->dokumenterCount }}
                </span>
            @endif

        </a>

    </div>

    {{-- Financial Information --}}
    <div class="grid grid-cols-2 gap-6">

        <div>
            <label class="block text-sm font-medium">Hovedstol</label>
            <p class="mt-1">{{ $this->formatNumber($sag->hovedstol) }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium">Renter</label>
            <p class="mt-1">{{ $this->formatNumber($sag->renter) }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium">Gebyr</label>
            <p class="mt-1">{{ $this->formatNumber($sag->gebyr) }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium">I alt</label>
            <p class="mt-1">{{ $this->formatNumber($sag->ialt) }}</p>
        </div>

    </div>

    {{-- Debitor information --}}
    <div>

        <div class="mt-4">
            <label class="block text-sm font-medium">Debitor navn</label>
            <p class="mt-1">{{ $debitor->navn ?? '-' }}</p>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium">Adresse</label>
            <p class="mt-1">{{ $debitor->adresse ?? '-' }}</p>
        </div>

    </div>

    {{-- Sagsbehandler --}}
    <div>
        <label class="block text-sm font-medium">Sagsbehandler</label>
        <p class="mt-1">
            {{ $sag->sagersagsbehandler->first()?->navn ?? '-' }}
        </p>
    </div>

    {{-- Date --}}
    <div>
        <label class="block text-sm font-medium">Dato</label>
        <p class="mt-1">
            {{ optional($sag->dato)->format('d-m-Y') ?? '-' }}
        </p>
    </div>

    {{-- Note --}}
    <div>
        <label class="block text-sm font-medium">Kort bemærkning</label>
        <p class="mt-1 whitespace-pre-line">
            {{ $sag->kort_bemaerkning ?? '-' }}
        </p>
    </div>

    {{-- Dokument Upload --}}
    <div class="border-t pt-6">

        <h3 class="text-lg font-semibold mb-4">
            Upload dokument
        </h3>

        <form
        action="{{ route('kreditor.sager.dokumenter.store', $sag) }}"
        method="POST"
        enctype="multipart/form-data"
        class="flex items-center gap-4">

            @csrf

            <input type="file" name="file" required>

            <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

                Upload

            </button>

        </form>

    </div>

</div>