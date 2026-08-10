<div class="space-y-4">
    <h2 class="text-xl font-bold mb-2 text-slate-800">Bogholderi</h2>

    {{-- Formular til oprettelse af bogholderinote --}}
    <form wire:submit.prevent="save" class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
        
        {{-- DÆMPET KONSULENT-VÆLGER --}}
        <div class="opacity-50 pointer-events-none">
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-slate-500">Vælg konsulent/afsender</label>
                <span class="text-[10px] font-semibold text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded">Deaktiveret</span>
            </div>
            <select 
                wire:model="konsulent_id" 
                disabled
                class="w-full text-xs rounded-xl border border-slate-200 bg-slate-100 p-2.5 text-slate-400 cursor-not-allowed outline-none"
            >
                <option value="">-- Vælg konsulent (Deaktiveret) --</option>
                @foreach($konsulenter as $konsulent)
                    <option value="{{ $konsulent->id }}">{{ $konsulent->navn }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Bogholderi note</label>
            <textarea 
                wire:model.defer="tekst"
                class="w-full p-2.5 border border-slate-200 rounded-xl text-xs focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white"
                rows="3"
                placeholder="Skriv bogholderinote..."
            ></textarea>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-xs text-slate-400">
                Gemmes som: <strong>{{ auth()->user()->name }}</strong>
            </span>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs transition shadow-sm cursor-pointer">
                Gem note
            </button>
        </div>
    </form>

    {{-- BESKEDER / BOGHOLDERILOG --}}
    <div class="space-y-2 mt-6">
        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Registrerede noter</h3>

        @forelse($dialogMessages as $message)
            <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-sm mb-3">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold text-slate-800">
                        👤 {{ $message->sender?->name ?? $message->sender?->navn ?? 'System' }}
                    </span>

                    <div class="flex items-center gap-3">
                        <span class="text-[10px] text-slate-400">
                            {{ \Carbon\Carbon::parse($message->dato)->format('d-m-Y H:i') }}
                        </span>

                        {{-- REDIGER KNAP --}}
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
                    {{-- ALMINDELIG VISNING AF NOTEN --}}
                    <p class="text-xs text-slate-700 whitespace-pre-wrap leading-relaxed">{{ $message->tekst }}</p>
                @endif
            </div>
        @empty
            <div class="p-6 text-center text-slate-400 text-xs italic bg-slate-50 rounded-2xl border border-slate-100">
                Ingen bogholderinoter oprettet endnu.
            </div>
        @endforelse
    </div>
</div>