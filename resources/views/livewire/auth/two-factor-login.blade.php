<div class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white shadow rounded-xl p-6">

        <h1 class="text-xl font-semibold mb-4">
            Two-Factor Authentication
        </h1>

        <p class="text-gray-600 mb-4">
            Enter the code from your authenticator app.
        </p>

        <form wire:submit="verify">

            <input
                type="text"
                wire:model="code"
                class="w-full border rounded p-3"
                placeholder="123456"
            >

            @error('code')
                <p class="text-red-500 mt-2">
                    {{ $message }}
                </p>
            @enderror

            <button
                type="submit"
                class="mt-4 w-full bg-blue-600 text-white py-3 rounded"
            >
                Verify
            </button>

        </form>

    </div>

</div>