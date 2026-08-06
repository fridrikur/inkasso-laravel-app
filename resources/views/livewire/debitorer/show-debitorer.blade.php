<div class="space-y-8">

    {{-- ========================================= --}}
    {{-- DEBITORER MED SAGER --}}
    {{-- ========================================= --}}
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Debitorer med sager
        </h2>

        <div class="overflow-hidden bg-white shadow rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Debitor</th>
                        <th class="px-4 py-3 text-left">Sager</th>
                        <th class="px-4 py-3 text-center">Handlinger</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach($debitorer as $debitor)
                        <tr class="odd:bg-white even:bg-slate-50 hover:bg-blue-50 transition">
                            <td class="px-4 py-3 font-mono">
                                #{{ $debitor->id }}
                            </td>

                            <td class="px-4 py-3">
                                <a
                                    href="{{ route('debitorer.edit', $debitor) }}"
                                    class="font-medium text-blue-600 hover:text-blue-800"
                                >
                                    {{ $debitor->navn }}
                                </a>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($debitor->sager as $sag)
                                        <a
                                            href="{{ route('sager.edit', $sag) }}"
                                            class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-sm hover:bg-slate-200"
                                        >
                                            Sag #{{ $sag->id }}
                                        </a>
                                    @endforeach
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button
                                    disabled
                                    class="px-3 py-1 rounded bg-gray-200 text-gray-500 cursor-not-allowed"
                                >
                                    Kan ikke slettes
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- FORÆLDRELØSE DEBITORER --}}
    {{-- ========================================= --}}
    <div>
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
            Forældreløse debitorer
        </h2>

        <div class="overflow-hidden bg-white shadow rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-emerald-700 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Debitor</th>
                        <th class="px-4 py-3 text-center">Handlinger</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach($orphans as $debitor)
                        <tr class="odd:bg-white even:bg-slate-50 hover:bg-emerald-50 transition">
                            <td class="px-4 py-3 font-mono">
                                #{{ $debitor->id }}
                            </td>

                            <td class="px-4 py-3">
                                <a
                                    href="{{ route('debitorer.edit', $debitor) }}"
                                    class="font-medium text-blue-600 hover:text-blue-800"
                                >
                                    {{ $debitor->navn }}
                                </a>
                            </td>

                            <td class="px-4 py-3 text-center">
                                <button
                                    wire:click="deleteDebitor({{ $debitor->id }})"
                                    wire:confirm="Er du sikker på at du vil slette debitoren?"
                                    class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700"
                                >
                                    Slet
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>