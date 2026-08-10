<x-layouts.app title="Importering slut">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 space-y-10">

            {{-- Success icon --}}
            <div class="flex justify-center">
                <div class="h-14 w-14 rounded-full bg-green-100 flex items-center justify-center text-2xl">
                    ✅
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center space-y-1">
                <h2 class="text-2xl font-semibold text-gray-900">
                    Import gennemført
                </h2>
                <p class="text-sm text-gray-500">
                    Resultatet af filimporten vises nedenfor
                </p>
            </div>

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-xl mx-auto">
                <div class="rounded-xl border border-green-200 bg-green-50 p-6 text-center">
                    <div class="text-3xl font-bold text-green-700">
                        {{ $inserted ?? $session->inserted ?? 0 }}
                    </div>
                    <div class="mt-1 text-sm text-green-700">
                        Rækker indsat
                    </div>
                </div>

                <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-center">
                    <div class="text-3xl font-bold text-red-700">
                        {{ $session->failed ?? count($failedRows ?? []) }}
                    </div>
                    <div class="mt-1 text-sm text-red-700">
                        Rækker fejlede
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-wrap justify-center items-center gap-4">
                
                {{-- Knappen er her igen og åbner nu den rigtige detaljeside! --}}
                @if(isset($session))
                    <a href="{{ route('sager.import.session', $session) }}"
                       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-6 py-3 rounded-lg shadow text-sm transition">
                        📊 Se import detaljer
                    </a>
                @endif

                @php
                    $kreditorObj = $kreditor ?? $session->kreditor ?? null;
                @endphp

                @if($kreditorObj)
                    <a href="{{ route('sager.import.form', $kreditorObj) }}"
                       class="inline-flex items-center gap-2 rounded-lg bg-slate-800 px-6 py-3 text-sm font-medium text-white hover:bg-slate-900 transition text-sm">
                        📂 Importér ny fil
                    </a>
                @endif

                <a href="{{ route('sager.import.log') }}"
                   class="inline-flex items-center gap-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium px-6 py-3 rounded-lg text-sm transition">
                    📋 Gå til Importlog
                </a>

            </div>

        </div>
    </div>
</x-layouts.app>