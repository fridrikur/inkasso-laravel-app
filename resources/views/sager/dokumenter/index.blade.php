<x-layouts.app title="Dokumenter - Sag {{ $sag->sagsnr }}">

<div class="max-w-5xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            Dokumenter - Sag {{ $sag->sagsnr }}
        </h1>

        {{-- 🟢 DOWNLOAD ALT KNAP --}}
        @if(isset($dokumenter) && $dokumenter->isNotEmpty())
            <a href="{{ route('sager.dokumenter.downloadAll', $sag) }}"
               class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700 transition flex items-center gap-2">
                <span>⬇ Download alt (.zip)</span>
            </a>
        @endif
    </div>

    {{-- Resten af dokument-oversigten ... --}}
</div>    {{-- Rettet så det matcher controllerens tilladelse for upload --}}
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

                @php
                    $ext = strtolower(pathinfo($dok->file_name, PATHINFO_EXTENSION));
                    $isPdf = $ext === 'pdf';
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                    // Generer URL til visning (kræver en rute eller at filen kan tilgås via storage URL)
                    $fileUrl = route('sager.dokumenter.download', [$sag->id, $dok->id]); // eller asset('storage/' . $dok->file_path)
                @endphp
                <div class="flex items-center gap-4">
                    @if($isPdf || $isImage) 
                        <button type="button" 
                                @click="$dispatch('open-preview', { url: '{{ $fileUrl }}', name: '{{ $dok->file_name }}', type: '{{ $isPdf ? 'pdf' : 'image' }}' })"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                            <span>👁️</span> Vis
                        </button>
                    @endif
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
</div>

{{-- PREVIEW / LÆSER MODAL --}}
<div x-data="{ 
        showPreview: false, 
        fileUrl: '', 
        fileName: '', 
        fileType: '' 
     }" 
     @open-preview.window="
        showPreview = true; 
        fileUrl = $event.detail.url; 
        fileName = $event.detail.name; 
        fileType = $event.detail.type;
     "
     x-show="showPreview" 
     class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4"
     style="display: none;">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl h-[85vh] flex flex-col overflow-hidden border border-slate-100"
         @click.outside="showPreview = false">
        
        {{-- MODAL HEADER --}}
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <span class="text-xl" x-text="fileType === 'pdf' ? '📄' : '🖼️'"></span>
                <div>
                    <h3 class="text-xs font-bold text-slate-900 truncate max-w-md" x-text="fileName"></h3>
                    <span class="text-[10px] text-slate-400">Forhåndsvisning</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a :href="fileUrl" download class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                    <span>⬇️</span> Download
                </a>
                <button @click="showPreview = false" class="w-8 h-8 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 flex items-center justify-center font-bold transition cursor-pointer">
                    &times;
                </button>
            </div>
        </div>

        {{-- MODAL BODY (VISER PDF ELLER BILLEDE) --}}
        <div class="flex-1 bg-slate-900/5 p-4 flex items-center justify-center overflow-auto">
            <template x-if="fileType === 'pdf'">
                <object :data="fileUrl" type="application/pdf" class="w-full h-full rounded-2xl border border-slate-200 bg-white shadow-inner">
                    <div class="p-8 text-center space-y-3">
                        <p class="text-xs text-slate-600">Din browser understøtter ikke direkte visning af PDF-filer.</p>
                        <a :href="fileUrl" download class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold inline-block">Download PDF i stedet</a>
                    </div>
                </object>
            </template>

            <template x-if="fileType === 'image'">
                <img :src="fileUrl" class="max-h-full max-w-full object-contain rounded-2xl shadow-md bg-white p-2">
            </template>
        </div>

    </div>
</div>
</x-layouts.app>