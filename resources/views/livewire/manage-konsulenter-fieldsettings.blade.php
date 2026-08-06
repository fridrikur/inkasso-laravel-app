<div><div class="grid grid-cols-2 gap-4">
    @foreach($settings as $key => $field)
        <div class="p-3 border rounded bg-white">
            <div class="font-medium">{{ $field['field_name'] }}</div>

            <input type="text"
                   wire:model.defer="settings.{{ $key }}.alias"
                   class="border rounded px-2 py-1 w-full mt-2 mb-2"
                   placeholder="Alias (label)" />

            <div class="flex gap-4 items-center text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model.defer="settings.{{ $key }}.visible" />
                    Visible
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model.defer="settings.{{ $key }}.required" />
                    Required
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model.defer="settings.{{ $key }}.readonly" />
                    Readonly
                </label>
            </div>
        </div>
    @endforeach
</div>

<button wire:click="save" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
    Save
</button>
</div>