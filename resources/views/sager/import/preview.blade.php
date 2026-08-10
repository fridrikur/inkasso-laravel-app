<x-layouts.app title="Forhåndsvisning">
    <div class="max-w-7xl mx-auto px-6 py-10 space-y-8">
        
        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
            <strong>Bemærk:</strong>
            Importen kan tage op til flere minutter afhængigt af filens størrelse.
            Luk ikke denne side.
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Import – Preview
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Gennemse data før import
                </p>
            </div>

            <a href="{{ route('sager.import.form', ['kreditor' => $kreditor])}}"
               class="text-sm text-indigo-600 hover:underline">
                ← Vælg anden fil
            </a>
        </div>

        {{-- 🟢 SUMMARY CARDS MED TOTAL ANTAL SAGER --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            {{-- KORT 1: SAMLET ANTAL SAGER I CSV --}}
            <div class="bg-white rounded-xl shadow p-5 border border-slate-100">
                <div class="text-sm text-gray-500 font-medium">Sager i CSV-fil</div>
                <div class="text-3xl font-bold text-indigo-600 mt-1">
                    {{ number_format($totalRows, 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-400 mt-1">
                    Viser {{ count($rows) }} rækker i forhåndsvisning
                </div>
            </div>

            {{-- KORT 2: DUBLETTER --}}
            <div class="bg-white rounded-xl shadow p-5 border border-slate-100">
                <div class="text-sm text-gray-500 font-medium">Dubletter fundet</div>
                <div class="text-3xl font-bold mt-1 {{ $duplicateCount > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                    {{ number_format($duplicateCount, 0, ',', '.') }}
                </div>
                <div class="text-xs text-gray-400 mt-1">
                    Eksisterende i systemet
                </div>
            </div>

            {{-- KORT 3: HANDLING --}}
            <div class="bg-white rounded-xl shadow p-5 border border-slate-100">
                <div class="text-sm text-gray-500 font-medium">Handling ved dubletter</div>
                <div class="text-xs text-gray-600 mt-2">
                    Vælg herunder hvordan dubletter skal håndteres ved indførsel.
                </div>
            </div>

        </div>

        {{-- Duplicate warning --}}
        @if($duplicateCount > 0)
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    ⚠️ Der er fundet <strong>{{ $duplicateCount }}</strong> dublet(ter) i filen. Vælg hvordan de skal håndteres før import.
                </p>
            </div>
        @endif

        {{-- Preview table --}}
        <div class="mt-6 rounded-xl border bg-white shadow-sm">
            <div class="border-b px-6 py-4 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">
                    Forhåndsvisning af data
                </h3>
                <span class="text-xs text-gray-500">
                    Viser første {{ count($rows) }} ud af {{ $totalRows }} rækker
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            @foreach($headers as $header)
                                <th class="px-4 py-3 text-left font-medium">
                                    {{ $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($rows as $row)
                            <tr class="hover:bg-gray-50">
                                @foreach($row as $cell)
                                    <td class="px-4 py-2 text-gray-700">
                                        {{ $cell }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Import form --}}
        <form action="{{ route('sager.import.execute', $kreditor) }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <input type="hidden" name="file_path" value="{{ $path }}">

            <div class="bg-white rounded-xl border p-5 space-y-3">
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-700">Håndtering af dubletter:</h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="duplicate_action" value="keep" checked class="text-indigo-600">
                        <span>Bevar begge (Opret ny sag selvom sagsnummer findes)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="duplicate_action" value="replace" class="text-indigo-600">
                        <span>Overskriv (Opdater eksisterende sag med nye data)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="duplicate_action" value="skip" class="text-indigo-600">
                        <span>Ignorer (Spring dubletter over)</span>
                    </label>
                </div>
            </div>

            <button
                type="submit"
                class="mt-6 inline-flex items-center justify-center gap-3 rounded-xl bg-indigo-600 px-6 py-3 text-white font-bold text-sm hover:bg-indigo-700 transition disabled:opacity-60 cursor-pointer shadow-sm"
                :disabled="loading"
            >
                <svg
                    x-show="loading"
                    class="h-5 w-5 animate-spin text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>

                <span x-text="loading ? 'Importerer {{ $totalRows }} sager…' : 'Importér {{ $totalRows }} sager nu'"></span>
            </button>
        </form>

    </div>
</x-layouts.app>