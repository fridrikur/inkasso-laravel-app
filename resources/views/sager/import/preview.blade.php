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
π      
      </p>
        </div>

        <a href="{{ route('sager.import.form', ['kreditor' => $kreditor])}}"
           class="text-sm text-indigo-600 hover:underline">
            ← Vælg anden fil
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-sm text-gray-500">Viste rækker</div>
            <div class="text-2xl font-semibold text-gray-900">
                {{ count($rows) }}
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-sm text-gray-500">Dubletter fundet</div>
            <div class="text-2xl font-semibold {{ $duplicateCount > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                {{ $duplicateCount }}
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <div class="text-sm text-gray-500">Handling</div>
            <div class="text-sm text-gray-700 mt-1">
                Vælg hvordan dubletter håndteres
            </div>
        </div>
    </div>

    {{-- Duplicate warning --}}
    @if($duplicateCount > 0)
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                ⚠️ Der er fundet dubletter i filen. Vælg hvordan de skal håndteres før import.
            </p>
        </div>
    @endif

    {{-- Preview table --}}
    <div class="mt-6 rounded-xl border bg-white shadow-sm">
        <div class="border-b px-6 py-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">
                Forhåndsvisning
            </h3>
            <span class="text-xs text-gray-500">
                Viser første 10 rækker
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
    <form action="{{ route('sager.import.execute', $kreditor) }}" method="POST" x-data="{ loading: false }"
        @submit="loading = true">

        @csrf

        <input type="hidden" name="file_path" value="{{ $path }}">

        <div class="space-y-2">
            <label class="flex items-center gap-2">
                <input type="radio" name="duplicate_action" value="keep" checked>
                Bevar begge
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="duplicate_action" value="replace">
                Overskriv
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" name="duplicate_action" value="skip">
                Ignorer
            </label>
        </div>

        <button
            type="submit"
            class="mt-6 inline-flex items-center justify-center gap-3 rounded-lg bg-indigo-600 px-6 py-3 text-white font-medium hover:bg-indigo-700 disabled:opacity-60"
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
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v8z"/>
            </svg>

            <span x-text="loading ? 'Importerer…' : 'Importér nu'"></span>
        </button>
    </form>

</div>
</x-layouts.app>
<script>
    const form = document.querySelector('form');
    const btn = document.getElementById('import-btn');
    const text = document.getElementById('import-text');
    const spinner = document.getElementById('import-spinner');

    form.addEventListener('submit', () => {
        btn.disabled = true;
        text.textContent = 'Importerer…';
        spinner.classList.remove('hidden');
    });
</script>
