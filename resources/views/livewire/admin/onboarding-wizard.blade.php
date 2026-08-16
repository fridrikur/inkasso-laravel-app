<div>
    @if($showWizard)
        <div 
            x-data="{ 
                audioCtx: null, 
                isPlaying: false,
                oscillators: [],
                gainNode: null,
                unboxing: false,
                progress: 0,
                statusText: 'Udpakker arbejdsplads...',
                
                initAudio() {
                    if (this.audioCtx) return;
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        this.audioCtx = new AudioContext();
                        
                        this.gainNode = this.audioCtx.createGain();
                        this.gainNode.gain.setValueAtTime(0.04, this.audioCtx.currentTime);
                        
                        const filter = this.audioCtx.createBiquadFilter();
                        filter.type = 'lowpass';
                        filter.frequency.setValueAtTime(280, this.audioCtx.currentTime);

                        const freqs = [138.59, 207.65, 261.63, 349.23];
                        
                        freqs.forEach(freq => {
                            const osc = this.audioCtx.createOscillator();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(freq, this.audioCtx.currentTime);
                            
                            const lfo = this.audioCtx.createOscillator();
                            lfo.frequency.setValueAtTime(0.15, this.audioCtx.currentTime);
                            const lfoGain = this.audioCtx.createGain();
                            lfoGain.gain.setValueAtTime(1.2, this.audioCtx.currentTime);
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
                        console.log('Audio error:', e);
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
                        this.gainNode.gain.exponentialRampToValueAtTime(0.0001, this.audioCtx.currentTime + 1.5);
                        setTimeout(() => this.audioCtx.close(), 1500);
                    }
                },

                startUnboxing() {
                    this.stopAudio();
                    this.unboxing = true;
                    this.progress = 5;
                    this.statusText = 'Åbner den digitale æske...';

                    // Jævn tæller der stiger op mod 99%
                    let timer = setInterval(() => {
                        if (this.progress < 99) {
                            this.progress += 1;
                            if (this.progress > 25 && this.progress < 50) {
                                this.statusText = 'Opretter test-kreditorer & brugere...';
                            } else if (this.progress >= 50 && this.progress < 80) {
                                this.statusText = 'Genererer demo-sager og relationer...';
                            } else if (this.progress >= 80) {
                                this.statusText = 'Færdiggør opsætning...';
                            }
                        }
                    }, 150);

                    // Kald Livewire-metoden
                    $wire.installDemoData()
                        .then(() => {
                            clearInterval(timer);
                            this.progress = 100;
                            this.statusText = 'Velkommen. Alt er klar.';
                            
                            // Ekstra sikkerhed for omdirigering hvis PHP-redirect tøver
                            setTimeout(() => {
                                window.location.href = '{{ route('dashboard') }}';
                            }, 500);
                        })
                        .catch((err) => {
                            clearInterval(timer);
                            this.unboxing = false;
                            console.error('Fejl:', err);
                        });
                }
            }"
            x-init="
                window.addEventListener('click', () => { if (!isPlaying) initAudio(); }, { once: true });
            "
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-2xl p-4 select-none"
        >
            <div wire:ignore.self class="w-full max-w-xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 rounded-[38px] shadow-2xl border border-slate-800/80 overflow-hidden text-center p-8 sm:p-12 relative transition-all duration-700">
                
                {{-- AMBIENT TOGGLE --}}
                <button 
                    type="button" 
                    @click="toggleAudio()"
                    class="absolute top-6 right-6 p-2.5 rounded-2xl bg-slate-800/60 hover:bg-slate-800 text-slate-300 transition text-xs font-medium flex items-center gap-2 border border-slate-700/50 cursor-pointer z-20 backdrop-blur-md"
                    :title="isPlaying ? 'Sluk ambient lyd' : 'Tænd ambient lyd'"
                >
                    <span x-text="isPlaying ? '🔊' : '🔇'"></span>
                    <span x-text="isPlaying ? 'Ambient Lyd' : 'Lyd Fra'" class="text-[11px] text-slate-400 hidden sm:inline"></span>
                </button>

                {{-- TRIN 0: INGEN TABELLER --}}
                @if(! $hasDatabaseTables)
                    
                    <div 
                        x-data="{ 
                            migrating: false,
                            progressMigrate: 0,
                            statusMigrateText: 'Klargør din nye arbejdsplads...',
                            startInstallation() {
                                this.migrating = true;
                                this.progressMigrate = 10;
                                
                                let timer = setInterval(() => {
                                    if (this.progressMigrate < 85) {
                                        this.progressMigrate += Math.floor(Math.random() * 8) + 3;
                                        if (this.progressMigrate > 30 && this.progressMigrate < 60) {
                                            this.statusMigrateText = 'Opbygger struktur og databasetabeller...';
                                        } else if (this.progressMigrate >= 60) {
                                            this.statusMigrateText = 'Konfigurerer sikkerhed og admin-bruger...';
                                        }
                                    }
                                }, 300);

                                $wire.executeSystemInstallation()
                                    .then((success) => {
                                        clearInterval(timer);
                                        if (success) {
                                            this.progressMigrate = 100;
                                            this.statusMigrateText = 'Alt er klar!';
                                            setTimeout(() => {
                                                window.location.reload();
                                            }, 800);
                                        } else {
                                            this.migrating = false;
                                        }
                                    })
                                    .catch((err) => {
                                        clearInterval(timer);
                                        this.migrating = false;
                                    });
                            }
                        }"
                        class="space-y-8"
                    >
                        <div class="mx-auto w-20 h-20 bg-gradient-to-tr from-slate-800 to-slate-700 rounded-3xl flex items-center justify-center shadow-2xl border border-slate-600/30">
                            <span class="text-4xl"></span>
                        </div>

                        <div class="space-y-2">
                            <h2 class="text-2xl sm:text-3xl font-semibold text-white tracking-tight">
                                Velkommen.
                            </h2>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                                Lad os konfigurere systemet og klargøre din nye digitale arbejdsplads.
                            </p>
                        </div>

                        <div x-show="!migrating" class="pt-2">
                            <button 
                                type="button" 
                                @click="startInstallation()"
                                class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-white hover:bg-slate-100 text-slate-900 font-semibold text-xs transition-all duration-200 transform hover:scale-[1.02] shadow-2xl cursor-pointer"
                            >
                                Installér System Nu
                            </button>
                        </div>

                        <div x-show="migrating" x-cloak class="space-y-4 pt-4 max-w-xs mx-auto">
                            <div class="w-full bg-slate-800/80 rounded-full h-1.5 overflow-hidden p-0.5 border border-slate-700/40 shadow-inner">
                                <div 
                                    class="bg-gradient-to-r from-blue-500 via-indigo-400 to-white h-full rounded-full transition-all duration-300 ease-out shadow-sm"
                                    :style="'width: ' + progressMigrate + '%'"
                                ></div>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium animate-pulse" x-text="statusMigrateText"></p>
                        </div>

                    </div>

                {{-- TRIN 1: VALG AF OPSTART --}}
                @else

                    <div>
                        <div x-show="!unboxing" class="space-y-8">
                            
                            <div class="mx-auto w-20 h-20 bg-gradient-to-tr from-slate-800 to-slate-700 rounded-3xl flex items-center justify-center text-3xl shadow-2xl border border-slate-600/30">
                                <span>✨</span>
                            </div>

                            <div class="space-y-2">
                                <h2 class="text-2xl sm:text-3xl font-semibold text-white tracking-tight">
                                    Næsten i mål.
                                </h2>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                                    Vælg hvordan du vil starte din nye sagsbehandlingsløsning.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-left pt-2">
                                
                                <button 
                                    type="button" 
                                    @click="stopAudio()"
                                    wire:click="startFresh"
                                    class="p-5 rounded-2xl bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/50 hover:border-slate-500 transition-all duration-200 group flex flex-col justify-between cursor-pointer"
                                >
                                    <div>
                                        <div class="text-2xl mb-3">🌱</div>
                                        <div class="font-semibold text-xs text-white group-hover:text-blue-400 transition">
                                            Tom database
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                                            Start fra bunden med en ren og tom arbejdsplads.
                                        </p>
                                    </div>
                                    <span class="mt-4 text-[10px] font-semibold text-slate-500 group-hover:text-blue-400 transition">Start tom &rarr;</span>
                                </button>

                                <button 
                                    type="button" 
                                    @click="stopAudio()"
                                    wire:click="goToImport"
                                    class="p-5 rounded-2xl bg-slate-800/40 hover:bg-slate-800/80 border border-slate-700/50 hover:border-slate-500 transition-all duration-200 group flex flex-col justify-between cursor-pointer"
                                >
                                    <div>
                                        <div class="text-2xl mb-3">📥</div>
                                        <div class="font-semibold text-xs text-white group-hover:text-blue-400 transition">
                                            Importér data
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                                            Overfør sager og kreditorer fra dit tidligere system.
                                        </p>
                                    </div>
                                    <span class="mt-4 text-[10px] font-semibold text-slate-500 group-hover:text-blue-400 transition">Gå til import &rarr;</span>
                                </button>

                                <button 
                                    type="button" 
                                    @click.prevent="startUnboxing()"
                                    class="p-5 rounded-2xl bg-gradient-to-b from-blue-600/20 to-indigo-600/20 hover:from-blue-600/30 hover:to-indigo-600/30 border border-blue-500/40 hover:border-blue-400 transition-all duration-200 group flex flex-col justify-between cursor-pointer shadow-lg relative overflow-hidden"
                                >
                                    <div>
                                        <div class="text-2xl mb-3">📦</div>
                                        <div class="font-semibold text-xs text-white group-hover:text-blue-300 transition">
                                            Kør demo-data
                                        </div>
                                        <p class="text-[11px] text-slate-300/80 mt-1 leading-relaxed">
                                            Pak en færdig arbejdsplads ud med sager og demo-brugere.
                                        </p>
                                    </div>
                                    <span class="mt-4 text-[10px] font-bold text-blue-300 group-hover:text-white transition">Unbox demo &rarr;</span>
                                </button>

                            </div>

                        </div>

                        {{-- UNBOXING SCREEN --}}
                        <div x-show="unboxing" x-cloak class="space-y-8 py-8 animate-in fade-in zoom-in-95 duration-500">
                            
                            <div class="mx-auto w-24 h-24 bg-gradient-to-tr from-slate-800 to-slate-700 rounded-3xl flex items-center justify-center text-4xl shadow-2xl border border-slate-600/40 relative">
                                <span>🎁</span>
                                <div class="absolute -inset-1 rounded-3xl bg-blue-500/20 blur-xl -z-10 animate-pulse"></div>
                            </div>

                            <div class="space-y-2">
                                <h3 class="text-xl sm:text-2xl font-semibold text-white tracking-tight" x-text="statusText"></h3>
                                <p class="text-xs text-slate-400">Gør alting klar...</p>
                            </div>

                            <div class="space-y-3 max-w-xs mx-auto pt-2">
                                <div class="w-full bg-slate-800/80 rounded-full h-1.5 overflow-hidden p-0.5 border border-slate-700/50 shadow-inner">
                                    <div 
                                        class="bg-gradient-to-r from-blue-500 via-indigo-400 to-white h-full rounded-full transition-all duration-300 ease-out shadow-sm"
                                        :style="'width: ' + progress + '%'"
                                    ></div>
                                </div>
                                <div class="text-[11px] font-mono text-slate-500 tracking-widest" x-text="progress + '%'"></div>
                            </div>

                        </div>

                    </div>

                @endif

            </div>
        </div>
    @endif
</div>