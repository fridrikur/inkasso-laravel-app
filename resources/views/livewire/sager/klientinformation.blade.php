<div class="space-y-4">
    <div class="flex items-center justify-between mb-2">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Klientinformation</h2>
            <p class="text-xs font-medium text-slate-500 mt-0.5">
                Sagsnr: <span class="font-bold text-indigo-600">#{{ $sag->sagsnr }}</span> 
                @if(isset($sagName) && $sagName)
                    <span class="mx-1.5">•</span> Navn: <span class="font-bold text-slate-700">{{ $sagName }}</span>
                @endif
            </p>
        </div>

        <button 
            type="button" 
            @click="$wire.set('showMeddebitorModal', true)"
            class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold text-sm rounded-xl transition flex items-center gap-1.5 cursor-pointer"
        >
            <span>➕ Tilføj Meddebitor / Ubetalte Måneder</span>
        </button>
    </div>

    <form wire:submit.prevent="save" class="space-y-2">
        <textarea 
            wire:model.defer="tekst"
            class="w-full p-3.5 border border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            rows="3"
            placeholder="Skriv en besked til klienten/kreditor..."
        ></textarea>

        <div class="flex justify-between items-center">
            <span class="text-xs text-slate-400">
                Sendes som: <strong>{{ auth()->user()->name }}</strong>
            </span>

            <button type="submit" class="px-3.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition cursor-pointer">
                Send besked
            </button>
        </div>
    </form>

    {{-- BESKEDER / BOBLE-LISTE --}}
    <div class="space-y-3 mt-4">
        @forelse($dialogMessages as $message)
            @php
                $isOwn = $message->sender_id === auth()->id();
                $senderName = $message->sender?->name ?? 'System';
            @endphp

            <div class="p-4 rounded-2xl border mb-3 transition-all {{ $isOwn ? 'bg-indigo-50/80 border-indigo-100 ml-6' : 'bg-white border-slate-200 mr-6 shadow-sm' }}">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-sm font-bold {{ $isOwn ? 'text-indigo-900' : 'text-slate-800' }}">
                        👤 {{ $senderName }}
                    </span>

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400">
                            {{ \Carbon\Carbon::parse($message->dato)->format('d-m-Y H:i') }}
                        </span>

                        {{-- REDIGER & SLET KNAPPER --}}
                        <div class="flex items-center gap-2">
                            @if($editingMessageId !== $message->id)
                                <button 
                                    type="button" 
                                    wire:click="editMessage({{ $message->id }})"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer"
                                >
                                    Rediger
                                </button>
                            @endif

                            <button 
                                type="button" 
                                wire:click="confirmDeleteMessage({{ $message->id }})"
                                class="text-xs text-rose-600 hover:text-rose-800 font-bold cursor-pointer"
                                title="Flyt til papirkurv"
                            >
                                🗑 Slet
                            </button>
                        </div>
                    </div>
                </div>

                @if($editingMessageId === $message->id)
                    <div class="space-y-2 mt-2">
                        <textarea 
                            wire:model="editingText" 
                            rows="3" 
                            class="w-full rounded-xl border border-indigo-300 bg-white p-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                        ></textarea>

                        <div class="flex justify-end gap-2">
                            <button 
                                type="button" 
                                wire:click="cancelEdit" 
                                class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-sm rounded-lg transition cursor-pointer"
                            >
                                Annuller
                            </button>
                            <button 
                                type="button" 
                                wire:click="updateMessage" 
                                class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow-sm transition cursor-pointer"
                            >
                                Gem ændringer
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed">{!! $message->tekst !!}</p>
                @endif
            </div>
        @empty
            <div class="p-6 text-center text-slate-400 text-sm italic bg-slate-50 rounded-2xl border border-slate-100">
                Ingen beskeder endnu. Skriv en besked ovenfor for at starte dialogen.
            </div>
        @endforelse
    </div>

    {{-- MODAL TIL TILFØJELSE AF MEDDEBITOR OG UBETALTE MÅNEDER --}}
    @if($showMeddebitorModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md border border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-900 text-base">Tilføj oplysning om Meddebitor & Ubetalte Måneder</h3>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Meddebitor Navn</label>
                    <input 
                        type="text" 
                        wire:model="meddebitorNavn" 
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500" 
                        placeholder="F.eks. Jens Jensen"
                    />
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Ubetalte Måneder</label>
                    <input 
                        type="text" 
                        wire:model="ubetalteMaaneder" 
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-indigo-500" 
                        placeholder="F.eks. Jan, Feb, Marts 2026 (3 måneder)"
                    />
                </div>

                <div class="space-y-2 pt-2">
                    {{-- Generel fejlbesked ved knapperne --}}
                    @if ($errors->any())
                        <div class="text-red-500 text-xs font-medium text-right">
                            Udfyld venligst alle felter før oprettelse.
                        </div>
                    @endif

                    <div class="flex justify-end gap-2">
                        <button 
                            type="button" 
                            wire:click="$set('showMeddebitorModal', false)" 
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition cursor-pointer"
                        >
                            Annuller
                        </button>
                        <button 
                            type="button" 
                            wire:click="addMeddebitorBubble" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition shadow-sm cursor-pointer"
                        >
                            Opret Boble
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL TIL SLETNING AF BESKED --}}
    @if($showDeleteMessageModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
                <button 
                    type="button" 
                    wire:click="$set('showDeleteMessageModal', false)" 
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition cursor-pointer text-lg"
                >
                    &times;
                </button>

                <div class="flex items-center gap-3">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Flyt besked til papirkurv?
                        </h3>
                        <p class="text-sm text-slate-500">
                            Beskeden fjernes, men kan gendannes, hvis du fortryder.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteMessageModal', false)" 
                        class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer"
                    >
                        Annuller
                    </button>
                    <button 
                        type="button" 
                        wire:click="executeDeleteMessage" 
                        class="px-4 py-2 text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer"
                    >
                        Flyt til papirkurv
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PAPIRKURV TIL SLETTEDE NOTER / DIALOGER --}}
    @if($this->trashMessages->isNotEmpty())
        <div class="mt-8 pt-6 border-t border-slate-200/80">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🗑 Papirkurv for slettede noter</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs">
                        {{ $this->trashMessages->count() }}
                    </span>
                </h3>
            </div>

            <div class="space-y-2">
                @foreach($this->trashMessages as $trashMsg)
                    <div class="p-3.5 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-bold text-slate-600 line-through">
                                    👤 {{ $trashMsg->sender?->name ?? 'System' }}
                                </span>
                                <span class="text-xs text-rose-600 font-medium">
                                    Slettet: {{ \Carbon\Carbon::parse($trashMsg->deleted_at)->format('d-m-Y H:i') }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 italic line-clamp-2">
                                "{{ $trashMsg->tekst }}"
                            </p>
                        </div>

                        <button 
                            type="button" 
                            wire:click="restoreMessage({{ $trashMsg->id }})"
                            class="px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-sm rounded-xl transition flex items-center gap-1.5 shrink-0 cursor-pointer"
                            title="Gendan note"
                        >
                            <span>♻️ Gendan</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>