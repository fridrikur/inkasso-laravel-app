<x-layouts.app title="Importer sager">
    <div class="max-w-3xl mx-auto p-8">
    <div class="bg-white rounded-xl shadow p-8 space-y-6">
        <h1 class="text-2xl font-semibold">Importer sager fra: {{ $kreditor->navn }}</h1>

        @if(!empty($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $error }}
            </div>
        @endif

        <form
            action="{{ route('sager.import.upload', $kreditor) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf

            <input type="hidden" name="lotusID" value="{{ request('lotusID') }}">

            <input
                type="file"
                name="file"
                required
                class="block w-full border rounded p-2"
            >

            <button
                type="submit"
                class="bg-indigo-600 text-white px-6 py-3 rounded-lg"
            >
                Forhåndsvis
            </button>
        </form>
    </div>
</div>
</x-layouts.app>