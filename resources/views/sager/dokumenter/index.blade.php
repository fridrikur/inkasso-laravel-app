<x-layouts.app title="Importering slut">

<div class="max-w-5xl mx-auto">

    <h1 class="text-2xl font-bold mb-6">
        Dokumenter - Sag {{ $sag->sagsnr }}
    </h1>

    @role('Admin|Medarbejder')
    <form action="{{ route('sager.dokumenter.store', $sag) }}"
          method="POST"
          enctype="multipart/form-data"
          class="mb-6 bg-white p-6 rounded-xl shadow">
        @csrf

        <input type="file" name="file" required>
        <button type="submit"
                class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg">
            Upload
        </button>
    </form>
    @endrole

    <div class="bg-white rounded-xl shadow divide-y">
        @foreach($dokumenter as $dok)
            <div class="p-4 flex justify-between items-center">
                <div>
                    <div class="font-semibold">{{ $dok->file_name }}</div>
                    <div class="text-sm text-gray-500">
                        {{ number_format($dok->file_size / 1024, 2) }} KB
                        – {{ $dok->uploaded_date->format('d-m-Y H:i') }}
                    </div>
                </div>

                <a href="{{ asset('storage/'.$dok->file_path) }}"
                   target="_blank"
                   class="text-blue-600 hover:underline">
                    Download
                </a>
            </div>
        @endforeach
    </div>

</div>
</x-layouts.app>