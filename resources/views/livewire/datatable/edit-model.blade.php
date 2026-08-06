<!-- resources/views/liveWire/datatable/edit-model.blade.php -->

<form wire:submit.prevent="update">
    @foreach($columns as $column)
        <div>
            <label for="{{ $column }}">{{ ucfirst($column) }}</label>
            <input type="text" id="{{ $column }}" wire:model="model.{{ $column }}">
        </div>
    @endforeach
    <button type="submit">Update</button>
</form>