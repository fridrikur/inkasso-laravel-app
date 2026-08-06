<x-layouts.app title="Importerings resultat">
    <div class="max-w-xl mx-auto p-8">
    <div class="bg-white shadow rounded-xl p-6 text-center space-y-4">
        <h2 class="text-2xl font-semibold">Import færdig</h2>

        <div class="text-green-600 font-medium">
            Indsat: {{ $inserted }}
        </div>

        <div class="text-red-500">
            Fejlede: {{ $failed }}
        </div>

        <a href="{{ route('sager.import.form') }}"
           class="inline-block mt-4 text-indigo-600 underline">
            Importér ny fil
        </a>
    </div>
</div>
</x-layouts.app>