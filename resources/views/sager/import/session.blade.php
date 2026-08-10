<x-layouts.app title="Import detaljer">
    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 space-y-8">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Import Session #{{ $session->id }}
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Fil: <span class="font-mono text-slate-700 font-semibold">{{ $session->file_name ?? basename($session->file_path ?? 'CSV-fil') }}</span>
                    • Dato: <span class="font-medium text-slate-700">{{ $session->created_at?->format('d/m-Y H:i') ?? 'Ukendt' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('sager.import.log') }}" class="px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                    ← Tilbage til log
                </a>

                @if(($session->inserted_rows ?? 0) > 0)
                    <form method="POST"
                        action="{{ route('sager.import.session.rollback', $session) }}"
                        onsubmit="return confirm('Er du sikker på, at du vil rulle denne import tilbage? Alle oprettede sager i denne session vil blive fjernet.');">
                        @csrf
                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition cursor-pointer">
                            Rul import tilbage
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- STATS NØGLETAL -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            
            {{-- INDSATTE RÆKKER --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm text-center">
                <div class="text-3xl font-bold text-emerald-600 font-mono">
                    {{ number_format($session->inserted_rows ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">
                    Sager indsat
                </div>
            </div>

            {{-- FEJLEDE RÆKKER --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm text-center">
                <div class="text-3xl font-bold {{ ($session->failed_rows ?? 0) > 0 ? 'text-rose-600' : 'text-slate-400' }} font-mono">
                    {{ number_format($session->failed_rows ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">
                    Rækker fejlede
                </div>
            </div>

            {{-- KREDITOR --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm text-center flex flex-col justify-center">
                <div class="text-base font-bold text-slate-900 truncate">
                    {{ $session->kreditor?->navn ?? 'Ukendt kreditor' }}
                </div>
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">
                    Kreditor
                </div>
            </div>

        </div>

        <!-- FEJLEDE RÆKKER TABEL -->
        @if(!empty($failedRows) && count($failedRows) > 0)
            <div class="bg-rose-50/60 border border-rose-200/80 rounded-3xl p-6 space-y-4">
                <h3 class="font-bold text-xs uppercase tracking-wider text-rose-900 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Fejlede rækker ({{ count($failedRows) }})</span>
                </h3>

                <div class="overflow-x-auto bg-white rounded-2xl border border-rose-100 shadow-sm">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-rose-100/50 text-rose-900 font-semibold uppercase text-[10px]">
                            <tr>
                                <th class="px-4 py-3">Række #</th>
                                <th class="px-4 py-3">Sagsnummer / Kontraktnr</th>
                                <th class="px-4 py-3">Fejlårsag</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-100 text-slate-700">
                            @foreach($failedRows as $fail)
                                <tr class="hover:bg-rose-50/30 transition">
                                    <td class="px-4 py-2.5 font-mono text-slate-500">
                                        {{ $fail['row'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 font-mono font-bold text-slate-900">
                                        {{ $fail['sagsnr'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2.5 text-rose-700 font-medium">
                                        {{ $fail['reason'] ?? 'Ukendt fejl' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>