<div class="space-y-4">
    <h2 class="text-xl font-bold mb-2">Klientinformation</h2>

    {{-- Formular --}}
    <form wire:submit.prevent="save" class="space-y-2">
        <textarea 
            wire:model.defer="tekst"
            class="w-full p-2 border rounded"
            rows="3"
            placeholder="Skriv en besked til klienten/kreditor..."
        ></textarea>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded font-bold text-xs">
            Send besked
        </button>
    </form>

    {{-- Beskeder --}}
    <div class="space-y-2 mt-4">
        @forelse($dialogMessages as $message)
            <div class="p-4 rounded-2xl border mb-3 {{ $message->sender_id === auth()->id() ? 'bg-indigo-50 border-indigo-100 ml-6' : 'bg-white border-slate-200 mr-6' }}">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-bold text-slate-800">
                        {{ $message->sender?->name ?? 'Bruger' }}
                    </span>
                    <span class="text-[10px] text-slate-400">
                        {{ \Carbon\Carbon::parse($message->dato)->format('d-m-Y H:i') }}
                    </span>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $message->tekst }}</p>
            </div>
        @empty
            <div class="p-4 text-center text-slate-400 text-xs">
                Ingen beskeder endnu. Skriv en besked ovenfor for at starte dialogen.
            </div>
        @endforelse
    </div>
</div>