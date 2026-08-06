<div class="bg-white rounded-2xl shadow-sm overflow-hidden">

    <table class="w-full">

        <thead class="bg-slate-50">

            <tr>

                <th class="text-left px-6 py-4">
                    Tekst
                </th>

                <th class="text-left px-6 py-4">
                    Oprettet
                </th>

                <th class="text-right px-6 py-4">
                    Handling
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($autotekster as $autotekst)

                <tr class="border-t odd:bg-white even:bg-slate-50 hover:bg-indigo-50">

                    <td class="px-6 py-4">
                        {{ $autotekst->tekst }}
                    </td>

                    <td class="px-6 py-4">
                        {{ optional($autotekst->dato)->format('d-m-Y') }}
                    </td>

                    <td class="px-6 py-4 text-right">

                        <button
                            wire:click="editAutotekst({{ $autotekst->id }})"
                            class="text-indigo-600 hover:text-indigo-800"
                        >
                            Rediger
                        </button>

                        <button
                            wire:click="confirmDelete('autotekst', {{ $autotekst->id }})"
                            class="ml-4 text-red-600 hover:text-red-800"
                        >
                            Slet
                        </button>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="3" class="text-center py-10 text-slate-500">
                        Ingen autotekster fundet
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>