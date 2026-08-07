<div class="space-y-6">

    {{-- HEADER BANNER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                System Backups
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Opret, gendan og administrer lokale SQL-dumps af systemets database.
            </p>
        </div>

        <button
            type="button"
            wire:click="runBackup"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-xs font-semibold shadow-sm transition shrink-0 cursor-pointer"
        >
            <svg wire:loading.remove wire:target="runBackup" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <svg wire:loading wire:target="runBackup" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <span wire:loading.remove wire:target="runBackup">Opret backup nu</span>
            <span wire:loading wire:target="runBackup">Genererer SQL-dump...</span>
        </button>
    </div>

    {{-- OVERSIGT OVER BACKUP-FILER --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div class="text-xs font-bold uppercase tracking-wider text-slate-700">
                Eksisterende backups ({{ count($backups) }})
            </div>
            <span class="text-xs text-slate-400">Gemmes lokalt i storage/app/backups</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/60">
                    <tr>
                        <th scope="col" class="px-6 py-4">Filnavn</th>
                        <th scope="col" class="px-6 py-4">Størrelse</th>
                        <th scope="col" class="px-6 py-4">Oprettet</th>
                        <th scope="col" class="px-6 py-4 text-right">Handling</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    @forelse($backups as $backup)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-slate-900">
                                💾 {{ $backup['name'] }}
                            </td>

                            <td class="px-6 py-4 text-slate-600 text-xs">
                                {{ $backup['size'] }}
                            </td>

                            <td class="px-6 py-4 text-slate-600 text-xs">
                                {{ $backup['created_at']->format('d/m-Y H:i:s') }} 
                                <span class="text-slate-400 text-[11px]">({{ $backup['created_at']->diffForHumans() }})</span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- GENDAN KNAP (ÅBNER MODAL) --}}
                                    <button
                                        type="button"
                                        wire:click="confirmRestore('{{ $backup['name'] }}')"
                                        class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-200 transition cursor-pointer"
                                        title="Gendan databasen fra denne fil"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>

                                    {{-- DOWNLOAD KNAP --}}
                                    <button
                                        type="button"
                                        wire:click="downloadBackup('{{ $backup['name'] }}')"
                                        class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition cursor-pointer"
                                        title="Download SQL-fil"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </button>

                                    {{-- SLET KNAP --}}
                                    <button
                                        type="button"
                                        wire:click="deleteBackup('{{ $backup['name'] }}')"
                                        wire:confirm="Er du sikker på, at du vil slette denne backup-fil?"
                                        class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition cursor-pointer"
                                        title="Slet backup"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-slate-400">
                                <span class="block text-sm font-semibold text-slate-900">Ingen backups fundet</span>
                                <span class="block text-xs text-slate-500 mt-1">Tryk på "Opret backup nu" for at tage det første snapshot af databasen.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- BEKRÆFTELSES MODAL TIL GENDANNELSE --}}
    @if($showRestoreModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-4">
                <div class="flex items-center gap-3 text-amber-600">
                    <div class="p-3 bg-amber-50 rounded-2xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Bekræft Gendannelse</h3>
                </div>

                <p class="text-sm text-slate-600">
                    Er du sikker på, at du vil gendanne databasen fra <span class="font-mono font-semibold text-slate-900">{{ $selectedBackupForRestore }}</span>?
                </p>
                
                <div class="p-3 bg-amber-50 rounded-2xl text-xs text-amber-800 font-medium">
                    ⚠️ Bemærk: Dette vil overskrive dine nuværende data i databasen!
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        wire:click="cancelRestore"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl hover:bg-slate-100 transition cursor-pointer"
                    >
                        Annuller
                    </button>

                    <button
                        type="button"
                        wire:click="restoreBackup"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer"
                    >
                        <span wire:loading.remove wire:target="restoreBackup">Gendan nu</span>
                        <span wire:loading wire:target="restoreBackup">Gendanner...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- STATISTIK MODAL EFTER GENOPRETTELSE --}}
    @if($showStatsModal && $restoreStats)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 space-y-5">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Gendannelse Gennemført</h3>
                            <p class="text-xs text-slate-500 font-mono">{{ $restoreStats['filename'] }}</p>
                        </div>
                    </div>

                    <button type="button" wire:click="closeStatsModal" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- STATISTIK KORT --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-slate-50 p-3.5 rounded-2xl text-center border border-slate-100">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase">Tabeller</span>
                        <span class="text-xl font-black text-slate-900">{{ $restoreStats['table_count'] }}</span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl text-center border border-slate-100">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase">Total Rækker</span>
                        <span class="text-xl font-black text-emerald-600">{{ $restoreStats['total_rows'] }}</span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl text-center border border-slate-100">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase">Tid</span>
                        <span class="text-xl font-black text-indigo-600">{{ $restoreStats['execution_time'] }}</span>
                    </div>
                </div>

                {{-- DETALJERET TABEL OVERSIGT --}}
                <div class="space-y-2">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Oversigt pr. tabel</span>
                    <div class="max-h-48 overflow-y-auto border border-slate-100 rounded-2xl divide-y divide-slate-100">
                        @foreach($restoreStats['tables'] as $tbl)
                            <div class="px-4 py-2 flex items-center justify-between text-xs">
                                <span class="font-mono text-slate-700">{{ $tbl['name'] }}</span>
                                <span class="font-semibold text-slate-900 bg-slate-100 px-2 py-0.5 rounded-md">{{ number_format($tbl['rows'], 0, ',', '.') }} rækker</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button
                        type="button"
                        wire:click="closeStatsModal"
                        class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-sm transition cursor-pointer"
                    >
                        Luk oversigt
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>