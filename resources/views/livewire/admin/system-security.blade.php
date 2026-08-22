<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow p-6">

        <h1 class="text-2xl font-bold mb-6">
            System Sikkerhed
        </h1>

        {{-- Success besked hvis den gemmes --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-emerald-100 text-emerald-800 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-5">

            <div>
                <label class="block text-sm font-medium mb-2">
                    Global låsekode
                </label>

                <input
                    type="password"
                    wire:model="unlock_code"
                    class="w-full border rounded-xl px-4 py-3 @error('unlock_code') border-rose-500 @enderror"
                >
                @error('unlock_code') 
                    <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    Bekræft låsekoden
                </label>

                <input
                    type="password"
                    wire:model="unlock_code_confirmation"
                    class="w-full border rounded-xl px-4 py-3"
                >
            </div>

            <button
                wire:click="save"
                class="px-6 py-3 bg-black text-white rounded-xl hover:bg-slate-800 transition"
            >
                Gem sikkerhedskoden
            </button>

        </div>

    </div>

</div>