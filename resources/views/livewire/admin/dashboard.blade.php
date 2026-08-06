<div class="space-y-6" wire:init="loadDashboard">

    {{-- HEADER BANNER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Admin Dashboard
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Overblik over sager, kreditorer, GDPR, brugere og systemstatus
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-semibold text-slate-600 sm:block">
                Session:
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
                class="hidden rounded-lg bg-slate-100 px-3 py-2 text-sm text-slate-600 sm:block">
                Session:
                <span x-text="formatTime(seconds)" class="font-mono font-bold text-slate-900">00:00:00</span>
            </div>
            </div>

            <button
                type="button"
                wire:click="toggleQuickMenu"
                class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-700 cursor-pointer"
            >
                Quick Menu
            </button>
        </div>
    </div>

    {{-- GDPR ADVARSEL BANNER --}}
    @if ($gdprExpiredCount > 0)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-600 text-white shadow-sm shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-rose-900">
                        GDPR Advarsel: {{ $gdprExpiredCount }} {{ $gdprExpiredCount === 1 ? 'sag er' : 'sager er' }} over 5-års grænsen
                    </h3>
                    <p class="text-xs text-rose-700">
                        Disse sager indeholder personhenførbare oplysninger og bør anonymiseres eller slettes jf. persondatareglerne.
                    </p>
                </div>
            </div>

            <a href="{{ route('gdpr.sager.retention') }}" 
               class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700 transition shrink-0">
                <span>Gå til GDPR-behandling</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    @endif

    {{-- Lazy loading state --}}
    @if (! $readyToLoad)
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-indigo-200 border-t-indigo-600"></div>

            <h2 class="text-lg font-semibold text-slate-900">
                Loading dashboard
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Henter sager, kreditorer, GDPR-statistik og systemdata...
            </p>
        </div>
    @else
        {{-- Quick stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total sager</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">
                    {{ number_format($totalSager, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total kreditorer</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">
                    {{ number_format($totalKreditorer, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm">
                <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">GDPR udløbet</p>
                <p class="mt-2 text-3xl font-bold text-red-700">
                    {{ number_format($gdprExpired, 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">GDPR udløber snart</p>
                <p class="mt-2 text-3xl font-bold text-amber-800">
                    {{ number_format($gdprExpiring, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Fane-navigation --}}
        @include('livewire.admin.partials.dashboard-tabs')

        {{-- Fane-indhold container --}}
        <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
            
            {{-- OVERVIEW TAB --}}
            @if ($activeTab === 'overview')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        {{-- Top kreditorer --}}
                        <div class="rounded-2xl border border-slate-200 p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">
                                        Top kreditorer
                                    </h2>
                                    <p class="text-sm text-slate-500">
                                        Klik på en kreditor for at filtrere sager
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @forelse ($recordsByKreditor as $navn => $count)
                                    <button
                                        type="button"
                                        wire:click="filterByKreditor('{{ addslashes($navn) }}')"
                                        class="flex w-full items-center justify-between rounded-xl border px-4 py-3 text-left transition cursor-pointer
                                            {{ $selectedKreditor === $navn
                                                ? 'border-indigo-500 bg-indigo-50'
                                                : 'border-slate-200 bg-white hover:bg-slate-50' }}"
                                    >
                                        <span class="font-medium text-slate-800">
                                            {{ $navn }}
                                        </span>

                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                            {{ $count }}
                                        </span>
                                    </button>
                                @empty
                                    <p class="text-sm text-slate-500">
                                        Ingen kreditor-data fundet.
                                    </p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Workload & Chart --}}
                        <div class="space-y-6">
                            <div class="rounded-2xl border border-slate-200 p-5">
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Workload
                                </h2>

                                <div class="mt-5 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <h3 class="mb-3 text-xs font-bold uppercase text-slate-400 tracking-wider">
                                            Konsulenter
                                        </h3>

                                        <div class="space-y-3">
                                            @forelse ($konsulentStats as $navn => $count)
                                                <div>
                                                    <div class="mb-1 flex justify-between text-sm">
                                                        <span class="text-slate-600">{{ $navn }}</span>
                                                        <span class="font-semibold text-slate-900">{{ $count }}</span>
                                                    </div>

                                                    <div class="h-2 rounded-full bg-slate-100">
                                                        <div
                                                            class="h-2 rounded-full bg-indigo-500"
                                                            style="width: {{ min(100, $count * 10) }}%"
                                                        ></div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-sm text-slate-500">
                                                    Ingen konsulentdata.
                                                </p>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div>
                                        <h3 class="mb-3 text-xs font-bold uppercase text-slate-400 tracking-wider">
                                            Sagsbehandlere
                                        </h3>

                                        <div class="space-y-3">
                                            @forelse ($sagsbehandlerStats as $navn => $count)
                                                <div>
                                                    <div class="mb-1 flex justify-between text-sm">
                                                        <span class="text-slate-600">{{ $navn }}</span>
                                                        <span class="font-semibold text-slate-900">{{ $count }}</span>
                                                    </div>

                                                    <div class="h-2 rounded-full bg-slate-100">
                                                        <div
                                                            class="h-2 rounded-full bg-emerald-500"
                                                            style="width: {{ min(100, $count * 10) }}%"
                                                        ></div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-sm text-slate-500">
                                                    Ingen sagsbehandlerdata.
                                                </p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-5">
                                <h2 class="text-lg font-semibold text-slate-900 mb-4">
                                    Modtagne sager fra kreditor
                                </h2>

                                <div
                                    wire:ignore
                                    x-data="{
                                        chart: null,
                                        init() {
                                            this.renderChart();
                                            Livewire.on('dashboard-loaded', () => {
                                                this.$nextTick(() => { this.renderChart(); });
                                            });
                                        },
                                        renderChart() {
                                            if (!window.Chart) return;
                                            if (this.chart) { this.chart.destroy(); }
                                            let data = @js($this->SagerChartData);
                                            if (!data.labels.length) return;

                                            this.chart = new Chart(this.$refs.canvas, {
                                                type: 'line',
                                                data: { labels: data.labels, datasets: data.datasets },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    interaction: { mode: 'index', intersect: false },
                                                    plugins: { legend: { position: 'bottom' } },
                                                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                                                }
                                            });
                                        }
                                    }"
                                    class="h-80"
                                >
                                    <canvas x-ref="canvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Seneste aktivitet --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="border-b border-slate-100 pb-3 mb-3">
                            <h2 class="text-lg font-semibold text-slate-900">
                                Seneste aktivitet
                            </h2>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse($activities ?? collect() as $activity)
                                <div class="py-3">
                                    <div class="font-medium text-slate-800">
                                        {{ $activity->description }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $activity->causer?->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-slate-500 text-sm">
                                    Ingen aktivitet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Lazy section loader --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Indlæste sektioner
                                </h2>
                                <p class="text-sm text-slate-500">
                                    {{ collect([$loadUnhandled, $loadIncoming, $loadUnread, $loadEditing])->filter()->count() }} / {{ $totalSections }}
                                </p>
                            </div>

                            <span class="text-sm font-semibold text-indigo-600">
                                {{ $this->loadingPercent }}%
                            </span>
                        </div>

                        <div class="h-3 overflow-hidden rounded-full bg-slate-100 mb-4">
                            <div
                                class="h-full rounded-full bg-indigo-600 transition-all duration-300"
                                style="width: {{ $this->loadingPercent }}%;"
                            ></div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <button
                                type="button"
                                wire:click="setTab('unhandled')"
                                class="rounded-xl border border-slate-200 py-3 text-center text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                            >
                                Ubehandlede
                            </button>
                            
                            <button
                                type="button"
                                wire:click="setTab('incoming')"
                                class="rounded-xl border border-slate-200 py-3 text-center text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                            >
                                Indkomne
                            </button>

                            <button
                                type="button"
                                wire:click="setTab('unread_messages')"
                                class="rounded-xl border border-slate-200 py-3 text-center text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                            >
                                Ulæste beskeder
                            </button>

                            <button
                                type="button"
                                wire:click="setTab('live_editing')"
                                class="rounded-xl border border-slate-200 py-3 text-center text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
                            >
                                Under behandling
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- UNHANDLED TAB --}}
            @if($activeTab === 'unhandled')
                <livewire:sager.sager-data-table
                    mode="unhandled"
                    ui-mode="table"
                    :selected-kreditor="$selectedKreditor"
                    :key="'unhandled-'.$selectedKreditor"
                />
            @endif

            {{-- INCOMING TAB --}}
            @if($activeTab === 'incoming')
                <livewire:sager.sager-data-table
                    mode="incoming"
                    ui-mode="table"
                    :selected-kreditor="$selectedKreditor"
                    :key="'incoming-'.$selectedKreditor"
                />
            @endif

            {{-- LIVE EDITING TAB --}}
            @if($activeTab === 'live_editing')
                <livewire:sager.sager-data-table
                    mode="live_editing"
                    ui-mode="table"
                    :selected-kreditor="$selectedKreditor"
                    :key="'live_editing-'.$selectedKreditor"
                />
            @endif

            {{-- UNREAD MESSAGES TAB --}}
            @if($activeTab === 'unread_messages')
                <livewire:sager.sager-data-table
                    mode="unread_messages"
                    ui-mode="table"
                    :selected-kreditor="$selectedKreditor"
                    :key="'unread_messages-'.$selectedKreditor"
                />
            @endif

            {{-- SAGER TAB --}}
            @if ($activeTab === 'sager')
                <div>
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="text-lg font-semibold text-slate-900">Sager</h2>
                        @if ($selectedKreditor)
                            <p class="mt-1 text-sm text-slate-500">
                                Filtreret på kreditor:
                                <span class="font-semibold text-indigo-600">{{ $selectedKreditor }}</span>
                                <button
                                    type="button"
                                    wire:click="filterByKreditor('{{ addslashes($selectedKreditor) }}')"
                                    class="ml-2 text-sm font-medium text-red-600 hover:text-red-700 cursor-pointer"
                                >
                                    Fjern filter
                                </button>
                            </p>
                        @endif
                    </div>

                    <livewire:sager.sager-data-table
                        mode="all"
                        ui-mode="table"
                        :selected-kreditor="$selectedKreditor"
                        :key="'admin-sager-table-'.$selectedKreditor"
                    />
                </div>
            @endif

            {{-- USERS TAB --}}
            @if ($activeTab === 'users')
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h2 class="text-lg font-semibold text-slate-900">
                            Brugere
                        </h2>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div class="rounded-xl bg-slate-50 p-4 border border-slate-100">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">
                                    {{ $userStats['total'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-100">
                                <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Aktive i dag</p>
                                <p class="mt-1 text-2xl font-bold text-emerald-800">
                                    {{ $userStats['active_today'] ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-5">
                        <h2 class="text-lg font-semibold text-slate-900">
                            Roller
                        </h2>

                        <div class="mt-4 space-y-3">
                            @forelse ($roleStats as $role => $count)
                                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                                    <span class="text-sm font-medium text-slate-700">
                                        {{ $role }}
                                    </span>

                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-900 shadow-sm border border-slate-200/60">
                                        {{ $count }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">
                                    Ingen rolledata fundet.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            {{-- WARNINGS TAB --}}
            @if ($activeTab === 'warnings')
                <div class="rounded-2xl border border-slate-200 p-5">
                    <h2 class="text-lg font-semibold text-slate-900">
                        Systemstatus
                    </h2>

                    <div class="mt-4">
                        @if (count($systemWarnings))
                            <div class="space-y-3">
                                @foreach ($systemWarnings as $warning)
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 font-medium">
                                        {{ $warning }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 font-medium">
                                Ingen systemadvarsler fundet.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    @endif
</div>