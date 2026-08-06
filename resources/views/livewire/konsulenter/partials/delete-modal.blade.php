<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">

        <h2 class="text-lg font-bold">
            Slet konsulent?
        </h2>

        <p class="mt-2 text-slate-600">
            Denne handling kan ikke fortrydes.
        </p>

        @error('delete')
            <div class="mt-4 rounded-lg bg-red-50 p-3 text-red-700">
                {{ $message }}
            </div>
        @enderror

        <div class="mt-6 flex justify-end gap-3">

            <button
                wire:click="$set('confirmingDelete', false)"
                class="rounded-lg border px-4 py-2"
            >
                Annuller
            </button>

            <button
                wire:click="delete"
                class="rounded-lg bg-red-600 px-4 py-2 text-white"
            >
                Slet
            </button>

        </div>

    </div>

</div>
