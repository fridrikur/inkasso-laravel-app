<div class="space-y-6">

    <!-- SØGEFELT -->
    <div class="flex justify-between items-center bg-white p-4 shadow rounded-lg border border-gray-200">
        <div class="w-full max-w-md">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Søg efter navn, e-mail eller CPR/PNR..." 
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm border p-2"
            >
        </div>
        @if($search)
            <button wire:click="$set('search', '')" class="text-sm text-gray-500 hover:text-gray-700 underline ml-4">
                Nulstil søgning
            </button>
        @endif
    </div>

    <!-- FANEBLAD NAVIGATION -->
    <div class="border-b border-gray-200 overflow-x-auto">
        <nav class="-mb-px flex space-x-6 min-w-max" aria-label="Tabs">
            
            <!-- Tab 1: Med sager -->
            <button wire:click="$set('activeTab', 'active')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition {{ $activeTab === 'active' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Debitorer med sager
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold {{ $activeTab === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-900' }}">{{ $activeCount }}</span>
            </button>

            <!-- Tab 2: Uden sager -->
            <button wire:click="$set('activeTab', 'orphans')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition {{ $activeTab === 'orphans' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Uden sager (Forældreløse)
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold {{ $activeTab === 'orphans' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-900' }}">{{ $orphansCount }}</span>
            </button>

            <!-- Tab 3: Samme Navn -->
            <button wire:click="$set('activeTab', 'same_name')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition {{ $activeTab === 'same_name' ? 'border-amber-600 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Dubletter: Samme navn
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold {{ $activeTab === 'same_name' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-900' }}">{{ $sameNameCount }}</span>
            </button>

            <!-- Tab 4: Samme CPR/PNR -->
            <button wire:click="$set('activeTab', 'same_cpr')" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition {{ $activeTab === 'same_cpr' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Dubletter: Samme CPR / PNR
                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-semibold {{ $activeTab === 'same_cpr' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-900' }}">{{ $sameCprCount }}</span>
            </button>

        </nav>
    </div>

    <!-- TAB 1: MED SAGER -->
    @if($activeTab === 'active')
        <div class="overflow-hidden bg-white shadow rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Debitor</th>
                        <th class="px-4 py-3 text-left">Sager</th>
                        <th class="px-4 py-3 text-center">Handlinger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($activeDebitorer as $debitor)
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-4 py-3 font-mono">#{{ $debitor->id }}</td>
                            <td class="px-4 py-3">
                                <button wire:click="openDebitorModal({{ $debitor->id }})" class="font-medium text-blue-600 hover:text-blue-800 hover:underline text-left">
                                    {{ $debitor->navn }}
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($debitor->sager as $sag)
                                        <a href="{{ route('sager.edit', $sag) }}" class="px-2 py-1 rounded bg-slate-100 text-sm hover:bg-slate-200">Sag #{{ $sag->id }}</a>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('debitorer.edit', $debitor) }}" class="px-2 py-1 rounded bg-gray-100 text-xs hover:bg-gray-200">Rediger</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Ingen debitorer fundet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 bg-gray-50 border-t">
                {{ $activeDebitorer->links() }}
            </div>
        </div>
    @endif

    <!-- TAB 2: FORÆLDRELØSE -->
    @if($activeTab === 'orphans')
        <div class="overflow-hidden bg-white shadow rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-emerald-700 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Debitor</th>
                        <th class="px-4 py-3 text-center">Handlinger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orphans as $debitor)
                        <tr class="hover:bg-emerald-50 transition">
                            <td class="px-4 py-3 font-mono">#{{ $debitor->id }}</td>
                            <td class="px-4 py-3">
                                <button wire:click="openDebitorModal({{ $debitor->id }})" class="font-medium text-blue-600 hover:text-blue-800 hover:underline text-left">
                                    {{ $debitor->navn }}
                                </button>
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <a href="{{ route('debitorer.edit', $debitor) }}" class="px-2 py-1 rounded bg-gray-100 text-xs hover:bg-gray-200">Rediger</a>
                                <button wire:click="deleteDebitor({{ $debitor->id }})" wire:confirm="Er du sikker på du vil slette debitoren?" class="px-3 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700">Slet</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500">Ingen forældreløse debitorer fundet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 bg-gray-50 border-t">
                {{ $orphans->links() }}
            </div>
        </div>
    @endif

    <!-- TAB 3: SAMME NAVN -->
    @if($activeTab === 'same_name')
        <div class="overflow-hidden bg-white shadow rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-amber-700 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Navn (Dublet)</th>
                        <th class="px-4 py-3 text-left">CPR/PNR</th>
                        <th class="px-4 py-3 text-center">Handlinger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sameNameDebitorer as $debitor)
                        <tr class="hover:bg-amber-50 transition">
                            <td class="px-4 py-3 font-mono">#{{ $debitor->id }}</td>
                            <td class="px-4 py-3">
                                <button wire:click="openDebitorModal({{ $debitor->id }})" class="font-medium text-blue-600 hover:text-blue-800 hover:underline text-left">
                                    {{ $debitor->navn }}
                                </button>
                            </td>
                            <td class="px-4 py-3 font-mono text-sm text-gray-600">{{ $debitor->cpr ?? $debitor->pnr ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('debitorer.edit', $debitor) }}" class="px-2 py-1 rounded bg-gray-100 text-xs hover:bg-gray-200">Rediger</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Ingen navne-dubletter fundet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 bg-gray-50 border-t">
                {{ $sameNameDebitorer->links() }}
            </div>
        </div>
    @endif

    <!-- TAB 4: SAMME CPR / PNR -->
    @if($activeTab === 'same_cpr')
        <div class="overflow-hidden bg-white shadow rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-red-700 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Navn</th>
                        <th class="px-4 py-3 text-left">CPR / PNR (Dublet)</th>
                        <th class="px-4 py-3 text-center">Handlinger</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sameCprDebitorer as $debitor)
                        <tr class="hover:bg-red-50 transition">
                            <td class="px-4 py-3 font-mono">#{{ $debitor->id }}</td>
                            <td class="px-4 py-3">
                                <button wire:click="openDebitorModal({{ $debitor->id }})" class="font-medium text-blue-600 hover:text-blue-800 hover:underline text-left">
                                    {{ $debitor->navn }}
                                </button>
                            </td>
                            <td class="px-4 py-3 font-mono font-semibold text-red-600">{{ $debitor->cpr ?? $debitor->pnr ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('debitorer.edit', $debitor) }}" class="px-2 py-1 rounded bg-gray-100 text-xs hover:bg-gray-200">Rediger</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">Ingen CPR/PNR-dubletter fundet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 bg-gray-50 border-t">
                {{ $sameCprDebitorer->links() }}
            </div>
        </div>
    @endif

    <!-- MODAL: VIS ALLE DEBITOR-DATA -->
    @if($showModal && $selectedDebitor)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full overflow-hidden border border-gray-200">
                
                <div class="bg-slate-800 text-white px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Debitor Detaljer: {{ $selectedDebitor->navn }}</h3>
                    <button wire:click="closeModal" class="text-gray-300 hover:text-white font-bold text-xl">&times;</button>
                </div>

                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 p-3 rounded border">
                            <span class="block text-gray-500 text-xs font-semibold uppercase">ID</span>
                            <span class="font-mono font-medium">#{{ $selectedDebitor->id }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded border">
                            <span class="block text-gray-500 text-xs font-semibold uppercase">Navn</span>
                            <span class="font-medium">{{ $selectedDebitor->navn }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded border">
                            <span class="block text-gray-500 text-xs font-semibold uppercase">CPR / PNR</span>
                            <span class="font-mono font-medium">{{ $selectedDebitor->cpr ?? $selectedDebitor->pnr ?? 'Ikke angivet' }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded border">
                            <span class="block text-gray-500 text-xs font-semibold uppercase">E-mail</span>
                            <span class="font-medium">{{ $selectedDebitor->email ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded border">
                            <span class="block text-gray-500 text-xs font-semibold uppercase">Adresse</span>
                            <span class="font-medium">{{ $selectedDebitor->adresse ?? '-' }}</span>
                        </div>
                        <div class="bg-gray-50 p-3 rounded border">
                            <span class="block text-gray-500 text-xs font-semibold uppercase">Telefon / Mobil</span>
                            <span class="font-medium">{{ $selectedDebitor->tlf ?? $selectedDebitor->mobil ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded border">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Tilknyttede Sager ({{ $selectedDebitor->sager->count() }})</h4>
                        @if($selectedDebitor->sager->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($selectedDebitor->sager as $sag)
                                    <a href="{{ route('sager.edit', $sag) }}" target="_blank" class="px-2.5 py-1 bg-white border rounded text-sm text-blue-600 hover:bg-blue-50">
                                        Sag #{{ $sag->id }}
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Ingen sager tilknyttet denne debitor.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3 border-t">
                    <a href="{{ route('debitorer.edit', $selectedDebitor) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                        Gå til redigering
                    </a>
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded hover:bg-gray-300">
                        Luk
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>