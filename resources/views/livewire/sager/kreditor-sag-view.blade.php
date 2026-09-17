<div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow space-y-6">

    <h2 class="text-2xl font-bold">
        Sag: {{ $sag->sagsnr }} 
    </h2>

    {{-- Tabs --}}
    <div class="flex space-x-6 border-b pb-2">

        @include('components.sag-tabs.klientinformation-tab', [
            'sag' => $sag,
            'klientinformationUnread' => $klientinformationUnread
        ])
        {{-- Dokumenter --}}
        <a href="{{ route('sager.dokumenter.index', $sag->id) }}"
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

    {{-- Sag & Debitor Information (Dynamisk visning af felter) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-5 rounded-2xl border border-slate-100">

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Aktiv</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $sag->aktiv ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">CPR / CVR</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $sag->cvr ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Debitor navn</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $debitor->navn ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Adresse</label>
            <p class="mt-1 font-semibold text-slate-800">
                {{ $debitor->adresse ?? '-' }}<br>
                <span class="text-xs font-normal text-slate-500">
                    {{ $debitor->postnr ?? '' }} {{ optional($debitor->postnummer)->by ?? '' }}
                </span>
            </p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Sagsbehandler</label>
            <p class="mt-1 font-semibold text-slate-800">
                {{ $sag->sagsbehandler->first()?->navn ?? '-' }}
            </p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Modtaget dato</label>
            <p class="mt-1 font-semibold text-slate-800">
                {{ optional($sag->modtaget)->format('d-m-Y H:i') ?? '-' }}
            </p>
        </div>

    </div>

    {{-- Financial Information --}}
    <div>
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3">Økonomi</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl border border-slate-200/80 bg-white">

            <div>
                <label class="block text-[11px] font-semibold text-slate-500">Hovedstol</label>
                <p class="mt-1 font-bold text-slate-900">{{ $this->formatNumber($sag->hovedstol) }} kr.</p>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500">Renter</label>
                <p class="mt-1 font-bold text-slate-900">{{ $this->formatNumber($sag->renter) }} kr.</p>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500">Gebyr</label>
                <p class="mt-1 font-bold text-slate-900">{{ $this->formatNumber($sag->gebyr) }} kr.</p>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-500">I alt</label>
                <p class="mt-1 font-bold text-indigo-600">{{ $this->formatNumber($sag->ialt) }} kr.</p>
            </div>

        </div>
    </div>

    {{-- Dokument Upload --}}
    <div class="border-t pt-6">

        <h3 class="text-lg font-semibold mb-4">
            Upload dokument
        </h3>

        <form
        action="{{ route('sager.dokumenter.store', $sag) }}"
        method="POST"
        enctype="multipart/form-data"
        class="flex items-center gap-4">

            @csrf

            <input type="file" name="file" required class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">

            <button
            type="submit"
            class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-500 transition cursor-pointer">

                Upload

            </button>

        </form>

    </div>

</div>