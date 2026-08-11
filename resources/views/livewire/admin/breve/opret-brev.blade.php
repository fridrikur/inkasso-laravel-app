<div class="space-y-6">

    <h2>Jeg er ikke overbevist</h2>

    {{-- TOP BAR --}}
    <div class="border-b flex flex-wrap gap-2 pb-3">

        @foreach($breveList as $brev)

            <div class="flex items-center gap-1">

                <button
                    wire:click="loadBrev({{ $brev['id'] }})"
                    class="px-4 py-2 rounded-t-lg text-sm transition
                        {{ $brevId === $brev['id']
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                        }}"
                >
                    {{ $brev['titel'] }}
                </button>

                <button
                    wire:click="deleteBrev({{ $brev['id'] }})"
                    onclick="confirm('Slet dette brev?') || event.stopImmediatePropagation()"
                    class="text-red-500 text-xs"
                >
                    ✕
                </button>

            </div>

        @endforeach

        <div class="flex items-center gap-2 ml-auto">

            <input
                type="text"
                wire:model="newBrevTitle"
                placeholder="Ny skabelon"
                class="border rounded px-3 py-2 text-sm"
            >

            <button
                wire:click="createNewBrev"
                class="bg-green-600 text-white px-4 py-2 rounded"
            >
                Opret
            </button>

        </div>

    </div>

    {{-- TOOLBAR --}}
    <div class="flex gap-2 items-center">

        <button
            wire:click="generatePreview"
            class="bg-blue-600 text-white px-3 py-1 rounded text-sm"
        >
            🔄 Opdater preview
        </button>

        <button
            wire:click="loadRandomSag"
            class="bg-purple-600 text-white px-3 py-1 rounded text-sm"
        >
            🎲 Random sag
        </button>

        <button
            wire:click="saveTemplate"
            class="bg-gray-800 text-white px-3 py-1 rounded text-sm ml-auto"
        >
            💾 Gem brev
        </button>

    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- META --}}
        <div class="bg-white border rounded-2xl p-4 space-y-4">

            <h2 class="font-semibold">Brev info</h2>

            <input wire:model="titel" class="w-full border rounded px-3 py-2 text-sm" placeholder="Titel">
            <input wire:model="emne" class="w-full border rounded px-3 py-2 text-sm" placeholder="Emne">

        </div>

        {{-- EDITOR --}}
        <div class="bg-white border rounded-2xl p-4">

            <h2 class="font-semibold mb-3">Skabelon</h2>

            <textarea
                wire:model="tekst"
                class="w-full border rounded px-3 py-2 min-h-[600px] font-mono text-sm"
            ></textarea>

        </div>

        {{-- PREVIEW --}}
        <div class="bg-white border rounded-2xl p-4">

            <div class="flex justify-between mb-3">
                <h2 class="font-semibold">Preview</h2>

                <button
                    wire:click="togglePreview"
                    class="text-xs bg-gray-200 px-2 py-1 rounded"
                >
                    Fullscreen
                </button>
            </div>

            <div class="prose border rounded p-4 min-h-[600px] bg-gray-50">
                {!! $previewHtml ?: '<span class="text-gray-400">Klik “Opdater preview”</span>' !!}
            </div>

        </div>

    </div>

    {{-- MODAL --}}
    @if($previewExpanded)
        <div class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center">

            <div class="bg-white w-[95vw] h-[95vh] rounded-xl p-6 overflow-auto relative">

                <button
                    wire:click="togglePreview"
                    class="absolute top-3 right-3 bg-gray-200 px-3 py-1 rounded"
                >
                    ✕ Close
                </button>

                <div class="prose">
                    {!! $previewHtml !!}
                </div>

            </div>

        </div>
    @endif

</div>