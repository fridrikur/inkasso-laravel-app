<div class="max-w-lg mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Opret ny bruger for: {{ $kreditornavn }}
    </h1>

    <form wire:submit.prevent="save" class="space-y-6">

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Navn</label>
            <input type="text" wire:model.defer="form.name" placeholder="John Doe"
                   class="block w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            @error('form.name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" wire:model.defer="form.email" placeholder="john@example.com"
                   class="block w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            @error('form.email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" wire:model.defer="form.password" placeholder="••••••••"
                   class="block w-full border border-gray-300 rounded-md p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            @error('form.password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

       
        <!-- Role (hidden, always Kreditor) -->
        <input type="hidden" wire:model.defer="form.role">



        <!-- Kreditor (readonly) -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Virksomhed</label>
            <input type="text" value="{{ $kreditornavn }}" readonly
                   class="block w-full border border-gray-300 rounded-md p-3 bg-gray-100 cursor-not-allowed">
        </div>

        <!-- Submit -->
        <div>
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-md shadow-md transition duration-150">
                Opret bruger
            </button>
        </div>

    </form>

    @if(session()->has('message'))
        <div class="mt-4 text-green-600 font-medium">{{ session('message') }}</div>
    @endif
</div>
