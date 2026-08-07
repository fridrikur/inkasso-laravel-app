<div class="space-y-6">

    {{-- HEADER BANNER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-xs font-bold uppercase tracking-wider">
                    Medarbejder Portalen
                </span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">
                Velkommen, {{ auth()->user()->name }}
            </h1>
            <p class="text-sm text-slate-500">
                Her er dit daglige overblik over sager, ubehandlede opgaver og klientbeskeder.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('medarbejder.sager.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Opret ny sag</span>
            </a>
            
            <a href="{{ route('medarbejder.sager.search') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-200 transition cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Søg i sager</span>
            </a>
        </div>
    </div>

    {{-- QUICK STATS KPI BRISKER --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        
        {{-- Ubehandlede sager --}}
        <div class="rounded-2xl border p-5 shadow-sm transition {{ $unreadSagerCount > 0 ? 'border-rose-200 bg-rose-50/50' : 'border-slate-200 bg-white' }}">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider {{ $unreadSagerCount > 0 ? 'text-rose-700' : 'text-slate-400' }}">
                    Ubehandlede Sager
                </p>
                <div class="p-2 rounded-xl {{ $unreadSagerCount > 0 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold {{ $unreadSagerCount > 0 ? 'text-rose-900' : 'text-slate-900' }}">
                {{ $unreadSagerCount }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Sager som afventer første behandling</p>
        </div>

        {{-- Ulæste beskeder --}}
        <div class="rounded-2xl border p-5 shadow-sm transition {{ count($sagerWithNewMessages) > 0 ? 'border-indigo-200 bg-indigo-50/50' : 'border-slate-200 bg-white' }}">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider {{ count($sagerWithNewMessages) > 0 ? 'text-indigo-700' : 'text-slate-400' }}">
                    Nye Klientbeskeder
                </p>
                <div class="p-2 rounded-xl {{ count($sagerWithNewMessages) > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold {{ count($sagerWithNewMessages) > 0 ? 'text-indigo-900' : 'text-slate-900' }}">
                {{ count($sagerWithNewMessages) }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Sager med ulæste henvendelser</p>
        </div>

        {{-- Aktive sager i systemet --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Aktive Sager
                </p>
                <div class="p-2 rounded-xl bg-slate-100 text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-slate-900">
                {{ $myActiveSagerCount }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Samlet antal åbne sager i systemet</p>
        </div>

    </div>

    {{-- HOVEDINDHOLD GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- VENSTRE KOLONNE: NYE BESKEDER & UBEHANDLEDE SAGER (2 Cols) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ✉️ NYE BESKEDER SEKTION --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Nye Klientbeskeder</h2>
                        <p class="text-xs text-slate-500">Sager med ulæste beskeder fra klienten</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs">
                        {{ count($sagerWithNewMessages) }} sager
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($sagerWithNewMessages as $sag)
                        @php
                            $debitor = $sag->sagerdebitor->first();
                            $kreditor = $sag->sagerkreditor->first();
                        @endphp

                        <a href="{{ route('medarbejder.sager.klientinformation', $sag->id) }}" 
                           class="flex items-center justify-between p-4 sm:px-6 hover:bg-slate-50/80 transition duration-150 group">
                            
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-slate-900 group-hover:text-indigo-600 transition">
                                        #{{ $sag->display_number ?? $sag->sagsnr ?? $sag->id }}
                                    </span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200/60">
                                        {{ $sag->unread_messages_count }} {{ $sag->unread_messages_count === 1 ? 'ny besked' : 'nye beskeder' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4 text-xs text-slate-500">
                                    <span>Debitor: <strong class="text-slate-700">{{ $debitor->navn ?? '-' }}</strong></span>
                                    <span>•</span>
                                    <span>Kreditor: <strong class="text-slate-700">{{ $kreditor->navn ?? '-' }}</strong></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-400 font-mono">
                                    {{ $sag->updated_at->diffForHumans() }}
                                </span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            Der er ingen ulæste klientbeskeder lige nu.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 🔴 UBEHANDLEDE SAGER SEKTION --}}
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Ubehandlede Sager</h2>
                        <p class="text-xs text-slate-500">Nye sager der endnu ikke er behandlet</p>
                    </div>
                    
                    <a href="{{ route('medarbejder.sager.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                        Se alle &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($unreadSager as $sag)
                        @php
                            $debitor = $sag->sagerdebitor->first();
                            $kreditor = $sag->sagerkreditor->first();
                        @endphp

                        <a href="{{ route('medarbejder.sager.edit', $sag->id) }}" 
                           class="flex items-center justify-between p-4 sm:px-6 hover:bg-slate-50/80 transition duration-150 group">
                            
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-slate-900 group-hover:text-indigo-600 transition">
                                        #{{ $sag->display_number ?? $sag->sagsnr ?? $sag->id }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200/60 uppercase">
                                        Ubehandlet
                                    </span>
                                </div>

                                <div class="flex items-center gap-4 text-xs text-slate-500">
                                    <span>Debitor: <strong class="text-slate-700">{{ $debitor->navn ?? '-' }}</strong></span>
                                    <span>•</span>
                                    <span>Kreditor: <strong class="text-slate-700">{{ $kreditor->navn ?? '-' }}</strong></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs text-slate-400 font-mono">
                                    {{ $sag->created_at->format('d/m H:i') }}
                                </span>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Ingen ubehandlede sager fundet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- HØJRE KOLONNE: SENESTE OPRETTEDE SAGER (1 Col) --}}
        <div class="space-y-6">

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden sticky top-6">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Seneste Sager</h2>
                        <p class="text-xs text-slate-500">Nyligt oprettede eller opdaterede sager</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($latestSager as $sag)
                        @php
                            $debitor = $sag->sagerdebitor->first();
                            $kreditor = $sag->sagerkreditor->first();
                            $sagsbehandler = $sag->sagersagsbehandler->first();
                        @endphp

                        <a href="{{ route('medarbejder.sager.edit', $sag->id) }}" 
                           class="block p-4 hover:bg-slate-50/80 transition duration-150 group">
                            
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-mono font-bold text-xs text-slate-900 group-hover:text-indigo-600 transition">
                                    #{{ $sag->display_number ?? $sag->sagsnr ?? $sag->id }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">
                                    {{ $sag->created_at->format('d/m H:i') }}
                                </span>
                            </div>

                            <div class="space-y-0.5 text-xs">
                                <div class="flex justify-between text-slate-600">
                                    <span class="text-slate-400">Debitor:</span>
                                    <span class="font-medium text-slate-800 truncate max-w-[130px]">{{ $debitor->navn ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span class="text-slate-400">Kreditor:</span>
                                    <span class="font-medium text-slate-800 truncate max-w-[130px]">{{ $kreditor->navn ?? '-' }}</span>
                                </div>
                                @if($sagsbehandler)
                                    <div class="flex justify-between text-slate-600 pt-1">
                                        <span class="text-slate-400">Sagsbehandler:</span>
                                        <span class="font-semibold text-indigo-600 truncate max-w-[130px]">{{ $sagsbehandler->navn }}</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            Ingen sager oprettet endnu.
                        </div>
                    @endforelse
                </div>

                <div class="p-4 bg-slate-50/50 border-t border-slate-100 text-center">
                    <a href="{{ route('medarbejder.sager.index') }}" 
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                        Gå til alle sager &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>