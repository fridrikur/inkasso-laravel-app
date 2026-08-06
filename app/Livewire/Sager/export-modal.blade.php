@if($showExportModal)

<div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white rounded-2xl shadow-xl w-[700px]">

        <div class="flex justify-between items-center p-6 border-b">

            <h2 class="text-xl font-semibold">

                Excel eksport

            </h2>

            <button
                wire:click="closeExportModal">

                ✕

            </button>

        </div>

        <div class="p-6">

            <p class="text-sm text-gray-500 mb-5">

                Vælg kolonner der skal eksporteres.

            </p>

            <div class="grid grid-cols-2 gap-3">

                @foreach($availableColumns as $field => $label)

                    <label class="flex items-center gap-3">

                        <input
                            type="checkbox"
                            wire:model="selectedColumns"
                            value="{{ $field }}">

                        {{ $label }}

                    </label>

                @endforeach

            </div>

        </div>

        <div
            class="flex justify-end gap-3 border-t p-6">

            <button
                wire:click="closeExportModal"
                class="px-4 py-2 rounded-lg bg-gray-100">

                Annuller

            </button>

            <button
                wire:click="exportExcel"
                class="px-4 py-2 rounded-lg bg-green-600 text-white">

                Exportér

            </button>

        </div>

    </div>

</div>

@endif