<div class="min-h-screen bg-slate-100">

    <div class="max-w-4xl mx-auto py-10 px-6">

        {{-- HERO --}}
        <div class="mb-8">

            <div class="flex items-center gap-4">

                <div class="h-14 w-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow">
                    +
                </div>

                <div>
                    <h1 class="text-3xl font-bold text-slate-900">
                        Opret kreditor
                    </h1>

                    <p class="text-slate-500">
                        Registrér en ny kreditor i systemet
                    </p>
                </div>

            </div>

        </div>

        {{-- CARD --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">

            {{-- CARD HEADER --}}
            <div class="px-8 py-6 border-b bg-slate-50">

                <h2 class="font-semibold text-lg">
                    Stamdata
                </h2>

            </div>

            {{-- FORM --}}
            <div class="p-8">

                <form wire:submit="save" class="space-y-8">

                    <div class="grid grid-cols-2 gap-6">

                        {{-- NAVN --}}
                        <div>

                            <label class="block text-sm font-medium mb-2">
                                Kreditornavn
                            </label>

                            <input
                            type="text"
                            wire:model.blur="form.navn"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3"
                            placeholder="Indtast navn"
                        >

                        @error('form.navn')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        </div>

                        {{-- LOTUS --}}
                        <div>

                            <label class="block text-sm font-medium mb-2">
                                Lotus ID
                            </label>

                            <input
                                type="number"
                                wire:model.live="form.lotusID"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3"
                                placeholder="{{ $this->suggestedLotusId }}"
                            >

                            @error('form.lotusID')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            @if($form->lotusID)

                                @if($this->lotusIdExists)

                                    <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                        <span>●</span>
                                        Findes allerede
                                    </div>

                                @else

                                    <div class="mt-2 flex items-center gap-2 text-green-600 text-sm">
                                        <span>●</span>
                                        Ledigt ID
                                    </div>

                                @endif

                            @endif

                        </div>

                    </div>

                    {{-- LOTUS INFO PANEL --}}
                    <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5">

                        <div class="font-medium text-blue-900">
                            Lotus Information
                        </div>

                        <div class="text-sm text-blue-700 mt-1">
                            Næste foreslåede LotusID:
                            <strong>{{ $this->suggestedLotusId }}</strong>
                        </div>

                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex justify-end gap-3">

                        <a
                            href="{{ route('kreditorer.index') }}"
                            class="px-5 py-3 rounded-xl border"
                        >
                            Annullér
                        </a>

                        <button
                            type="submit"
                            class="px-6 py-3 rounded-xl bg-blue-600 text-white font-medium shadow hover:bg-blue-700"
                        >
                            Opret kreditor
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>