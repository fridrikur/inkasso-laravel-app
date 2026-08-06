{{-- ========================= --}}
{{-- QUICK MENU OVERLAY --}}
{{-- ========================= --}}
@if($showQuickMenu)

    <div class="fixed inset-0 z-50 flex items-center justify-center">

        {{-- BACKDROP --}}
        <div
            class="absolute inset-0 bg-black/50 backdrop-blur-md"
            wire:click="closeQuickMenu"
        ></div>

        {{-- TERMINAL WINDOW --}}
        <div class="relative z-10 w-[460px] bg-black text-green-400 font-mono border border-green-600 rounded-lg shadow-2xl overflow-hidden">

            {{-- HEADER --}}
            <div class="flex items-center justify-between px-4 py-2 border-b border-green-700 text-xs">

                <span>
                    ADMIN TERMINAL // QUICK ACTIONS
                </span>

                <button
                    wire:click="closeQuickMenu"
                    class="text-red-400 hover:text-red-300"
                >
                    ESC
                </button>

            </div>

            {{-- MENU ITEMS --}}
            {{-- MENU ITEMS --}}
<div class="p-4 space-y-2 text-sm">

    {{-- ========================= --}}
    {{-- MAIN MENU --}}
    {{-- ========================= --}}
    @if($quickMenuScreen === 'main')

        <button
            wire:click="goToCreateKreditor"
            class="w-full text-left hover:bg-green-900 px-3 py-2 rounded transition"
        >
            1. Opret kreditor
        </button>

        <button
            wire:click="goToCreateBrev"
            class="w-full text-left hover:bg-green-900 px-3 py-2 rounded transition"
        >
            2. Opret Brev
        </button>

        <button
            wire:click="openImportSagerMenu"
            class="w-full text-left hover:bg-green-900 px-3 py-2 rounded transition"
        >
            3. Importer sager →
        </button>

        <button
            wire:click="goToFindSag"
            class="w-full text-left hover:bg-green-900 px-3 py-2 rounded transition"
        >
            4. Find sag
        </button>

        <button
            wire:click="goToCreateUser"
            class="w-full text-left hover:bg-green-900 px-3 py-2 rounded transition"
        >
            5. Opret bruger
        </button>

    @endif

    {{-- ========================= --}}
    {{-- IMPORT SAGER SUBMENU --}}
    {{-- ========================= --}}
    @if($quickMenuScreen === 'import-sager')

        <div class="text-green-500 text-xs mb-2 border-b border-green-800 pb-2">
            Vælg kreditor (lotusID)
        </div>

        <button
            wire:click="$set('quickMenuScreen', 'main')"
            class="w-full text-left hover:bg-green-900 px-3 py-2 rounded transition text-yellow-400"
        >
            ← Tilbage
        </button>

        <div class="max-h-72 overflow-y-auto space-y-1 mt-2">

            @foreach($kreditors as $kreditor)

                <button
                    wire:click="goToImportSager('{{ $kreditor->lotusID }}')"
                    class="w-full text-left hover:bg-green-900 px-3 py-2 rounded transition"
                >
                    {{ $kreditor->navn }}
                    <span class="text-green-700 text-xs">
                        [{{ $kreditor->lotusID }}]
                    </span>
                </button>

            @endforeach

        </div>

    @endif

</div>

            {{-- FOOTER --}}
            <div class="px-4 py-2 border-t border-green-700 text-xs text-green-600">

                ENTER • ESC • CTRL+SPACE

            </div>

        </div>

    </div>

@endif