<x-layouts.app title="Importering slut">
    <div class="max-w-5xl mx-auto py-10 space-y-8">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">
                    Import Session #{{ $session->id }}
                </h1>
                <p class="text-sm text-gray-500">
                    Status:
                    <span class="font-medium
                        @if($session->status === 'completed') text-green-600
                        @elseif($session->status === 'failed') text-red-600
                        @elseif($session->status === 'rolled_back') text-gray-500
                        @endif
                    ">
                        {{ ucfirst(str_replace('_', ' ', $session->status)) }}
                    </span>
                </p>
            </div>

            @if($session->status === 'completed')
                <form method="POST"
                    action="{{ route('sager.import.session.rollback', $session) }}"
                    onsubmit="return confirm('Er du sikker på at du vil rulle importen tilbage?');">
                    @csrf
                    <button
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow">
                        Rul import tilbage
                    </button>
                </form>
            @endif
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ $session->inserted }}
                </div>
                <div class="text-sm text-gray-500">Indsat</div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $session->failed }}
                </div>
                <div class="text-sm text-gray-500">Fejlede</div>
            </div>

            <div class="bg-white rounded-xl shadow p-6 text-center">
                <div class="text-sm text-gray-500">Kreditor</div>
                <div class="font-medium">{{ $session->kreditor }}</div>
            </div>
        </div>

        <!-- Failed rows -->
        @if(!empty($failedRows))
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <h3 class="font-semibold text-red-700 mb-4">
                    Fejlede rækker ({{ count($failedRows) }})
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-red-800">
                            <tr>
                                <th class="px-3 py-2">Række</th>
                                <th class="px-3 py-2">Kontraktnr</th>
                                <th class="px-3 py-2">Årsag</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-red-200">
                            @foreach($failedRows as $fail)
                                <tr>
                                    <td class="px-3 py-2">{{ $fail['row'] }}</td>
                                    <td class="px-3 py-2 font-mono">
                                        {{ $fail['sagsnr'] }}
                                    </td>
                                    <td class="px-3 py-2 text-red-700">
                                        {{ $fail['reason'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
@if(isset($session->meta['rolled_back_at']))
    <p class="text-sm text-gray-500 mt-2">
        Rullet tilbage {{ \Carbon\Carbon::parse($session->meta['rolled_back_at'])->diffForHumans() }}
    </p>
@endif

    </div>
</x-layouts.app>