<div>
    @if($sag && $sag->exists)
        <div class="space-y-6">
            @role('Admin|Medarbejder|Kreditor')
                <form wire:submit.prevent="uploadDokument" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <input
                            type="file"
                            wire:model="newDokument"
                            class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer"
                        >
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition"
                        >
                            Upload Dokument
                        </button>
                    </div>
                    @error('newDokument')
                        <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </form>
            @endrole

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm divide-y divide-slate-100">
                @forelse($dokumenter as $dok)
                    <div class="p-4 flex justify-between items-center">
                        <div>
                            <div class="font-bold text-slate-800 text-xs">{{ $dok->file_name }}</div>
                            <div class="text-[11px] text-slate-400">
                                {{ number_format($dok->file_size / 1024, 2) }} KB
                                – {{ optional($dok->uploaded_date)->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a
                                href="{{ route('sager.dokumenter.download', [$sag, $dok]) }}"
                                class="text-indigo-600 hover:underline text-xs font-bold"
                            >
                                Download
                            </a>

                            @role('Admin|Medarbejder')
                                <button
                                    type="button"
                                    wire:click="deleteDokument({{ $dok->id }})"
                                    class="text-rose-500 hover:text-rose-700 text-xs font-bold"
                                >
                                    Slet
                                </button>
                            @endrole
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Ingen dokumenter tilknyttet denne sag endnu.
                    </div>
                @endforelse
            </div>
        </div>
    @else
        <div class="p-8 text-center text-slate-400 text-xs bg-slate-50 rounded-2xl">
            Du skal gemme sagen som kladde, før du kan uploade dokumenter.
        </div>
    @endif
</div>