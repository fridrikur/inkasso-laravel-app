<!-- resources/views/liveWire/ktr/create-ktr.blade.php -->
<div>
    <h1>Opret ny KTR</h1>
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

