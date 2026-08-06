<div class="space-y-4">
    <h2 class="text-xl font-bold mb-2">Historik & Interne Noter</h2>

    {{-- Formular --}}
    <form wire:submit.prevent="save" class="space-y-2">
        <textarea 
            wire:model.defer="tekst"
            class="w-full p-2 border rounded"
            rows="3"
            placeholder="Skriv internt notat..."
        ></textarea>

        <select wire:model="konsulent_id" class="w-full p-2 border rounded mb-2">
            <option value="">Vælg konsulent</option>
            @foreach($konsulenter as $konsulent)
                <option value="{{ $konsulent->id }}">
                    {{ $konsulent->navn }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-bold text-xs">
            Gem notat
        </button>
    </form>

    {{-- Beskeder / Noter --}}
    <div class="space-y-2 mt-4">
        @forelse($dialogMessages as $message)
            <div class="p-4 rounded-2xl border mb-3 bg-slate-50 border-slate-200">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-bold text-slate-800">
                        {{ $message->sender?->name ?? 'Konsulent' }}
                    </span>
                    <span class="text-[10px] text-slate-400">
                        {{ \Carbon\Carbon::parse($message->dato)->format('d-m-Y H:i') }}
                    </span>
                </div>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $message->tekst }}</p>
            </div>
        @empty
            <div class="p-4 text-center text-slate-400 text-xs">
                Ingen historik eller noter oprettet endnu.
            </div>
        @endforelse
    </div>
</div>