<div class="space-y-4">
    <h2 class="text-xl font-bold mb-2">Bogholderi</h2>

    {{-- Message form --}}
    <form wire:submit.prevent="save" class="space-y-2">
        <textarea wire:model.defer="tekst"
                  class="w-full p-2 border rounded"
                  rows="3"
                  placeholder="Skriv en besked..."></textarea>

                  <select wire:model="konsulent_id"
                        class="w-full p-2 border rounded mb-2">

                    <option value="">Vælg konsulent</option>

                    @foreach($konsulenter as $konsulent)
                        <option value="{{ $konsulent->id }}">
                            {{ $konsulent->navn }}
                        </option>
                    @endforeach
                </select>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
            Send
        </button>
    </form>

    {{-- Messages list --}}
    <div class="space-y-2 mt-4">
        @forelse($dialogMessages as $message)
    <div class="p-4 rounded-2xl border mb-3">
        <p class="text-sm text-slate-700">{{ $message->tekst }}</p>
    </div>
@empty
    <div class="p-4 text-center text-slate-400 text-xs">
        Ingen beskeder endnu.
    </div>
@endforelse
    </div>
</div>