<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-2xl p-4 select-none" 
     x-data="{
         // Reagerer når alle 4 cifre er udfyldt i første række
         get pinComplete() {
             return $wire.digit1 !== '' && $wire.digit2 !== '' && $wire.digit3 !== '' && $wire.digit4 !== '';
         }
     }"
>
    <div class="w-full max-w-lg bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 rounded-[38px] shadow-2xl border border-slate-800/80 overflow-hidden text-center p-8 sm:p-12 relative">
        
        <div class="space-y-6">
            <div class="mx-auto w-20 h-20 bg-gradient-to-tr from-indigo-900 to-slate-800 rounded-3xl flex items-center justify-center shadow-2xl border border-indigo-500/30">
                <span class="text-3xl">🔒</span>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl sm:text-3xl font-semibold text-white tracking-tight" x-text="pinComplete ? 'Bekræft låsekode' : 'Vælg låsekode'">
                    Vælg låsekode
                </h2>
                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed" x-text="pinComplete ? 'Indtast den samme 4-cifrede pinkode igen for at bekræfte.' : 'Indtast en 4-cifret pinkode, der skal bruges som global sikkerhedskode.'">
                    Indtast en 4-cifret pinkode, der skal bruges som global sikkerhedskode for systemet.
                </p>
            </div>

            <form wire:submit.prevent="saveUnlockCode" class="space-y-6">
                
                {{-- FØRSTE KODE (Vises altid) --}}
                <div class="space-y-2">
                    <div class="flex justify-center gap-3" x-data="{
                        handleInput(e, nextIndex) {
                            if (e.target.value.length >= 1 && nextIndex) {
                                document.getElementById('pin-' + nextIndex).focus();
                            }
                        }
                    }">
                        <input id="pin-1" type="password" maxlength="1" wire:model.live="digit1" @input="handleInput($event, 2)" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none" autofocus required />
                        <input id="pin-2" type="password" maxlength="1" wire:model.live="digit2" @input="handleInput($event, 3)" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none" required />
                        <input id="pin-3" type="password" maxlength="1" wire:model.live="digit3" @input="handleInput($event, 4)" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none" required />
                        <input id="pin-4" type="password" maxlength="1" wire:model.live="digit4" @input="if($event.target.value.length >= 1) { setTimeout(() => { let el = document.getElementById('confirm-1'); if(el) el.focus(); }, 50); }" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none" required />
                    </div>
                </div>

                {{-- BEKRÆFT KODE (Vises først når de første 4 cifre er tastet) --}}
                <div class="space-y-2 pt-2" x-show="pinComplete" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
                    <label class="block text-[11px] font-semibold text-indigo-400 uppercase tracking-wider">Bekræft låsekode</label>
                    <div class="flex justify-center gap-3" x-data="{
                        handleConfirmInput(e, nextIndex) {
                            if (e.target.value.length >= 1 && nextIndex) {
                                document.getElementById('confirm-' + nextIndex).focus();
                            }
                        }
                    }">
                        <input id="confirm-1" type="password" maxlength="1" wire:model="confirmDigit1" @input="handleConfirmInput($event, 2)" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-indigo-500/50 rounded-2xl text-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/15 outline-none" />
                        <input id="confirm-2" type="password" maxlength="1" wire:model="confirmDigit2" @input="handleConfirmInput($event, 3)" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-indigo-500/50 rounded-2xl text-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/15 outline-none" />
                        <input id="confirm-3" type="password" maxlength="1" wire:model="confirmDigit3" @input="handleConfirmInput($event, 4)" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-indigo-500/50 rounded-2xl text-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/15 outline-none" />
                        <input id="confirm-4" type="password" maxlength="1" wire:model="confirmDigit4" class="w-12 h-14 text-center text-xl font-bold bg-slate-800/80 border border-indigo-500/50 rounded-2xl text-white focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/15 outline-none" />
                    </div>
                </div>

                {{-- KNAP (Vises også først når bekræftelse er aktiv) --}}
                <div class="pt-2" x-show="pinComplete" x-transition:enter="transition ease-out duration-300" style="display: none;">
                    <button 
                        type="submit" 
                        class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition-all duration-200 shadow-2xl cursor-pointer"
                    >
                        Gem låsekode & fortsæt
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>