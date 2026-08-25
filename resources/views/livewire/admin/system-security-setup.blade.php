<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-2xl p-4 select-none">
    <div class="w-full max-w-lg bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 rounded-[38px] shadow-2xl border border-slate-800/80 overflow-hidden text-center p-8 sm:p-12 relative">
        
        <div class="space-y-6">
            <div class="mx-auto w-20 h-20 bg-gradient-to-tr from-indigo-900 to-slate-800 rounded-3xl flex items-center justify-center shadow-2xl border border-indigo-500/30">
                <span class="text-3xl">🔒</span>
            </div>

            <div class="space-y-2">
                <h2 class="text-2xl sm:text-3xl font-semibold text-white tracking-tight">
                    Vælg låsekode
                </h2>
                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                    Indtast en 4-cifret pinkode, der skal bruges som global sikkerhedskode for systemet.
                </p>
            </div>

            <form wire:submit.prevent="saveUnlockCode" class="space-y-6">
                <div class="flex justify-center gap-3" x-data="{
                    handleInput(e, nextIndex) {
                        if (e.target.value.length >= 1 && nextIndex) {
                            document.getElementById('pin-' + nextIndex).focus();
                        }
                    }
                }">
                    <input 
                        id="pin-1"
                        type="password" 
                        maxlength="1" 
                        wire:model="digit1" 
                        @input="handleInput($event, 2)"
                        class="w-14 h-16 text-center text-2xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none"
                        autofocus
                        required
                    />
                    <input 
                        id="pin-2"
                        type="password" 
                        maxlength="1" 
                        wire:model="digit2" 
                        @input="handleInput($event, 3)"
                        class="w-14 h-16 text-center text-2xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none"
                        required
                    />
                    <input 
                        id="pin-3"
                        type="password" 
                        maxlength="1" 
                        wire:model="digit3" 
                        @input="handleInput($event, 4)"
                        class="w-14 h-16 text-center text-2xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none"
                        required
                    />
                    <input 
                        id="pin-4"
                        type="password" 
                        maxlength="1" 
                        wire:model="digit4" 
                        class="w-14 h-16 text-center text-2xl font-bold bg-slate-800/80 border border-slate-700 rounded-2xl text-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15 outline-none"
                        required
                    />
                </div>

                <div>
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