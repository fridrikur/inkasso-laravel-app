<x-global-modal title="Redigér" size="lg" wire:model="modalIsOpen">
    @if($selectedModel)
        <form wire:submit.prevent="update" class="grid grid-cols-2 gap-4">
            @foreach($displayColumns as $column)
                <div class="flex flex-col">
                    <label for="{{ $column }}" class="text-sm font-medium">{{ ucfirst($column) }}</label>
                    <input type="text" id="{{ $column }}" wire:model.blur="form.{{ $column }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
            @endforeach
            <div class="col-span-2 pt-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Opdatér</button>
            </div>
        </form>
    @endif
    <x-slot name="footer">
        <button wire:click="$set('modalIsOpen', false)" class="px-4 py-2 bg-gray-200 rounded">Luk</button>
    </x-slot>
</x-global-modal>