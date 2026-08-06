@if($showFormModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        {{-- Mørkt backdrop med sløring --}}
        <div wire:click="closeFormModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-100">
                
                {{-- Header --}}
                <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between rounded-t-3xl">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm shadow-sm">👤</span>
                        <h3 class="text-base font-bold text-slate-900">
                            {{ $editingId ? 'Rediger Konsulent' : 'Opret ny Konsulent' }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeFormModal" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Formular --}}
                <form wire:submit.prevent="save">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Navn</label>
                            <input type="text" wire:model="modalNavn" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required />
                            @error('modalNavn') <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">E-mail</label>
                            <input type="email" wire:model="modalEmail" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" required />
                            @error('modalEmail') <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Telefon</label>
                                <input type="text" wire:model="modalTlf" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobil</label>
                                <input type="text" wire:model="modalMobil" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10" />
                            </div>
                        </div>

                        {{-- Roller / Fluer --}}
                        <div class="pt-2 space-y-2 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Roller</label>
                            
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="modalIsHoved" class="rounded text-indigo-600 focus:ring-indigo-500" />
                                <span>⭐ Hovedkonsulent</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="modalIsNotifikation" class="rounded text-indigo-600 focus:ring-indigo-500" />
                                <span>🔔 Modtag Notifikationer</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                <input type="checkbox" wire:model="modalIsSkjult" class="rounded text-indigo-600 focus:ring-indigo-500" />
                                <span>🙈 Skjult Konsulent</span>
                            </label>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                        <button type="button" wire:click="closeFormModal" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer">
                            Annuller
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition shadow-sm cursor-pointer">
                            Gem konsulent
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endif