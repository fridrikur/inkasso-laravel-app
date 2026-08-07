<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Sagsbehandling' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    {{-- CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body 
    x-data="{ 
        sidebarOpen: {{ request()->routeIs('dashboard*') ? '(window.innerWidth >= 1024)' : 'false' }},
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; }
    }"
    class="bg-slate-100 font-sans antialiased text-slate-900 min-h-screen flex flex-col"
>

    {{-- 2-TRINS INAKTIVITETS & RE-AUTH MODAL --}}
    <div id="session-warning"
         style="display:none;"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-md flex items-center justify-center z-50">

        <div class="bg-white rounded-3xl p-6 shadow-2xl w-full max-w-md text-center border border-slate-100 space-y-4">
            
            {{-- TRIN 1: ADVARSEL (Før de 30 sekunder udløber) --}}
            <div id="modal-step-warning" class="space-y-4">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Sessionen udløber snart</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Du bliver logget ud om 
                        <span id="countdown" class="font-mono font-bold text-rose-600">30</span> 
                        sekunder pga. inaktivitet.
                    </p>
                </div>

                <button type="button" 
                        onclick="extendSession()"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-xl transition shadow-sm cursor-pointer text-sm">
                    Fortsæt med at være logget på
                </button>
            </div>

            {{-- TRIN 2: LÅST SKÆRM / RE-AUTH (Efter de 30 sekunder er løbet ud) --}}
            <div id="modal-step-reauth" class="space-y-4" style="display:none;">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <div>
                    <h2 class="text-lg font-bold text-slate-900">Du er blevet logget ud</h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Skriv din adgangskode for at fortsætte direkte, hvor du slap.
                    </p>
                </div>

                <form onsubmit="reAuthenticate(event)" class="space-y-3">
                    <div class="text-left">
                        <input type="password" 
                               id="re-auth-password"
                               placeholder="Indtast din adgangskode"
                               required
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none text-center">
                        <p id="re-auth-error" class="text-xs text-rose-600 font-semibold mt-1 text-center" style="display:none;"></p>
                    </div>

                    <button type="submit" 
                            id="re-auth-btn"
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white font-semibold px-4 py-2.5 rounded-xl transition shadow-sm flex items-center justify-center gap-2 cursor-pointer text-sm">
                        <span>Lås op og fortsæt</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- HOVED LAYOUT WRAPPER --}}
    <div class="min-h-screen flex w-full relative overflow-x-hidden">

        {{-- MOBIL OVERLAY (Kun til mobil/tablet) --}}
        <div 
            x-show="sidebarOpen" 
            x-cloak
            x-transition.opacity
            @click="sidebarOpen = false" 
            class="fixed inset-0 z-30 bg-slate-950/40 lg:hidden"
        ></div>

        {{-- VENSTRE SIDEBAR MENU --}}
        <aside 
            x-cloak
            :class="sidebarOpen ? 'w-64 translate-x-0 opacity-100' : 'w-0 -translate-x-full opacity-0 pointer-events-none lg:w-0'"
            class="fixed inset-y-0 left-0 z-40 bg-slate-900 text-white transition-all duration-300 ease-in-out lg:static shrink-0 overflow-y-auto flex flex-col justify-between shadow-2xl border-r border-slate-800"
        >
            <div class="p-5 space-y-6 w-64">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-indigo-600 text-white font-bold text-base shadow-sm">⚖️</span>
                        <div>
                            <span class="font-bold text-base tracking-wide text-white block">InkassoApp</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block">Sagsadministration</span>
                        </div>
                    </div>
                    <button @click="toggleSidebar" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer" title="Skjul menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    </button>
                </div>

                <nav class="space-y-6 text-sm font-medium">
                    <div class="space-y-1">
                        <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Oversigt</div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('dashboard*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📊</span> Dashboard
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sagsbehandling</div>
                        <a href="{{ route('showsager') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('showsager') || request()->routeIs('sager.*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📂</span> Sager
                        </a>
                        <a href="{{ route('sager.search') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('sager.search') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🔍</span> Søg Sager
                        </a>
                        <a href="{{ route('sager.trash') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('sager.trash') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🗑️</span> Papirkurv
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Parter</div>
                        <a href="{{ route('kreditorer.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('kreditorer*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🏢</span> Kreditorer
                        </a>
                        <a href="{{ route('sagsbehandlere.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('sagsbehandlere*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>👨‍💼</span> Sagsbehandlere
                        </a>
                        <a href="{{ route('manage-konsulenter') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('manage-konsulenter') || request()->routeIs('konsulenter*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>💼</span> Konsulenter
                        </a>
                    </div>

                    <div class="space-y-1">
                        <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Værktøjer & Admin</div>
                        <a href="{{ route('sager.doctor') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('sager.doctor') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🩺</span> Doctor Norton 3.0
                        </a>
                        <a href="{{ route('gdpr.sager.retention') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('gdpr*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>🛡️</span> GDPR Retention
                        </a>
                        <a href="{{ route('autotekster.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('autotekster*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>💬</span> Autotekster
                        </a>
                        <a href="{{ route('dropdowns.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('dropdowns*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>📋</span> Dropdown felter
                        </a>
                        <a href="{{ route('users.manage-users') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('users*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>👤</span> Brugere & Roles
                        </a>
                        <a href="{{ route('backups.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('backups*') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>💾</span> Backups
                        </a>
                    </div>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40 w-64">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 font-bold flex items-center justify-center border border-indigo-500/30 shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="truncate text-xs">
                        <p class="font-bold text-slate-200 truncate">{{ auth()->user()->name ?? 'Bruger' }}</p>
                        <p class="text-slate-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- HOVED INDHOLDSOMRÅDE --}}
        <div class="flex-1 flex flex-col min-w-0 w-full">
            
            {{-- 🟢 OPGRADERET TOPHEADER --}}
            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-2.5 flex items-center justify-between sticky top-0 z-20 shadow-sm">
                
                {{-- VENSTRE SIDE: MENU KNAP & DATO/KLOKKESLÆT --}}
                <div class="flex items-center gap-4">
                    <button 
                        @click="toggleSidebar"
                        type="button"
                        class="p-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 transition flex items-center gap-2 text-xs font-bold shadow-sm cursor-pointer"
                        title="Åbn/Skjul hovedmenu"
                    >
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span x-text="sidebarOpen ? 'Skjul menu' : 'Hovedmenu'">Hovedmenu</span>
                    </button>

                    {{-- TEMPUS FUGIT / DATO OG REALTIDS-UR --}}
                    <div 
                        x-data="{ 
                            time: '', 
                            updateClock() { 
                                const now = new Date(); 
                                this.time = now.toLocaleTimeString('da-DK', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); 
                            } 
                        }" 
                        x-init="updateClock(); setInterval(() => updateClock(), 1000)"
                        class="hidden md:flex items-center gap-2 border-l border-slate-200 pl-4 text-xs text-slate-500"
                    >
                        <span class="text-slate-400">📅</span>
                        <span class="font-medium text-slate-700">
                            {{ \Carbon\Carbon::now()->locale('da')->isoFormat('dddd [d.] D. MMM YYYY') }}
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-400">⏰</span>
                        <span x-text="time" class="font-mono font-bold text-slate-800">00:00:00</span>
                    </div>
                </div>

                {{-- SESSION NEDTÆLLING I HEADER --}}
<div 
    x-data="{ 
        timeLeft: 900,
        timer: null,
        formatTime(sec) {
            let m = String(Math.floor(sec / 60)).padStart(2, '0');
            let s = String(sec % 60).padStart(2, '0');
            return `${m}:${s}`;
        },
        resetTimer() {
            let modal = document.getElementById('session-warning');
            if (!modal || modal.style.display === 'none') {
                this.timeLeft = 900;
            }
        }
    }"
    x-init="
        timer = setInterval(() => { if (timeLeft > 0) timeLeft--; }, 1000);
        ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => {
            window.addEventListener(evt, () => resetTimer());
        });
    "
    class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600"
>
    <span class="text-slate-400">⏳</span>
    <span>Session udløber:</span>
    <span x-text="formatTime(timeLeft)" 
          :class="timeLeft < 60 ? 'text-rose-600 animate-pulse font-bold' : 'text-slate-900 font-mono font-bold'">
        15:00
    </span>
</div>

                {{-- HØJRE SIDE: BRUGERINFO, SESSION, QUICK MENU & LOG UD --}}
                <div class="flex items-center gap-3">
                    
                    {{-- BRUGER BADGE MED ROLLE --}}
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                        <div class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <span class="font-bold text-slate-800 block leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-indigo-600 font-semibold block leading-tight">
                                {{ auth()->user()->getRoleNames()->first() ?? 'Bruger' }}
                            </span>
                        </div>
                    </div>

                    {{-- LIVEWIRE SESSION MANAGER / TIMER --}}
                    <livewire:session-manager />

                    {{-- QUICK MENU KNAP --}}
                    <button
                        @click="$dispatch('open-quick-menu')"
                        type="button"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-indigo-700 transition shadow-sm cursor-pointer"
                    >
                        <span>⚡ Quick Menu</span>
                        <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>

                    {{-- 🔴 LOG AF KNAP --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 hover:border-rose-300 font-bold text-xs transition shadow-sm cursor-pointer"
                            title="Log af systemet"
                        >
                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden md:inline">Log af</span>
                        </button>
                    </form>

                </div>
            </header>

            {{-- MAIN PAGE CONTAINER --}}
            <main class="flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
                {{ $slot ?? $appSlot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    <livewire:admin.quick-menu />

    @livewireScripts

    {{-- INAKTIVITETS TIMEOUT & RE-AUTH JAVASCRIPT --}}
    <script>
    (function() {
        let totalTimeout = 900; // 15 minutter i sekunder
        let warningBefore = 30; // 30 sekunders advarsel

        let warningTime = totalTimeout - warningBefore;
        let countdownInterval;
        let countdown = warningBefore;
        let inactivityTimer;

        function startInactivityTimer() {
            clearTimeout(inactivityTimer);
            clearInterval(countdownInterval);

            let modal = document.getElementById('session-warning');
            let step1 = document.getElementById('modal-step-warning');
            let step2 = document.getElementById('modal-step-reauth');

            if (modal) modal.style.display = 'none';
            if (step1) step1.style.display = 'block';
            if (step2) step2.style.display = 'none';

            inactivityTimer = setTimeout(() => {
                countdown = warningBefore;
                if (modal) modal.style.display = 'flex';

                countdownInterval = setInterval(() => {
                    countdown--;
                    let countElem = document.getElementById('countdown');
                    if (countElem) countElem.innerText = countdown;

                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        if (step1) step1.style.display = 'none';
                        if (step2) step2.style.display = 'block';
                    }
                }, 1000);

            }, warningTime * 1000);
        }

        // Nulstil kun timeren ved aktivitet hvis modalen IKKE er synlig
        ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => {
            window.addEventListener(evt, () => {
                let modal = document.getElementById('session-warning');
                if (!modal || modal.style.display === 'none') {
                    startInactivityTimer();
                }
            });
        });

        startInactivityTimer();

        // FORHINDR LIVEWIRE I AT GENINDLÆSE SIDEN VED SESSION TIMEOUT
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 401 || status === 419) {
                        preventDefault();

                        let modal = document.getElementById('session-warning');
                        let step1 = document.getElementById('modal-step-warning');
                        let step2 = document.getElementById('modal-step-reauth');

                        if (modal) modal.style.display = 'flex';
                        if (step1) step1.style.display = 'none';
                        if (step2) step2.style.display = 'block';
                    }
                });
            });
        });

        window.extendSession = function() {
            fetch('/keep-alive', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(res => {
                if (res.ok) {
                    startInactivityTimer();
                } else {
                    let step1 = document.getElementById('modal-step-warning');
                    let step2 = document.getElementById('modal-step-reauth');
                    if (step1) step1.style.display = 'none';
                    if (step2) step2.style.display = 'block';
                }
            });
        };

        window.reAuthenticate = async function(event) {
            event.preventDefault();

            let passwordInput = document.getElementById('re-auth-password');
            let errorElem = document.getElementById('re-auth-error');
            let btn = document.getElementById('re-auth-btn');

            errorElem.style.display = 'none';
            btn.disabled = true;

            try {
                const tokenResponse = await fetch('/refresh-csrf');
                const tokenData = await tokenResponse.json();
                const newToken = tokenData.token;

                let csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfMeta) csrfMeta.setAttribute('content', newToken);

                const response = await fetch('/re-authenticate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': newToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: passwordInput.value })
                });

                const data = await response.json();
                btn.disabled = false;

                if (response.ok && data.success) {
                    passwordInput.value = '';
                    window.location.reload();
                } else {
                    errorElem.innerText = data.message || 'Forkert adgangskode. Prøv igen.';
                    errorElem.style.display = 'block';
                }
            } catch (error) {
                btn.disabled = false;
                errorElem.innerText = 'Der opstod en netværksfejl. Prøv igen.';
                errorElem.style.display = 'block';
            }
        };
    })();
    </script>

    {{-- GLOBAL TOAST CONTAINER --}}
    <div 
        x-data="{ 
            toasts: [],
            add(event) {
                let data = event.detail || event;
                if (Array.isArray(data)) data = data[0];
                
                const id = Date.now();
                const toast = {
                    id: id,
                    message: data.message || 'Handling gennemført',
                    type: data.type || 'info',
                    icon: data.icon || 'check'
                };
                this.toasts.push(toast);
                setTimeout(() => this.remove(id), 5000);
            },
            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }
        }"
        @toast.window="add($event)"
        x-init="
            @if(session()->has('toast'))
                add({ detail: {{ json_encode(session('toast')) }} });
            @endif
        "
        class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                x-show="true"
                x-transition:enter="transition ease-out duration-300 transform translate-y-2 opacity-0"
                x-transition:enter-end="transform translate-y-0 opacity-100"
                x-transition:leave="transition ease-in duration-200 transform translate-y-2 opacity-0"
                class="pointer-events-auto p-4 rounded-2xl shadow-xl border flex items-center justify-between gap-3 text-xs font-semibold"
                :class="{
                    'bg-emerald-900 text-white border-emerald-800': toast.type === 'success',
                    'bg-rose-900 text-white border-rose-800': toast.type === 'error',
                    'bg-amber-900 text-white border-amber-800': toast.type === 'warning',
                    'bg-slate-900 text-white border-slate-800': toast.type === 'info'
                }"
            >
                <div class="flex items-center gap-2.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </template>
                    <span x-text="toast.message"></span>
                </div>
                <button @click="remove(toast.id)" class="opacity-60 hover:opacity-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>
</body>
</html>