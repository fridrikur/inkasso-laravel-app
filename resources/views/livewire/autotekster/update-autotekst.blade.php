<div>
    <form wire:submit="save">
        <div>
            <div>
                <label for="tekst">Tekst</label><br>
                <input type="text" wire:model.blur="form.tekst">
                @error('form.tekst') <span class="error">{{ $message }}</span> @enderror
                <input type="datetime-local" wire:model.blur="form.dato" placeholder="dato" value="now();">
                @error('form.dato') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
            <div><button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Gem</button>
        </div>
</form></div>
</div>