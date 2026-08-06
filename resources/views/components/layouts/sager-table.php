<div class="overflow-x-auto shadow-md sm:rounded-lg">

    <table class="w-full text-sm text-left text-gray-500">

        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">Sagsnr</th>
                <th class="px-6 py-3">Debitor</th>
                <th class="px-6 py-3">Kreditor</th>
                <th class="px-6 py-3">Handling</th>
            </tr>
        </thead>

        <tbody>
            @foreach($sagers as $sag)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $sag->id }}</td>
                    <td class="px-6 py-4">{{ $sag->sagsnr }}</td>
                    <td class="px-6 py-4">{{ $sag->debitor_navn }}</td>
                    <td class="px-6 py-4">{{ $sag->kreditor_navn }}</td>

                    <td class="px-6 py-4">
                        <a href="{{ route('sager.edit', $sag) }}" class="text-green-600">
                            Redigér
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>

    <div class="mt-4">
        {{ $sagers->links() }}
    </div>

</div>