<div class="bg-white rounded-2xl shadow-sm overflow-hidden">

    <table class="w-full">

        <thead class="bg-slate-50">

            <tr>
                <th class="text-left px-6 py-4">
                    Tekst
                </th>

                <th class="text-left px-6 py-4">
                    Forkortelse
                </th>

                <th class="text-right px-6 py-4">
                    Handling
                </th>
            </tr>

        </thead>

        <tbody>

            @forelse($statuses as $status)

                <tr class="border-t odd:bg-white even:bg-slate-50 hover:bg-indigo-50">

                    <td class="px-6 py-4">
                        {{ $status->tekst }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-slate-100 rounded-lg text-sm">
                            {{ $status->forkortelse }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-right">

                        <button
                            wire:click="editStatus({{ $status->id }})"
                            class="text-indigo-600 hover:text-indigo-800"
                        >
                            Rediger
                        </button>

                        <button
                            wire:click="confirmDelete('status', {{ $status->id }})"
                            class="ml-4 text-red-600 hover:text-red-800"
                        >
                            Slet
                        </button>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="3" class="text-center py-10 text-slate-500">
                        Ingen statustekster fundet
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

</div>