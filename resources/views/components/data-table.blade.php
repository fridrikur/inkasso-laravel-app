@props([
    'headers' => [],
    'items' => [],
    'title' => '',
    'description' => '',
    'search' => null
])

<div class="space-y-6">
    {{-- Header & Opret-sektion --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                {{ $title }}
            </h1>
            @if($description)
                <p class="text-xs text-slate-500 mt-1">{{ $description }}</p>
            @endif
        </div>

        @if(isset($action))
            <div>{{ $action }}</div>
        @else
            <button 
                type="button" 
                wire:click="openCreateModal" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm cursor-pointer shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Opret ny</span>
            </button>
        @endif
    </div>

    {{-- Tabel-kort med automatisk søgefelt --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        
        @if(!is_null($search))
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-end">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Søg..." 
                    class="w-full sm:w-64 rounded-xl border border-slate-200 px-3.5 py-2 text-xs outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition"
                />
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <tr>
                        @foreach($headers as $header)
                            <th scope="col" class="px-6 py-3.5">{{ $header }}</th>
                        @endforeach
                        <th scope="col" class="px-6 py-3.5 text-right">Handling</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        @if(method_exists($items, 'links'))
            <div class="p-4 border-t border-slate-100 bg-slate-50/30">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>