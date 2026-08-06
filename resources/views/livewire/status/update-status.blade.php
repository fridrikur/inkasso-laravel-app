<div>
    <h1>Redigér status</h1>
    <form wire:submit="save">
        <div>
            <div>
                <label for="tekst">Tekst</label><br>
                <input type="text" wire:model.blur="form.tekst">
                @error('form.tekst') <span class="error">{{ $message }}</span> @enderror
                <label for="Forkortelse">Forkortelse</label><br>
                <input type="text" wire:model="form.forkortelse">
                @error('form.forkortelse') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
            <div><button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Gem</button>
        </div>
</form></div>
</div>