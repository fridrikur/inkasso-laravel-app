{{-- SLIDE-OVER DRAWER TIL SAGSDETALJER --}}
<div 
    x-data="{ open: @entangle('showDrawer'), activeTab: 'detaljer' }"
    x-show="open"
    x-cloak
    class="relative z-50"
>
    {{-- Baggrundsblur (Backdrop) --}}
    <div 
        x-transition:enter="ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity z-40"
        @click="$wire.closeSag()"
    ></div>

    <div class="fixed inset-0 overflow-hidden z-50 pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                
                {{-- Selve skuffen der glider ind fra højre --}}
                <div 
                    x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-3xl"
                >
                    <div class="flex h-full flex-col bg-white shadow-2xl border-l border-slate-200">
                        
                        {{-- Top bar / Header --}}
                        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 bg-indigo-600 text-white rounded-lg text-xs font-mono font-bold">
                                    @if($activeSag) #{{ $activeSag->sagsnr }} @else Indlæser... @endif
                                </span>
                                <h2 class="text-sm font-bold">Sagsdetaljer</h2>
                            </div>

                            <button 
                                type="button" 
                                wire:click="closeSag"
                                @click="open = false"
                                class="p-1.5 text-slate-400 hover:text-white rounded-xl hover:bg-slate-800 transition cursor-pointer"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- FANENAVIGATION --}}
                        <div class="px-6 pt-4 bg-white border-b border-slate-200 flex items-center gap-2">
                            <button
                                type="button"
                                @click="activeTab = 'detaljer'"
                                :class="activeTab === 'detaljer' ? 'border-indigo-600 text-indigo-600 font-bold bg-indigo-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                                class="px-4 py-2.5 text-xs border-b-2 font-semibold transition cursor-pointer rounded-t-xl"
                            >
                                📋 Sagsinformation
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'klientinfo'"
                                :class="activeTab === 'klientinfo' ? 'border-indigo-600 text-indigo-600 font-bold bg-indigo-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                                class="px-4 py-2.5 text-xs border-b-2 font-semibold transition cursor-pointer rounded-t-xl"
                            >
                                💬 Dialog med DKG
                            </button>

                            <button
                                type="button"
                                @click="activeTab = 'dokumenter'"
                                :class="activeTab === 'dokumenter' ? 'border-indigo-600 text-indigo-600 font-bold bg-indigo-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                                class="px-4 py-2.5 text-xs border-b-2 font-semibold transition cursor-pointer rounded-t-xl flex items-center gap-2"
                            >
                                <span>📁 DKG dokumenter</span>
                            </button>
                        </div>

      {{-- Indhold i skuffen --}}
<div class="flex-1 overflow-y-auto p-6 space-y-6" wire:key="drawer-content-{{ $activeSag?->id ?? 'empty' }}">
    @if($activeSag)
        @php
            $debitor = $activeSag->debitor->first();
        @endphp

        {{-- FANE 1: SAGSINFORMATION --}}
        <div x-show="activeTab === 'detaljer'" class="space-y-6">
            {{-- Overskrift med fuld bredde --}}
            <div class="mb-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">Sags- og debitorinformation</h3>
                <p class="text-xs text-slate-500">Overblik over sagens grundlæggende stamdata.</p>
            </div>
            
            {{-- Grid container til alle felterne --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                {{-- Aktiv --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aktiv</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
                        {{ $activeSag->aktiv ?? '-' }}
                    </div>
                </div>

                {{-- c/o --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">c/o</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
                        {{ $debitor->co ?? '-' }}
                    </div>
                </div>

                {{-- Debitor navn --}}
                <div class="space-y-1 md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Debitor navn</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
                        {{ $debitor->navn ?? '-' }}
                    </div>
                </div>

               {{-- Adresse --}}
                <div class="space-y-1 md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Adresse</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
                        <div>{{ $debitor->adresse ?? '-' }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            {{ $debitor->postnr ?? '' }} {{ $debitor->postnummer?->by ?? '' }}
                        </div>
                    </div>
                </div>

                {{-- Telefon --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Telefon</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
                        {{ $debitor->tlf ?? '-' }}
                    </div>
                </div>

                {{-- Stelnummer --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Stelnummer</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none font-mono">
                        {{ $activeSag->stelnr ?? '-' }}
                    </div>
                </div>

                {{-- KTR --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">KTR</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
                        {{ $activeSag->ktr->first()?->tekst ?? '-' }}
                    </div>
                </div>

                {{-- Udlæg --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Udlæg</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none">
                        {{ $activeSag->udlaeg->first()?->tekst ?? '-' }}
                    </div>
                </div>

                {{-- Sagsbehandler --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sagsbehandler</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-3 text-slate-800 select-none space-y-1">
                        @if($activeSag->first_sagsbehandler)
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span>👤</span>
                                <span>{{ $activeSag->first_sagsbehandler->navn }}</span>
                            </div>
                            <div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 border-t border-slate-200/60 pt-1.5">
                                @if($activeSag->first_sagsbehandler->email)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400">✉️</span>
                                        <a href="mailto:{{ $activeSag->first_sagsbehandler->email }}" class="text-indigo-600 hover:underline">
                                            {{ $activeSag->first_sagsbehandler->email }}
                                        </a>
                                    </div>
                                @endif
                                @if($activeSag->first_sagsbehandler->tlf)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400">📞</span>
                                        <a href="tel:{{ $activeSag->first_sagsbehandler->tlf }}" class="hover:underline">
                                            {{ $activeSag->first_sagsbehandler->tlf }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </div>
                </div>

                {{-- DKG sagsbehandler --}}
                <div class="space-y-1">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">DKG sagsbehandler</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-3 text-slate-800 select-none space-y-1">
                        @if($activeSag->first_konsulent)
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span>👤</span>
                                <span>{{ $activeSag->first_konsulent->navn }}</span>
                            </div>
                            <div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 border-t border-slate-200/60 pt-1.5">
                                @if($activeSag->first_konsulent->email)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400">✉️</span>
                                        <a href="mailto:{{ $activeSag->first_konsulent->email }}" class="text-indigo-600 hover:underline">
                                            {{ $activeSag->first_konsulent->email }}
                                        </a>
                                    </div>
                                @endif
                                @if($activeSag->first_konsulent->tlf)
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-slate-400">📞</span>
                                        <a href="tel:{{ $activeSag->first_konsulent->tlf }}" class="hover:underline">
                                            {{ $activeSag->first_konsulent->tlf }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </div>
                </div>

                {{-- Modtaget dato --}}
                <div class="space-y-1 md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Modtaget dato</label>
                    <div class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-slate-800 font-medium select-none font-mono">
                        {{ optional($activeSag->modtaget)->format('d-m-Y H:i') ?? '-' }}
                    </div>
                </div>

            </div>
        </div>

        {{-- FANE 2: KLIENTINFORMATION & DIALOG --}}
        <div x-show="activeTab === 'klientinfo'" class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm space-y-4">
                @livewire('sager.klientinformation', ['sag' => $activeSag], key('klientinfo-comp-' . $activeSag->id))
            </div>
        </div>

        {{-- FANE 3: DKG DOKUMENTER --}}
        <div x-show="activeTab === 'dokumenter'" class="space-y-6">
            {{-- Upload Form --}}
            <form wire:submit.prevent="uploadDokument" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center gap-4">
                    <input type="file" wire:model="newDokument" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Upload Dokument
                    </button>
                </div>
                @error('newDokument') <span class="text-xs text-rose-500 mt-2 block">{{ $message }}</span> @enderror
            </form>

            {{-- Liste over dokumenter --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100">
                @forelse($activeSag->dokumenter ?? [] as $dokument)
                    <div class="p-4 flex justify-between items-center" wire:key="dok-{{ $dokument->id }}">
                        <div>
                            <div class="font-bold text-slate-800 text-xs">{{ $dokument->file_name ?? $dokument->navn ?? 'Dokument' }}</div>
                            <div class="text-[11px] text-slate-400">
                                {{ isset($dokument->file_size) ? number_format($dokument->file_size / 1024, 2) . ' KB' : '' }} 
                                @if(isset($dokument->created_at)) – {{ $dokument->created_at->format('d-m-Y H:i') }} @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('sager.dokumenter.download', [$activeSag->id, $dokument->id]) }}" class="text-indigo-600 hover:underline text-xs font-bold">
                                Download
                            </a>

                            <button type="button" wire:click="deleteDokument({{ $dokument->id }})" class="text-rose-500 hover:text-rose-700 text-xs font-bold cursor-pointer">
                                Slet
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400 text-xs italic">
                        Ingen dokumenter fundet på denne sag endnu.
                    </div>
                @endforelse
            </div>
        </div>

    @else
        <div class="flex items-center justify-center h-64 text-slate-400 text-xs italic">
            Henter sagsoplysninger...
        </div>
    @endif
</div>
                    </div></div>
            </div>
        </div>
    </div>
</div>