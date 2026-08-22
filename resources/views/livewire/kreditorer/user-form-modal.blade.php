<div>
@if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
            <button type="button" wire:click="closeModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 transition cursor-pointer">&times;</button>
            <h2 class="text-lg font-bold text-slate-900">{{ $editingId ? 'Rediger bruger' : 'Opret ny bruger' }}</h2>
            
            <form wire:submit.prevent="save" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Navn</label>
                    <input type="text" wire:model="name" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:outline-hidden">
                    @error('name') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">E-mail</label>
                    <input type="email" wire:model="email" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:outline-hidden">
                    @error('email') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Adgangskode {{ $editingId ? '(valgfri ved ændring)' : '' }}
                    </label>
                    <input type="password" wire:model="password" placeholder="••••••••" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-xs focus:outline-hidden">
                    @error('password') <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">Annuller</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-xs transition cursor-pointer">Gem bruger</button>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- SLET BEKRÆFTELSESMODAL --}}
@if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative border border-slate-100 space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-rose-50 rounded-2xl text-rose-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Fjern bruger</h3>
                    <p class="text-xs text-slate-500">Er du sikker på, at du vil fjerne denne bruger fra kreditoren?</p>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition cursor-pointer">Annuller</button>
                <button type="button" wire:click="deleteUser" class="px-4 py-2 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition cursor-pointer">Fjern bruger</button>
            </div>
        </div>
    </div>
@endif
</div>