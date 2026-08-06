<div>
    <h1>Opret ny sagervalgliste</h1>
    <form wire:submit="save">
        <div class="flex flex-nowrap gap-x-px">
            <div>
                <div><label for="navn">Navn</label></div>
                <input type="text" wire:model.blur="form.navn" placeholder="navn">
                @error('form.navn') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <div><label for="forkortelse">Forkortelse</label></div>
                <input type="text" wire:model.blur="form.forkortelse" placeholder="forkortelse">
                @error('form.forkortelse') <span class="error">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">Save</button>
        </div>
    </form>
</div>
