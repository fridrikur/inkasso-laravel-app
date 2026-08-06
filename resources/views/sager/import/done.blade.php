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
                    {{ $inserted }}
                </div>
                <div class="mt-1 text-sm text-green-700">
                    Rækker indsat
                </div>
            </div>

            <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-center">
                <div class="text-3xl font-bold text-red-700">
                    {{ count($failedRows ?? []) }}
                </div>
                <div class="mt-1 text-sm text-red-700">
                    Rækker fejlede
                </div>
            </div>
        </div>

        {{-- Failed rows table --}}
        @if(!empty($failedRows))
            <div class="rounded-xl border border-red-200 bg-red-50 p-6">
                <h3 class="font-semibold text-red-800 mb-4">
                    Fejlede rækker
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm bg-white rounded-lg overflow-hidden">
                        <thead class="bg-red-100 text-red-800 text-left">
                            <tr>
                                <th class="py-3 px-4 font-medium">Række</th>
                                <th class="py-3 px-4 font-medium">Kontraktnr</th>
                                <th class="py-3 px-4 font-medium">Årsag</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-100">
                            @foreach($failedRows as $fail)
                                <tr class="hover:bg-red-50">
                                    <td class="px-4 py-3">
                                        {{ $fail['row'] }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-gray-800">
                                        {{ $fail['sagsnr'] }}
                                    </td>
                                    <td class="px-4 py-3 text-red-700">
                                        {{ $fail['reason'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ route('sager.import.session', $session) }}"
            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700
                    text-white px-6 py-3 rounded-lg shadow">
                📊 Se import detaljer
            </a>
            <div class="flex justify-center">
            <a
                href="{{ route('sager.import.form',$kreditor) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700 transition"
            >
                Importér en ny fil
            </a>
        </div>
        
        </div>
    </div>
</div>
</x-layouts.app>