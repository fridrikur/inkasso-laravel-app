<div x-data="{
    showCopyAnimation: false,
    insertAtCursor(event) {
        let textToInsert = event.target.value;
        if (!textToInsert) return;

        let textarea = this.$refs.historikInput;
        let startPos = textarea.selectionStart || 0;
        let endPos = textarea.selectionEnd || 0;
        let currentValue = textarea.value || '';

        let newValue = currentValue.substring(0, startPos) 
                     + textToInsert 
                     + currentValue.substring(endPos, currentValue.length);

        textarea.value = newValue;
        $wire.set('messageText', newValue);
        
        // 🟢 Giv Livewire besked om at der er valgt en autotekst
        $wire.call('setAutotekstSelected', true);

        this.$nextTick(() => {
            textarea.focus();
            textarea.setSelectionRange(startPos + textToInsert.length, startPos + textToInsert.length);
        });

        event.target.value = '';
    }
}" 
x-on:klientinformation-updated.window="
    showCopyAnimation = true; 
    setTimeout(() => showCopyAnimation = false, 3500);
"
class="space-y-3 relative">

    {{-- ANIMATION BANNER (Kun ved kopiering til Klientinformation) --}}
    <div 
        x-show="showCopyAnimation"
        x-cloak
        x-transition:enter="transition ease-out duration-300 transform -translate-y-2 opacity-0"
        x-transition:enter-end="transform translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-300 transform -translate-y-2 opacity-0"
        class="rounded-2xl border border-indigo-200 bg-indigo-50/90 p-3.5 shadow-sm flex items-center justify-between"
    >
        <div class="flex items-center gap-3">
            <div class="relative flex h-8 w-8 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold shrink-0 shadow-sm">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-xl bg-indigo-400 opacity-75"></span>
                <svg class="w-4 h-4 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold text-indigo-900 flex items-center gap-1.5">
                    <span>Note gemt & kopi oprettet i Klientinformation</span>
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </p>
                <p class="text-[11px] text-indigo-700">
                    Kreditor kan nu se beskeden direkte under Klientinformation-fanen.
                </p>
            </div>
        </div>

        <button 
            type="button" 
            @click="showCopyAnimation = false" 
            class="text-indigo-400 hover:text-indigo-700 transition p-1"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- OVERSKRIFT & DROPDOWN --}}
    <div class="flex items-center justify-between">
        <label class="text-xs font-bold text-slate-700">Skriv intern historik / note</label>

        <select 
            @change="insertAtCursor($event)"
            class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-600 focus:border-indigo-500 outline-none cursor-pointer"
        >
            <option value="">⚡ Indsæt autotekst ved markør...</option>
            @foreach(\App\Models\Autotekster::orderBy('id', 'desc')->get() as $autotekst)
                <option value="{{ $autotekst->tekst }}">
                    {{ \Illuminate\Support\Str::limit($autotekst->tekst, 45) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- TEKSTAREAL --}}
    <textarea 
        x-ref="historikInput"
        wire:model.live="messageText" 
        rows="3" 
        class="w-full rounded-2xl border border-slate-200 p-3 text-xs outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition"
        placeholder="Skriv note her..."
    ></textarea>

    {{-- GEM-KNAP --}}
    <div class="flex justify-end">
        <button 
            type="button" 
            wire:click="saveNote"
            wire:loading.attr="disabled"
            class="px-4 py-2 font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-2 cursor-pointer {{ $isAutotekstSelected ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-slate-700 hover:bg-slate-800 text-white' }}"
        >
            <svg wire:loading class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            
            <span>
                @if($isAutotekstSelected)
                    Gem i Historik & Klientinformation
                @else
                    Gem i Historik
                @endif
            </span>
        </button>
    </div>

    {{-- LISTE OVER EKSISTERENDE HISTORIK BESKEDER --}}
    <div class="mt-6 space-y-3 divide-y divide-slate-100">
        @forelse($messages as $msg)
            @include('livewire.sager.partials.message-item', ['msg' => $msg])
        @empty
            <p class="text-xs text-slate-400 italic">Ingen historiknotater oprettet endnu.</p>
        @endforelse
    </div>

    {{-- BESKED-SLETTEMODAL --}}
    @if($showDeleteMessageModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
                <button 
                    type="button" 
                    wire:click="$set('showDeleteMessageModal', false)" 
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition cursor-pointer"
                >
                    &times;
                </button>

                <div class="flex items-center gap-3">
                    <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">
                            Flyt besked til papirkurv?
                        </h3>
                        <p class="text-xs text-slate-500">
                            Beskeden fjernes, men kan gendannes, hvis du fortryder.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button 
                        type="button" 
                        wire:click="$set('showDeleteMessageModal', false)" 
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer"
                    >
                        Annuller
                    </button>
                    <button 
                        type="button" 
                        wire:click="executeDeleteMessage" 
                        class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer"
                    >
                        Flyt til papirkurv
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PAPIRKURV TIL SLETTEDE NOTER / DIALOGER --}}
    @if($this->trashMessages->isNotEmpty())
        <div class="mt-8 pt-6 border-t border-slate-200/80">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🗑 Papirkurv for slettede noter</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px]">
                        {{ $this->trashMessages->count() }}
                    </span>
                </h3>
            </div>

            <div class="space-y-2">
                @foreach($this->trashMessages as $trashMsg)
                    <div class="p-3.5 rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 flex items-center justify-between gap-4">
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-600 line-through">
                                    👤 {{ $trashMsg->sender?->name ?? 'System' }}
                                </span>
                                <span class="text-[10px] text-rose-600 font-medium">
                                    Slettet: {{ \Carbon\Carbon::parse($trashMsg->deleted_at)->format('d-m-Y H:i') }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 italic line-clamp-2">
                                "{{ $trashMsg->tekst }}"
                            </p>
                        </div>

                        <button 
                            type="button" 
                            wire:click="restoreMessage({{ $trashMsg->id }})"
                            class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-xs rounded-xl transition flex items-center gap-1.5 shrink-0 cursor-pointer"
                            title="Gendan note"
                        >
                            <span>♻️ Gendan</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>