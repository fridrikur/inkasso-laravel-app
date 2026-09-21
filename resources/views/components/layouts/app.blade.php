<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('app_name', 'Sagsbehandling') }} - {{ $title ?? 'Dashboard' }}</title>

    {{-- DYNAMISK FARVETEMA FRA SYSTEMSETTINGS --}}
    <style>    
        :root {
            --theme-primary: {{ setting('theme_primary', '#4f46e5') }};
            --theme-sidebar-bg: {{ setting('theme_sidebar_bg', '#0f172a') }};
            --theme-sag-editor-bg: {{ setting('theme_sag_editor_bg', '#ffffff') }};
            --theme-sag-editor-wrapper-bg: {{ setting('theme_sag_editor_wrapper_bg', '#f1f5f9') }};
            --theme-sag-editor-header: {{ setting('theme_sag_editor_header', '#4f46e5') }};
        }
        [x-cloak] { display: none !important; }
    </style>

    {{-- CHART.JS GLOBAL IMPORT --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body 
    x-data="{ 
        sidebarOpen: window.innerWidth >= 1024,
        toggleSidebar() { this.sidebarOpen = !this.sidebarOpen; }
    }"
    class="bg-slate-100 font-sans antialiased text-slate-900 min-h-screen flex flex-col"
>

    {{-- ⚠️ GLOBAL SANDKASSE BANNER (VISES PÅ ALLE SIDER) --}}
    @if(setting('environment', 'sandbox') === 'sandbox')
        <div class="bg-amber-400 text-amber-950 font-bold text-xs sm:text-sm py-2 px-4 text-center shadow-sm z-50 flex items-center justify-center gap-2 w-full">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 animate-pulse shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="tracking-wide uppercase">⚠️ Kører i Sandkasse tilstand</span>
        </div>
    @endif

    {{-- 2-TRINS INAKTIVITETS & RE-AUTH MODAL --}}
    <div id="session-warning"
         style="display:none;"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-md flex items-center justify-center z-50">

        <div class="bg-white rounded-3xl p-6 shadow-2xl w-full max-w-md text-center border border-slate-100 space-y-4">
            
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
                        class="w-full bg-[var(--theme-primary)] hover:opacity-90 text-white font-semibold px-4 py-2.5 rounded-xl transition shadow-sm cursor-pointer text-sm">
                    Fortsæt med at være logget på
                </button>
            </div>

            <div id="modal-step-reauth" class="space-y-4" style="display:none;">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-[var(--theme-primary)]/10 text-[var(--theme-primary)] flex items-center justify-center">
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
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-[var(--theme-primary)] focus:ring-4 focus:ring-[var(--theme-primary)]/10 outline-none text-center">
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
            :class="sidebarOpen ? 'w-64 translate-x-0 opacity-100' : 'w-0 -translate-x-full opacity-0 pointer-events-none'"
            class="fixed inset-y-0 left-0 z-40 bg-[var(--theme-sidebar-bg)] text-white transition-all duration-300 ease-in-out lg:static shrink-0 overflow-y-auto flex flex-col justify-between shadow-2xl border-r border-slate-800"
        >
            <div class="p-5 space-y-6 w-64">
                {{-- LOGO OG OVERSKRIFT --}}
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-[var(--theme-primary)] text-white font-bold text-base shadow-sm">⚖️</span>
                        <div>
                            <span class="font-bold text-base tracking-wide text-white block">{{ setting('app_name', 'InkassoApp') }}</span>
                            <span class="text-[10px] text-slate-400 uppercase tracking-wider block">
                                @role('Admin') Admin Portal @else Medarbejder Portal @endrole
                            </span>
                        </div>
                    </div>
                    <button @click="toggleSidebar" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition cursor-pointer" title="Skjul menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    </button>
                </div>

                <nav class="space-y-6 text-sm font-medium">

                    {{-- ============================================================ --}}
                    {{-- 💼 MEDARBEJDER MENU (Viser rigtige medarbejder-ruter) --}}
                    {{-- ============================================================ --}}
                    @hasrole('Medarbejder')
                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Oversigt</div>
                            <a href="{{ route('medarbejder.dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('medarbejder.dashboard') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>📊</span> Dashboard
                            </a>
                        </div>

                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sagsbehandling</div>
                            <a href="{{ route('medarbejder.sager.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('medarbejder.sager.index') || request()->routeIs('medarbejder.sager.edit') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>📂</span> Sager
                            </a>
                            <a href="{{ route('medarbejder.sager.search') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('medarbejder.sager.search') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>🔍</span> Søg Sager
                            </a>
                        </div>
                    @endhasrole

                    {{-- ============================================================ --}}
                    {{-- 👑 ADMIN MENU --}}
                    {{-- ============================================================ --}}
                    @role('Admin')
                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Oversigt</div>
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('dashboard*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>📊</span> Dashboard
                            </a>
                        </div>

                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sagsbehandling</div>
                            
                            {{-- 🟢 SAGER DROPDOWN (INKL. DEBITORER, STATUS, DROPDOWN TEKSTER M.M.) --}}
                            <div class="relative pt-1" x-data="{ sagerOpen: {{ request()->routeIs('sager.*') || request()->routeIs('admin.sager.status.*') || request()->routeIs('dropdowns.*') || request()->routeIs('debitorer.*') ? 'true' : 'false' }} }">
                                <button 
                                    @click="sagerOpen = !sagerOpen" 
                                    class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ request()->routeIs('sager.*') || request()->routeIs('admin.sager.status.*') || request()->routeIs('dropdowns.*') || request()->routeIs('debitorer.*') ? 'bg-[var(--theme-primary)] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                >
                                    <div class="flex items-center gap-3">
                                        <span>📂</span>
                                        <span>Sager</span>
                                    </div>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{'rotate-180': sagerOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div 
                                    x-show="sagerOpen" 
                                    x-cloak
                                    class="mt-1.5 w-full bg-slate-900 border border-slate-800 rounded-xl shadow-xl py-2 z-50 space-y-1 text-xs"
                                >
                                    <a href="{{ route('sager.index') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('sager.index') || request()->routeIs('sager.edit') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>📂</span> Alle sager
                                    </a>
                                    <a href="{{ route('debitorer.index') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('debitorer.*') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>👥</span> Debitorer
                                    </a>
                                    <a href="{{ route('admin.sager.status.index') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('admin.sager.status.*') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>🏷️</span> Sagsstatus
                                    </a>
                                    <a href="{{ route('dropdowns.index') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('dropdowns*') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>💬</span> Dropdown tekster
                                    </a>
                                    <a href="{{ route('sager.search') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('sager.search') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>🔍</span> Søg Sager
                                    </a>
                                    <a href="{{ route('sager.papirkurv') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('sager.papirkurv') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>🗑️</span> Papirkurv
                                    </a>
                                </div>
                            </div>

                            {{-- BREVE DROPDOWN --}}
                            <div class="relative pt-1" x-data="{ breveOpen: {{ request()->routeIs('admin.breve.*') || request()->routeIs('breve.*') ? 'true' : 'false' }} }">
                                <button 
                                    @click="breveOpen = !breveOpen" 
                                    class="w-full flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ request()->routeIs('admin.breve.*') || request()->routeIs('breve.*') ? 'bg-[var(--theme-primary)] text-white font-bold shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
                                >
                                    <div class="flex items-center gap-3">
                                        <span>✉️</span>
                                        <span>Breve</span>
                                    </div>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{'rotate-180': breveOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div 
                                    x-show="breveOpen" 
                                    x-cloak
                                    class="mt-1.5 w-full bg-slate-900 border border-slate-800 rounded-xl shadow-xl py-2 z-50 space-y-1 text-xs"
                                >
                                    <a href="{{ route('admin.breve.rediger') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('admin.breve.rediger') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>✏️</span> Rediger skabeloner
                                    </a>
                                    <a href="{{ route('breve.filter') }}" 
                                       class="flex items-center gap-2.5 px-4 py-2 font-semibold transition {{ request()->routeIs('breve.filter') ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                        <span>⚙️</span> Sortering & synlige felter
                                    </a>
                                </div>
                            </div>

                            <a href="{{ route('sager.import.log') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('sager.import.log') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} text-xs font-semibold">
                                <span>📊</span> Import Log
                            </a>
                        </div>

                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Parter</div>
                            <a href="{{ route('kreditorer.index') }}" 
                            wire:navigate
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('kreditorer*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>🏢</span> Kreditorer
                            </a>
                            <a href="{{ route('konsulenter.manage-konsulenter') }}" 
                            wire:navigate
                            class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('manage-konsulenter') || request()->routeIs('konsulenter*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>💼</span> Konsulenter
                            </a>
                        </div>

                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Værktøjer & Admin</div>
                            <a href="{{ route('sager.doctor') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('sager.doctor') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>🩺</span> Doctor Norton 3.0
                            </a>
                            <a href="{{ route('gdpr.sager.retention') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('gdpr*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>🛡️</span> GDPR Retention
                            </a>
                            <a href="{{ route('autotekster.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('autotekster*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>💬</span> Autotekster
                            </a>
                            <a href="{{ route('users.manage-users') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('users*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>👤</span> Brugere & Roles
                            </a>
                            <a href="{{ route('admin.system-settings.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('admin.system-settings.*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>⚙️</span> Systemindstillinger
                            </a>
                            <a href="{{ route('backups.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('backups*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>💾</span> Backups
                            </a>
                        </div>
                    @endrole
                    {{-- ============================================================ --}}
                    {{-- 🏢 KREDITOR MENU --}}
                    {{-- ============================================================ --}}
                    @hasrole('Kreditor')
                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kreditor Overblik</div>
                            <a href="{{ route('kreditor.dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('kreditor.dashboard') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>📊</span> Dashboard
                            </a>
                        </div>

                        <div class="space-y-1">
                            <div class="px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sagsstyring</div>
                            <a href="{{ route('kreditor.sager.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('kreditor.sager.index') || request()->routeIs('kreditor.sag.*') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>📂</span> Alle sager
                            </a>
                            <a href="{{ route('kreditor.search') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('kreditor.search') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>🔍</span> Søg i sager
                            </a>
                            <a href="{{ route('kreditor.sag.create') }}" 
                               class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition {{ request()->routeIs('kreditor.sag.create') ? 'bg-[var(--theme-primary)] text-white font-bold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <span>➕</span> Opret ny sag
                            </a>
                        </div>
                    @endhasrole
                </nav>
            </div>

            {{-- BUND BRUGER-PROFIL --}}
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40 w-64">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-[var(--theme-primary)]/20 text-[var(--theme-primary)] font-bold flex items-center justify-center border border-[var(--theme-primary)]/30 shrink-0">
                        {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="truncate text-xs">
                        <p class="font-bold text-slate-200 truncate">{{ auth()->user()?->name ?? 'Bruger' }}</p>
                        <p class="text-slate-500 truncate">{{ auth()->user()?->email ?? '' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- HOVED INDHOLDSOMRÅDE --}}
        <div class="flex-1 flex flex-col min-w-0 w-full">

            <header class="bg-white border-b border-slate-200 px-4 sm:px-6 py-2.5 flex items-center justify-between sticky top-0 z-20 shadow-sm">
                
                <div class="flex items-center gap-4">
                    <button 
                        @click="toggleSidebar"
                        type="button"
                        class="p-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 transition flex items-center gap-2 text-xs font-bold shadow-sm cursor-pointer"
                        title="Åbn/Skjul hovedmenu"
                    >
                        <svg class="w-4 h-4 text-[var(--theme-primary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <span x-text="sidebarOpen ? 'Skjul menu' : 'Hovedmenu'">Hovedmenu</span>
                    </button>

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

                <div class="flex items-center gap-3">
                    
                    @auth
                        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                            <div class="w-6 h-6 rounded-lg bg-[var(--theme-primary)] text-white font-bold text-[10px] flex items-center justify-center shrink-0">
                                {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <span class="font-bold text-slate-800 block leading-tight">{{ auth()->user()?->name ?? 'Bruger' }}</span>
                                <span class="text-[10px] text-[var(--theme-primary)] font-semibold block leading-tight">
                                    {{ auth()->user()?->getRoleNames()?->first() ?? 'Bruger' }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                    <div x-data="{ 
                        seconds: {{ $sessionSeconds ?? 0 }},
                        formatTime(sec) {
                            let h = String(Math.floor(sec / 3600)).padStart(2, '0');
                            let m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
                            let s = String(sec % 60).padStart(2, '0');
                            return `${h}:${m}:${s}`;
                        }
                    }" 
                    x-init="setInterval(() => seconds++, 1000)"
                    class="hidden rounded-xl bg-slate-100 px-3.5 py-2 text-sm text-slate-600 sm:block">
                        Session:
                        <span x-text="formatTime(seconds)" class="font-mono font-bold text-slate-900">00:00:00</span>
                    </div>
                </div>
                    @endauth

                    @role('Admin')
                        <button
                            @click="$dispatch('open-quick-menu')"
                            type="button"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-[var(--theme-primary)] hover:opacity-90 px-3.5 py-2 text-xs font-bold text-white transition shadow-sm cursor-pointer"
                        >
                            <span>⚡ Quick Menu</span>
                        </button>
                        <x-performance-badge />
                    @endrole

                    @auth
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
                    @endauth

                </div>
            </header>

            <main class="flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
                {{ $slot ?? $appSlot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
    <livewire:admin.quick-menu />
    @livewireScripts
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        let idleTimeout = 15 * 60 * 1000;
        let countdownInterval;
        let warningTimer;
        let reauthTimer;

        function resetIdleTimers() {
            clearTimeout(warningTimer);
            clearTimeout(reauthTimer);
            clearInterval(countdownInterval);

            document.getElementById('session-warning').style.display = 'none';
            document.getElementById('modal-step-warning').style.display = 'block';
            document.getElementById('modal-step-reauth').style.display = 'none';

            warningTimer = setTimeout(showWarningModal, idleTimeout - 30000);
        }

        function showWarningModal() {
            const modal = document.getElementById('session-warning');
            const countdownEl = document.getElementById('countdown');
            let timeLeft = 30;

            modal.style.display = 'flex';
            countdownEl.innerText = timeLeft;

            countdownInterval = setInterval(() => {
                timeLeft--;
                countdownEl.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    triggerLockout();
                }
            }, 1000);
        }

        function extendSession() {
            fetch('/_ignition/health-check', { method: 'GET' }).catch(() => {});
            resetIdleTimers();
        }

        function triggerLockout() {
            document.getElementById('modal-step-warning').style.display = 'none';
            document.getElementById('modal-step-reauth').style.display = 'block';
            
            fetch('{{ route("logout") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        }

        window.extendSession = extendSession;

        window.addEventListener('mousemove', resetIdleTimers);
        window.addEventListener('mousedown', resetIdleTimers);
        window.addEventListener('keypress', function(e) {
            resetIdleTimers();
        });
        window.addEventListener('scroll', resetIdleTimers);
        window.addEventListener('touchstart', resetIdleTimers);

        resetIdleTimers();
    });

    function reAuthenticate(event) {
        event.preventDefault();
        const password = document.getElementById('re-auth-password').value;
        const errorEl = document.getElementById('re-auth-error');
        const btn = document.getElementById('re-auth-btn');

        btn.disabled = true;
        btn.innerText = 'Logger ind...';
        errorEl.style.display = 'none';

        fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: '{{ auth()->user()?->email ?? "" }}',
                password: password
            })
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                return response.json().then(data => {
                    throw new Error(data.message || 'Forkert adgangskode.');
                });
            }
        })
        .catch(error => {
            errorEl.innerText = error.message;
            errorEl.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'Lås op og fortsæt';
        });
    }
    </script>
</body>
</html>