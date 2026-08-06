@if($showModal)

<div class="fixed inset-0 z-50">

    <div
        class="absolute inset-0 bg-black/50 backdrop-blur-md"
        wire:click="$set('showModal', false)"
    ></div>

    <div class="relative z-10 flex items-center justify-center min-h-screen p-4">

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl p-8">

            <h2 class="text-2xl font-bold mb-6">

                {{ $editingId ? 'Rediger' : 'Opret' }}

                {{ $type === 'status'
                    ? 'Status'
                    : 'Autotekst'
                }}

            </h2>

            <div class="bg-white">

                <div>

                    <label class="block mb-2 font-medium">
                        Tekst
                    </label>

                    <input
                        type="text"
                        wire:model.defer="form.tekst"
                        class="w-full rounded-xl border-slate-300"
                    >

                </div>

                @if($type === 'status')

                    <div>

                        <label class="block mb-2 font-medium">
                            Forkortelse
                        </label>

                        <input
                            type="text"
                            wire:model.defer="form.forkortelse"
                            class="w-full rounded-xl border-slate-300"
                        >

                    </div>

                @endif

                @if($type === 'autotekst')

                    <div>

                        <label class="block mb-2 font-medium">
                            Dato
                        </label>

                        <input
                            type="date"
                            wire:model.defer="form.dato"
                            class="w-full rounded-xl border-slate-300"
                        >

                    </div>

                @endif

            </div>

            <div class="flex justify-end gap-3 mt-8">

                <button
                    wire:click="$set('showModal', false)"
                    class="px-4 py-2 border rounded-xl"
                >
                    Annuller
                </button>

                <button
                    wire:click="save"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-xl"
                >
                    Gem
                </button>

            </div>

        </div>

    </div>

</div>

@endif