<!-- resources/views/liveWire/afslutning/update-afslutning.blade.php -->
<div>
    <h1>Opdater afslutning</h1>
    <form wire:submit="save">
        <label>Tekst</label>
        <input type="text" wire:model="form.tekst">
        @error('form.tekst') <span class="error">{{ $message }}</span> @enderror
        <label>Forkortelse</label>
        <input type="text" wire:model="form.forkortelse">
        @error('form.forkortelse') <span class="error">{{ $message }}</span> @enderror
        <button type="submit">Gem</button>
    </form>
</div>