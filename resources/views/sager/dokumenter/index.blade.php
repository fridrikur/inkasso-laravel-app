<x-layouts.app title="Dokumenter - Sag {{ $sag->sagsnr }}">

<div class="max-w-5xl mx-auto">

    <h1 class="text-2xl font-bold mb-6">
        Dokumenter - Sag {{ $sag->sagsnr }}
    </h1>

    {{-- Rettet så det matcher controllerens tilladelse for upload --}}
    @role('Admin|Medarbejder|Kreditor')
    <form action="{{ route('sager.dokumenter.store', $sag) }}"
          method="POST"
          enctype="multipart/form-data"
          class="mb-6 bg-white p-6 rounded-xl shadow">
        @csrf

        <input type="file" name="file" required class="text-sm">
        <button type="submit"
                class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold">
            Upload
        </button>
    </form>
    @endrole

    <div class="bg-white rounded-xl shadow divide-y">
        @forelse($dokumenter as $dok)
            <div class="p-4 flex justify-between items-center">
                <div>
                    <div class="font-semibold text-slate-800">{{ $dok->file_name }}</div>
                    <div class="text-xs text-slate-500">
                        {{ number_format($dok->file_size / 1024, 2) }} KB
                        – {{ $dok->uploaded_date->format('d-m-Y H:i') }}
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Download link (enten via asset() eller via din download route) --}}
                    <a href="{{ route('sager.dokumenter.download', [$sag, $dok]) }}"
                    class="text-blue-600 hover:underline text-sm font-semibold">
                        Download
                    </a>

                    {{-- Slet-knap (Kun for Admin & Medarbejder) --}}
                    @role('Admin|Medarbejder')
                    <form action="{{ route('sager.dokumenter.destroy', [$sag, $dok]) }}" method="POST" onsubmit="return confirm('Er du sikker på du vil slette dette dokument?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-rose-600 hover:text-rose-800 text-sm font-semibold">
                            Slet
                        </button>
                    </form>
                    @endrole
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-slate-400 text-sm">
                Ingen dokumenter fundet på denne sag endnu.
            </div>
        @endforelse
    </div>

</div>
</x-layouts.app>