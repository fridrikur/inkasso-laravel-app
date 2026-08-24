<div class="max-w-4xl mx-auto space-y-6">

    <x-data-table 
        title="💬 Autotekster" 
        description="Administrer skabelontekster og sags-dialog beskeder."
        :headers="['Autotekst', 'Dato']"
        :items="$autotekster"
    >
        @forelse ($autotekster as $item)
            <tr wire:key="auto-{{ $item->id }}" class="hover:bg-slate-50/50 transition">
                <td class="px-6 py-4 font-semibold text-slate-900 w-3/5">
                    <button 
                        type="button"
                        wire:click="openEditModal({{ $item->id }})" 
                        class="hover:text-indigo-600 transition text-left cursor-pointer line-clamp-2"
                    >
                        {{ $item->tekst }}
                    </button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 font-mono w-1/4">
                    {{ $item->dato }}
                </td>
                
                {{-- Brug standard table-actions, hvor du peger på de rette metoder i din Trait --}}
                <td class="px-6 py-4 text-right whitespace-nowrap w-32">
                    <div class="flex items-center justify-end gap-1.5">
                        <x-table-actions 
                            :id="$item->id" 
                            editAction="openEditModal" 
                            deleteAction="confirmDelete" 
                        />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-xs">
                            Ingen autotekster fundet.
                </td>
            </tr>
        @endforelse
    </x-data-table>

    {{-- OPRET / REDIGER MODAL --}}
    @if($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div wire:click="closeFormModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    
                    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm shadow-sm">💬</span>
                            <h3 class="text-base font-bold text-slate-900">{{ $editingId ? 'Rediger Autotekst' : 'Opret ny Autotekst' }}</h3>
                        </div>
                        <button type="button" wire:click="closeFormModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tekst</label>
                                <textarea wire:model="tekst" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required></textarea>
                                @error('tekst') <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Dato</label>
                                <input type="date" wire:model="dato" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required />
                                @error('dato') <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button type="button" wire:click="closeFormModal" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs cursor-pointer">Annuller</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-xs cursor-pointer shadow-sm">Gem ændringer</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    {{-- GENBRUGELIG SLETTEMODAL TIL BEKRÆFTELSE --}}
    <x-confirm-delete-modal 
        :show="$showDeleteModal" 
        title="Slet autotekst?" 
        message="Er du sikker på, at du vil slette denne autotekst? Handlingen kan ikke fortrydes." 
    />

</div>