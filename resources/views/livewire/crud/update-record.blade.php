<div class="max-w-md mx-auto p-4">
    <form wire:submit.prevent="update" class="grid grid-cols-2 gap-4">
        @foreach($columns as $column)
            <div class="flex flex-col">
                <label for="{{ $column }}" class="text-sm font-medium">{{ ucfirst($column) }}</label>
                <input type="text" id="{{ $column }}" wire:model.blur="form.{{ $column }}" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
        @endforeach
        <div class="col-span-2 pt-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Opdatér</button>
        </div>
    </form>
    <div x-data @refresh-page.window="location.reload()"></div>
</div>