<div class="max-w-4xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        
        <!-- HEADER -->
        <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm shadow-sm">🏢</span>
                <h2 class="text-base font-bold text-slate-900">Rediger Debitor: {{ $form->navn ?? '' }}</h2>
            </div>
            <a href="{{ route('debitorer.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                Tilbage til oversigt
            </a>
        </div>

        <!-- FORMULAR -->
        <form wire:submit.prevent="save" class="p-6 space-y-5">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                
                <!-- Navn -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Navn</label>
                    <input type="text" wire:model.defer="form.navn" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.navn') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- C/O -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">C/O</label>
                    <input type="text" wire:model.defer="form.co" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.co') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Adresse -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Adresse</label>
                    <input type="text" wire:model.defer="form.adresse" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.adresse') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Postnr -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Postnummer</label>
                    <input type="text" wire:model.defer="form.postnr" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.postnr') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- CPR / PNR -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">CPR / PNR</label>
                    <input type="text" wire:model.defer="form.pnr" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-mono outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.pnr') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">E-mail</label>
                    <input type="email" wire:model.defer="form.email" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.email') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tlf -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Telefon</label>
                    <input type="text" wire:model.defer="form.tlf" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.tlf') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Mobil -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mobil</label>
                    <input type="text" wire:model.defer="form.mobil" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.mobil') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Adropl (Datofelt) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Adropl (Dato)</label>
                    <input type="date" wire:model.defer="form.adropl" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition">
                    @error('form.adropl') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Kontakt bemærkning -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kontakt bemærkning</label>
                    <textarea wire:model.defer="form.kontakt_bemaerkning" rows="3" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition"></textarea>
                    @error('form.kontakt_bemaerkning') <span class="text-xs text-rose-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

            </div>

            <!-- HANDLINGSKNAPPER -->
            <div class="bg-slate-50/80 -mx-6 -mb-6 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 mt-6">
                <a href="{{ route('debitorer.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs cursor-pointer transition">
                    Annuller
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs cursor-pointer shadow-sm transition">
                    Gem ændringer
                </button>
            </div>

        </form>

    </div>
</div>