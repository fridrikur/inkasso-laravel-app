@if($showDeleteModal)

<div class="fixed inset-0 z-50">

    <div
        class="absolute inset-0 bg-black/50 backdrop-blur-md"
        wire:click="$set('showModal', false)"
    ></div>

    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl p-8">
            <h2 class="text-xl font-bold mb-4">
                Bekræft sletning
            </h2>

            <p class="text-slate-600 mb-6">
                Er du sikker på at du vil slette denne tekst?
            </p>

            <div class="flex justify-end gap-3">

                <button
                    wire:click="$set('showDeleteModal', false)"
                    class="px-4 py-2 border rounded-xl"
                >
                    Annuller
                </button>

                <button
                    wire:click="delete"
                    class="px-4 py-2 bg-red-600 text-white rounded-xl"
                >
                    Slet
                </button>

            </div>

        </div>

    </div>

</div>

@endif