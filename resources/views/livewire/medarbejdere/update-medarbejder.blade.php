<div>
    <h1 class="font-bold text-xl mb-4">{{ $message }}</h1>

    <!-- FORM -->
    <form wire:submit="save">
        <label>Navn</label>
        <input type="text" wire:model.blur="form.navn" class="border p-2 w-full">

        @error('form.navn') 
            <span class="text-red-500">{{ $message }}</span> 
        @enderror

        <button type="submit" class="mt-3 px-4 py-2 bg-blue-500 text-white rounded">
            Gem
        </button>
    </form>

    <!-- USER SECTION -->
    <div class="mt-6">
        <h2 class="font-semibold mb-2">Brugeradgang</h2>

        @if($users->count())
            @foreach($users as $user)
                <div class="flex items-center gap-2 mb-2">
                    <flux:badge size="sm" icon="user-circle"></flux:badge>

                    <a href="{{ route('updateuser', $user->id) }}">
                        {{ $user->name }}
                    </a>
                </div>
            @endforeach
        @else
            <p class="text-gray-500">Ingen medarbejder-brugere endnu</p>
        @endif

        <!-- Create user -->
        <div class="mt-3">
            <button 
                wire:click="createUserForMedarbejder"
                class="text-blue-600 underline"
            >
                Tildel brugeradgang
            </button>
        </div>
    </div>
    <div 
        x-data="{ show: false, message: '' }"
        x-on:toast.window="
            message = $event.detail.message;
            show = true;
            setTimeout(() => show = false, 3000);
        "
        x-show="show"
        x-transition
        class="fixed bottom-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg"
        style="display:none;"
    >
        <span x-text="message"></span>
    </div>
</div>