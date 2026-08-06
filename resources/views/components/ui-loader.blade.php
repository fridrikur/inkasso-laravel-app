<div 
    x-data="{
        progress: 0,
        step: 1,
        timer: null,
        startProgress() {
            this.progress = 0;
            this.step = 1;
            clearInterval(this.timer);
            
            this.timer = setInterval(() => {
                if (this.progress < 95) {
                    // Kør hurtigt i starten, derefter lidt langsommere
                    let increment = Math.floor(Math.random() * 15) + 10;
                    this.progress = Math.min(this.progress + increment, 95);
                    
                    if (this.progress > 30 && this.step < 2) this.step = 2;
                    if (this.progress > 70 && this.step < 3) this.step = 3;
                }
            }, 120);
        },
        completeProgress() {
            clearInterval(this.timer);
            this.progress = 100;
            this.step = 3;
        }
    }"
    x-init="startProgress()"
    {{ $attributes->merge(['class' => 'w-full max-w-md mx-auto p-6 bg-white rounded-3xl border border-slate-100 shadow-2xl space-y-5']) }}
>
    
    {{-- TOP: IKON & TILE --}}
    <div class="flex items-center gap-4 border-b border-slate-100 pb-4">
        <div class="relative flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 transition-all">
            <template x-if="progress < 100">
                <svg class="h-6 w-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </template>
            <template x-if="progress === 100">
                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </template>

            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5" x-show="progress < 100">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-indigo-600"></span>
            </span>
        </div>

        <div class="text-left">
            <h3 class="text-base font-bold text-slate-900" x-text="progress === 100 ? 'Sager hentet!' : '{{ $title }}'"></h3>
            <p class="text-xs text-slate-500 mt-0.5" x-text="progress === 100 ? 'Opdaterer visningen...' : '{{ $subtitle }}'"></p>
        </div>
    </div>

    {{-- MIDTEN: PROGRESS BAR DER KØRER REELT TIL 100% --}}
    <div class="space-y-2">
        <div class="flex justify-between items-center text-xs font-bold text-slate-600">
            <span class="flex items-center gap-1.5" x-show="progress < 100">
                <span class="h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                Synkroniserer...
            </span>
            <span class="flex items-center gap-1.5 text-emerald-600 font-bold" x-show="progress === 100">
                <span>✓</span> Målstreg nået
            </span>

            <span class="font-mono text-indigo-600" x-text="progress + '%'"></span>
        </div>

        {{-- BREEZE PROGRESS TRACK --}}
        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden relative p-0.5">
            <div 
                class="h-full rounded-full transition-all duration-200 ease-out shadow-sm"
                :class="progress === 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-indigo-500 to-indigo-600'"
                :style="'width: ' + progress + '%'"
            ></div>
        </div>
    </div>

    {{-- BUNDEN: MIKRO-STEPS DER TÆNDER ÉN AF GANGEN --}}
    <div class="grid grid-cols-3 gap-2 pt-1 text-[11px] font-semibold">
        {{-- STEP 1 --}}
        <div 
            class="flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-xl border transition-all"
            :class="step >= 1 ? 'text-indigo-700 bg-indigo-50/80 border-indigo-200' : 'text-slate-400 bg-slate-50 border-transparent'"
        >
            <span x-text="step >= 1 ? '✓' : '•'"></span> Henter
        </div>

        {{-- STEP 2 --}}
        <div 
            class="flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-xl border transition-all"
            :class="step >= 2 ? 'text-indigo-700 bg-indigo-50/80 border-indigo-200' : 'text-slate-400 bg-slate-50 border-transparent'"
        >
            <span x-text="step >= 2 ? '✓' : '•'"></span> Parter
        </div>

        {{-- STEP 3 --}}
        <div 
            class="flex items-center justify-center gap-1.5 px-2 py-1.5 rounded-xl border transition-all"
            :class="step >= 3 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-slate-400 bg-slate-50 border-transparent'"
        >
            <span x-text="step >= 3 ? '✓' : '•'"></span> Klargør
        </div>
    </div>

</div>