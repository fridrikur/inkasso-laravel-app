<div class="max-w-4xl mx-auto bg-white p-6 rounded-2xl shadow space-y-6">

    <h2 class="text-2xl font-bold">
        Sag: #{{ $sag->sagsnr }} 
    </h2>

    {{-- Tabs --}}
    <div class="flex space-x-6 border-b pb-2">
        @include('components.sag-tabs.klientinformation-tab', [
            'sag' => $sag,
            'klientinformationUnread' => $klientinformationUnread,
            'label' => 'Dialog med DKG'
        ])

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

    {{-- Inkluder fælles stamdata/detaljer --}}
    <div class="bg-slate-50/50 p-5 rounded-2xl border border-slate-100">
        @include('livewire.sager.partials.sag-details', ['currentSag' => $sag])
    </div>
</div>