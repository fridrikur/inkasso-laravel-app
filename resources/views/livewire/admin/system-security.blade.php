<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow p-6">

        <h1 class="text-2xl font-bold mb-6">
            System Security
        </h1>

        <div class="space-y-5">

            <div>
                <label class="block text-sm font-medium mb-2">
                    Global unlock code
                </label>

                <input
                    type="password"
                    wire:model="unlock_code"
                    class="w-full border rounded-xl px-4 py-3"
                >
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    Confirm unlock code
                </label>

                <input
                    type="password"
                    wire:model="unlock_code_confirmation"
                    class="w-full border rounded-xl px-4 py-3"
                >
            </div>

            <button
                wire:click="save"
                class="px-6 py-3 bg-black text-white rounded-xl"
            >
                Save Security Code
            </button>

        </div>

    </div>

</div>