<div class="space-y-4">
    <div class="flex items-center justify-between mb-2">
        <h2 class="text-xl font-bold text-slate-800">Klientinformation</h2>

        {{-- KNAP TIL AT INDSÆTTE MEDDEBITOR / UBETALTE MÅNEDER BOBLE --}}
        <button 
            type="button" 
            @click="$wire.set('showMeddebitorModal', true)"
            class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5 cursor-pointer"
        >
            <span>➕ Tilføj Meddebitor / Ubetalte Måneder</span>
        </button>
    </div>

    {{-- FORMULAR TIL ALMINDELIG BESKED --}}
    <form wire:submit.prevent="save" class="space-y-2">
        <textarea 
            wire:model.defer="tekst"
            class="w-full p-2.5 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            rows="3"
            placeholder="Skriv en besked til klienten/kreditor..."
        ></textarea>

        <div class="flex justify-between items-center">
            <span class="text-xs text-slate-400">
                Sendes som: <strong>{{ auth()->user()->name }}</strong>
            </span>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs transition shadow-sm cursor-pointer">
                Send besked
            </button>
        </div>
    </form>

    {{-- BESKEDER / BOBLE-LISTE --}}
    <div class="space-y-2 mt-4">
        @forelse($dialogMessages as $message)
            @php
                $isOwn = $message->sender_id === auth()->id();
                // Finder navnet på den indloggede bruger eller afsenderen
                $senderName = $message->sender?->name ?? 'System';
            @endphp

            <div class="p-4 rounded-2xl border mb-3 transition-all {{ $isOwn ? 'bg-indigo-50/80 border-indigo-100 ml-6' : 'bg-white border-slate-200 mr-6 shadow-sm' }}">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold {{ $isOwn ? 'text-indigo-900' : 'text-slate-800' }}">
                        👤 {{ $senderName }}
                    </span>

                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-slate-400">
                            {{ \Carbon\Carbon::parse($message->dato)->format('d-m-Y H:i') }}
                        </span>

                        @if($editingMessageId !== $message->id)
                            <button 
                                type="button" 
                                wire:click="editMessage({{ $message->id }})"
                                class="text-[11px] text-indigo-600 hover:text-indigo-800 font-bold cursor-pointer"
                            >
                                Rediger
                            </button>
                        @endif
                    </div>
                </div>

                {{-- HVIS BESKEDEN ER UNDER REDIGERING --}}
                @if($editingMessageId === $message->id)
                    <div class="space-y-2 mt-2">
                        <textarea 
                            wire:model="editingText" 
                            rows="3" 
                            class="w-full rounded-xl border border-indigo-300 bg-white p-2.5 text-xs outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                        ></textarea>

                        <div class="flex justify-end gap-2">
                            <button 
                                type="button" 
                                wire:click="cancelEdit" 
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition cursor-pointer"
                            >
                                Annuller
                            </button>
                            <button 
                                type="button" 
                                wire:click="updateMessage" 
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition cursor-pointer"
                            >
                                Gem ændringer
                            </button>
                        </div>
                    </div>
                @else
                    {{-- ALMINDELIG VISNING AF BOBLEN --}}
                    <p class="text-xs text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $message->tekst }}</p>
                @endif
            </div>
        @empty
            <div class="p-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-2xl border border-slate-100">
                Ingen beskeder endnu. Skriv en besked ovenfor for at starte dialogen.
            </div>
        @endforelse
    </div>

    {{-- MODAL TIL TILFØJELSE AF MEDDEBITOR OG UBETALTE MÅNEDER --}}
    @if($showMeddebitorModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md border border-slate-100 space-y-4">
                <h3 class="font-bold text-slate-900 text-sm">Tilføj oplysning om Meddebitor & Ubetalte Måneder</h3>
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Meddebitor Navn</label>
                    <input 
                        type="text" 
                        wire:model="meddebitorNavn" 
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500" 
                        placeholder="F.eks. Jens Jensen"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Ubetalte Måneder</label>
                    <input 
                        type="text" 
                        wire:model="ubetalteMaaneder" 
                        class="w-full border border-slate-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-indigo-500" 
                        placeholder="F.eks. Jan, Feb, Marts 2026 (3 måneder)"
                    />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button 
                        type="button" 
                        wire:click="$set('showMeddebitorModal', false)" 
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
                    >
                        Annuller
                    </button>
                    <button 
                        type="button" 
                        wire:click="addMeddebitorBubble" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-sm"
                    >
                        Opret Boble
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>