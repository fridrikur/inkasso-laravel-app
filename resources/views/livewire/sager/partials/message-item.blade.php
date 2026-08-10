<div class="pt-3 text-xs">
    <div class="flex items-center justify-between text-slate-400 mb-1">
        <span class="font-bold text-slate-700">{{ $msg->sender?->name ?? 'System' }}</span>
        
        <div class="flex items-center gap-3">
            <span>{{ \Carbon\Carbon::parse($msg->dato ?? $msg->created_at)->format('d/m-Y H:i') }}</span>
            
            @if($editingMessageId !== $msg->id)
                <button 
                    type="button" 
                    wire:click="editMessage({{ $msg->id }})"
                    class="text-indigo-600 hover:text-indigo-800 font-semibold cursor-pointer"
                >
                    Rediger
                </button>
            @endif
        </div>
    </div>

    @if($editingMessageId === $msg->id)
        <div class="space-y-2 mt-2">
            <textarea 
                wire:model="editingText" 
                rows="3" 
                class="w-full rounded-xl border border-indigo-300 p-2.5 text-xs outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
            ></textarea>
            
            <div class="flex justify-end gap-2">
                <button 
                    type="button" 
                    wire:click="cancelEdit" 
                    class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-lg transition cursor-pointer"
                >
                    Annuller
                </button>
                <button 
                    type="button" 
                    wire:click="updateMessage" 
                    class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition cursor-pointer"
                >
                    Gem ændringer
                </button>
            </div>
        </div>
    @else
        <p class="text-slate-800 whitespace-pre-line bg-slate-50 p-3 rounded-xl border border-slate-100">
            {{ $msg->tekst }}
        </p>
    @endif
</div>