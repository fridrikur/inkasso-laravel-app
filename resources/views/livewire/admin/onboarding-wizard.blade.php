<div>
    @if($showWizard)
        <div 
            x-data="{ 
                audioCtx: null, 
                isPlaying: false,
                oscillators: [],
                gainNode: null,
                initAudio() {
                    if (this.audioCtx) return;
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        this.audioCtx = new AudioContext();
                        
                        this.gainNode = this.audioCtx.createGain();
                        this.gainNode.gain.setValueAtTime(0.05, this.audioCtx.currentTime); // Lav, rolig lydstyrke
                        
                        // Lavpas filter for en blød, ambient tone
                        const filter = this.audioCtx.createBiquadFilter();
                        filter.type = 'lowpass';
                        filter.frequency.setValueAtTime(320, this.audioCtx.currentTime);

                        // Rolig ambient synth akkord (A maj9 pad: A2, E3, C#4, G#4)
                        const freqs = [110.00, 164.81, 277.18, 415.30];
                        
                        freqs.forEach(freq => {
                            const osc = this.audioCtx.createOscillator();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(freq, this.audioCtx.currentTime);
                            
                            // Lidt LFO for blød vibrato/bevægelse
                            const lfo = this.audioCtx.createOscillator();
                            lfo.frequency.setValueAtTime(0.2, this.audioCtx.currentTime);
                            const lfoGain = this.audioCtx.createGain();
                            lfoGain.gain.setValueAtTime(1.5, this.audioCtx.currentTime);
                            lfo.connect(osc.frequency);
                            lfo.start();

                            osc.connect(filter);
                            osc.start();
                            this.oscillators.push(osc);
                        });

                        filter.connect(this.gainNode);
                        this.gainNode.connect(this.audioCtx.destination);
                        this.isPlaying = true;
                    } catch(e) {
                        console.log('Web Audio ikke understøttet eller blokeret:', e);
                    }
                },
                toggleAudio() {
                    if (!this.audioCtx) {
                        this.initAudio();
                        return;
                    }
                    if (this.audioCtx.state === 'suspended') {
                        this.audioCtx.resume();
                        this.isPlaying = true;
                    } else if (this.audioCtx.state === 'running') {
                        this.audioCtx.suspend();
                        this.isPlaying = false;
                    }
                },
                stopAudio() {
                    if (this.audioCtx) {
                        this.gainNode.gain.exponentialRampToValueAtTime(0.0001, this.audioCtx.currentTime + 1);
                        setTimeout(() => this.audioCtx.close(), 1000);
                    }
                }
            }"
            x-init="
                // Start lyd ved første klik hvor som helst i modalen hvis autoplay var blokeret
                window.addEventListener('click', () => { if (!isPlaying) initAudio(); }, { once: true });
            "
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4"
        >
            <div class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden text-center p-8 space-y-6 relative">
                
                {{-- LYDSTYRKE / AMBIENT TOGGLE KNAP --}}
                <button 
                    type="button" 
                    @click="toggleAudio()"
                    class="absolute top-5 right-5 p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition text-xs font-bold flex items-center gap-1.5 cursor-pointer z-10"
                    :title="isPlaying ? 'Sluk ambient musik' : 'Tænd ambient musik'"
                >
                    <span x-text="isPlaying ? '🔊' : '🔇'"></span>
                    <span x-text="isPlaying ? 'Ambient Lyd Til' : 'Lyd Fra'" class="text-[10px] text-slate-500 hidden sm:inline"></span>
                </button>

                {{-- 🔴 TRIN 0: INGEN TABELLER I DATABASEN (INSTALLATION) --}}
                @if(! $hasDatabaseTables)
                    
                    <div 
                        x-data="{ 
                            migrating: false,
                            progress: 0,
                            statusText: 'Klar til opstart...',
                            logs: [],
                            startInstallation() {
                                this.migrating = true;
                                this.progress = 5;
                                this.statusText = 'Forbinder til MySQL databasen...';
                                this.logs.push('[INIT] Opretter forbindelse til databasen...');

                                setTimeout(() => {
                                    this.progress = 25;
                                    this.statusText = 'Udfører php artisan migrate...';
                                    this.logs.push('[MIGRATE] Opretter systemtabeller, system_settings & roles...');
                                }, 600);

                                setTimeout(() => {
                                    this.progress = 60;
                                    this.statusText = 'Kører database migrationer...';
                                    this.logs.push('[MIGRATE] Bygger tabelstrukturer for sager, kreditorer & brugere...');
                                }, 1200);

                                setTimeout(() => {
                                    this.progress = 85;
                                    this.statusText = 'Opretter standardroller og Admin-bruger...';
                                    this.logs.push('[SEED] Afvikler UserSeeder (Admin: admin / 123456)...');
                                }, 1800);

                                $wire.executeSystemInstallation().then(() => {
                                    this.progress = 100;
                                    this.statusText = 'Systemet blev installeret med succes!';
                                    this.logs.push('[SUCCESS] Databasen er nu 100% klar.');
                                });
                            }
                        }"
                        class="space-y-6"
                    >
                        <div class="mx-auto w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center text-amber-400 font-mono text-2xl shadow-xl border border-slate-800">
                            &gt;_
                        </div>

                        <div class="space-y-1">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                                System Installation
                            </h2>
                            <p class="text-xs text-slate-500 max-w-md mx-auto">
                                Der blev ikke fundet nogle tabeller i databasen. Klik herunder for at opbygge databasestrukturen.
                            </p>
                        </div>

                        {{-- KNAP FØR START --}}
                        <div x-show="!migrating" class="pt-2 flex justify-center">
                            <button 
                                type="button" 
                                @click="startInstallation()"
                                class="px-8 py-3.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-emerald-400 font-mono font-bold text-xs shadow-xl transition cursor-pointer flex items-center gap-2.5 border border-slate-700"
                            >
                                <span>⚡ $ php artisan migrate --install</span>
                            </button>
                        </div>

                        {{-- 💻 UNIX STYLE TERMINAL PROGRESS BAR --}}
                        <div x-show="migrating" x-cloak class="w-full bg-slate-950 rounded-2xl p-5 border border-slate-800 text-left font-mono space-y-4 shadow-2xl">
                            
                            {{-- TERMINAL HEADER --}}
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2 text-[10px] text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500/80 inline-block"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500/80 inline-block"></span>
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500/80 inline-block"></span>
                                    <span class="ml-2 text-slate-400">bash - root@dkg-app:~#</span>
                                </div>
                                <span class="text-emerald-400 animate-pulse">SYSTEM_BUILDING</span>
                            </div>

                            {{-- STATUS-TEKST --}}
                            <div class="text-xs text-emerald-400 flex items-center justify-between">
                                <span x-text="statusText"></span>
                                <span x-text="progress + '%'" class="font-bold"></span>
                            </div>

                            {{-- GRAFISK RETRO BAR --}}
                            <div class="w-full bg-slate-900 rounded-lg p-1 border border-slate-800">
                                <div 
                                    class="h-3 bg-gradient-to-r from-emerald-600 to-emerald-400 rounded transition-all duration-300 ease-out shadow-sm"
                                    :style="'width: ' + progress + '%'"
                                ></div>
                            </div>

                            {{-- UNIX ASCII PROGRESSTEXT BAR ([▓▓▓▓▓░░░░░]) --}}
                            <div class="text-[11px] text-slate-400 tracking-widest text-center">
                                [<span class="text-emerald-400" x-text="'▓'.repeat(Math.floor(progress / 5))"></span><span class="text-slate-800" x-text="'░'.repeat(20 - Math.floor(progress / 5))"></span>]
                            </div>

                            {{-- LØBENDE LOG OUTPUT --}}
                            <div class="space-y-1 text-[10px] text-slate-400 pt-1 border-t border-slate-900 max-h-24 overflow-y-auto">
                                <template x-for="(log, idx) in logs" :key="idx">
                                    <div class="flex items-center gap-2">
                                        <span class="text-emerald-500">&gt;</span>
                                        <span x-text="log" class="text-slate-300"></span>
                                    </div>
                                </template>
                            </div>

                        </div>

                    </div>

                {{-- ⚖️ TRIN 1: TABELLER ER OPRETTET, MEN Systemet mangler indhold --}}
                @else

                    {{-- NY SAGSBEHANDLINGS-IKON BADGE (⚖️ & 📂) --}}
                    <div class="mx-auto w-20 h-20 bg-indigo-50 border border-indigo-100 rounded-3xl flex items-center justify-center text-3xl shadow-sm animate-pulse relative">
                        <span>⚖️</span>
                        <span class="absolute -bottom-1 -right-1 text-lg">📂</span>
                    </div>

                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">
                            Velkommen til dit nye sagsbehandlingssystem!
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">
                            Databasen er oprettet. Hvordan ønsker du at opstarte løsningen?
                        </p>
                    </div>

                    {{-- VALGMULIGHEDER (3 KORT) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left pt-2">
                        
                        {{-- KORT 1: REN INSTALLATION --}}
                        <button 
                            type="button" 
                            @click="stopAudio()"
                            wire:click="startFresh"
                            class="p-5 rounded-2xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/30 transition group flex flex-col justify-between cursor-pointer"
                        >
                            <div>
                                <div class="text-2xl mb-2">✨</div>
                                <div class="font-bold text-xs text-slate-900 group-hover:text-indigo-600">
                                    Start ny installation
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                    Begynd med en helt tom database klar til dine egne data.
                                </p>
                            </div>
                            <span class="mt-4 text-[10px] font-bold text-indigo-600">Vælg tom DB &rarr;</span>
                        </button>

                        {{-- KORT 2: IMPORTER FRA GAMMELT SYSTEM --}}
                        <button 
                            type="button" 
                            @click="stopAudio()"
                            wire:click="goToImport"
                            class="p-5 rounded-2xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/30 transition group flex flex-col justify-between cursor-pointer"
                        >
                            <div>
                                <div class="text-2xl mb-2">📥</div>
                                <div class="font-bold text-xs text-slate-900 group-hover:text-indigo-600">
                                    Importér fra gammelt system
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                    Upload eksisterende sager og kreditorer via importøren.
                                </p>
                            </div>
                            <span class="mt-4 text-[10px] font-bold text-indigo-600">Gå til import &rarr;</span>
                        </button>

                        {{-- KORT 3: INSTALLER TEST DATA --}}
                        <button 
                            type="button" 
                            @click="stopAudio()"
                            wire:click="installDemoData"
                            class="p-5 rounded-2xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/30 transition group flex flex-col justify-between cursor-pointer"
                        >
                            <div>
                                <div class="text-2xl mb-2">🧪</div>
                                <div class="font-bold text-xs text-slate-900 group-hover:text-emerald-600">
                                    Installér testdata
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                    Fyld systemet med demo-brugere, kreditorer og sager.
                                </p>
                            </div>
                            <span class="mt-4 text-[10px] font-bold text-emerald-600">Kør demo-data &rarr;</span>
                        </button>

                    </div>

                @endif

            </div>
        </div>
    @endif
</div>