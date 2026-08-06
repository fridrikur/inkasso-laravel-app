<div>
    <h2 class="text-lg font-semibold">
    {{ $user?->exists ? 'Rediger bruger' : 'Opret bruger' }}
</h2>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Navn</label>
            <input type="text" wire:model.defer="name" class="mt-1 block w-full border rounded px-2 py-1"/>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" wire:model.defer="email" class="mt-1 block w-full border rounded px-2 py-1"/>
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" wire:model.defer="password" class="mt-1 block w-full border rounded px-2 py-1"/>
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Roller</label>
            <select multiple wire:model="roles" class="mt-1 block w-full border rounded px-2 py-1">
                @foreach($allRoles as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
            @error('roles') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        @if(in_array('Kreditor', $roles))
        <div>
            <label class="block text-sm font-medium text-gray-700">Kreditor</label>
            <select wire:model="selectedKreditor" class="mt-1 block w-full border rounded px-2 py-1">
                <option value="">Vælg Kreditor</option>
                @foreach($allKreditors as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            @error('selectedKreditor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        @endif

        <div class="flex justify-end gap-2">
            <button type="button" wire:click="$dispatch('userSaved')" class="px-4 py-2 bg-gray-300 rounded">Luk</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Gem</button>
        </div>
    </form>
</div>
