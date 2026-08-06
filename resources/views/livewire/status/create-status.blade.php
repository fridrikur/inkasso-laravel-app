<div style="max-width:500px">
    <h1>Opret ny status</h1>
    <form wire:submit="save">
        <div class="columns-2">
            <label for="tekst">Tekst</label><br>
            <input type="text" wire:model="form.tekst">
            @error('form.tekst') <span class="error">{{ $message }}</span> @enderror
            <label for="Forkortelse">Forkortelse</label><br>
            <input type="text" wire:model="form.forkortelse">
            @error('form.forkortelse') <span class="error">{{ $message }}</span> @enderror
        </div>
        <div><button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Gem</button>
        </div>
    </form>
</div>
