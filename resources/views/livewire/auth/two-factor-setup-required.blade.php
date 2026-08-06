<div class="min-h-screen flex items-center justify-center bg-gray-100">

    <div class="bg-white rounded-xl shadow p-8 w-full max-w-lg">

        <h1 class="text-2xl font-bold mb-4">
            Two-Factor Authentication Required
        </h1>

        <p class="mb-6 text-gray-600">
            Your role requires Two-Factor Authentication before you can continue.
        </p>

        <div class="border rounded p-4 mb-6">
            {!! $user->twoFactorQrCodeSvg() !!}
        </div>

        <form wire:submit="confirm">

            <label class="block mb-2 font-medium">
                Verification Code
            </label>

            <input
                type="text"
                wire:model="code"
                class="w-full border rounded px-3 py-2"
                placeholder="123456"
            >

            @error('code')
                <div class="text-red-600 mt-2">
                    {{ $message }}
                </div>
            @enderror

            <button
                type="submit"
                class="mt-4 w-full bg-blue-600 text-white py-3 rounded"
            >
                Activate Two-Factor Authentication
            </button>

        </form>

    </div>

</div>