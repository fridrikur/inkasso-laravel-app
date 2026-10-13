<div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow space-y-6">

    <h2 class="text-2xl font-bold">
        Sag: {{ $sag->sagsnr }} 
    </h2>

    {{-- Tabs --}}
    <div class="flex space-x-6 border-b pb-2">

        {{-- Omdøbt til Dialog med DKG (eller tilrettet i komponenten) --}}
        @include('components.sag-tabs.klientinformation-tab', [
            'sag' => $sag,
            'klientinformationUnread' => $klientinformationUnread,
            'label' => 'Dialog med DKG'
        ])

        {{-- Dokumenter omdøbt til DKG dokumenter --}}
        <a href="{{ route('sager.dokumenter.index', $sag->id) }}"
        class="{{ request()->routeIs('sager.dokumenter.*')
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
        whitespace-nowrap pb-2 border-b-2 font-medium text-sm flex items-center gap-2">

            <span>DKG dokumenter</span>

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
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">c/o</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $debitor->co ?? '-' }}</p>
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
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Telefon</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $debitor->tlf ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Stelnummer</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $sag->stelnr ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">KTR</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $sag->ktr->first()?->tekst ?? $sag->ktr->first()?->tekst ?? '-' }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Udlæg</label>
            <p class="mt-1 font-semibold text-slate-800">{{ $sag->udlaeg->first()?->tekst ?? $sag->udlaeg->first()?->udlaeg ?? '-' }}</p>
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
</div>