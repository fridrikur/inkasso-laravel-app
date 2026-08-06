<div>dfsefsdfsdf{{$showModal}}
    @if($showModal)
    <div style="border:1px solid red">
        @foreach($this->getFormProperty()->all() as $field => $value)
            <div>
                <label for="{{ $field }}">{{ ucfirst($field) }}</label>
                <input type="text" wire:model="form.{{ $field }}" id="{{ $field }}" value="{{ $value }}">
            </div>
        @endforeach
        <button wire:click="save">Save</button>
        <!-- Modal HTML here -->
        <button wire:click="closeModal">Close</button>
    </div>
@endif
</div>