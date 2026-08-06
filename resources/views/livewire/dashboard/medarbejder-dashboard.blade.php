<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- 🔴 Ubehandlede sager --}}
    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="font-bold text-lg mb-2">
            Ubehandlede sager ({{ $unreadSagerCount }})
        </h2>

        <div class="text-sm text-gray-500">
            Sager som endnu ikke er åbnet eller behandlet.
        </div>
    </div>


    <div class="bg-white p-4 rounded-xl shadow">
        <h2 class="font-bold text-lg mb-3">Seneste sager</h2>

        @forelse($latestSager as $sag)

            @php
                $debitor = $sag->sagerdebitor->first();
                $kreditor = $sag->sagerkreditor->first();
                $sagsbehandler = $sag->sagersagsbehandler->first();
            @endphp

            <a href="{{ route('sager.edit', $sag->id) }}"
            class="block border-b py-3 hover:bg-gray-50 transition">

                <div class="grid grid-cols-4 gap-2 text-sm">

                    <div>
                        <div class="font-semibold">#{{ $sag->display_number }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $sag->created_at->format('d-m H:i') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-xs">Debitor</div>
                        <div>{{ $debitor->navn ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-xs">Kreditor</div>
                        <div>{{ $kreditor->navn ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-xs">Sagsbehandler</div>
                        <div>{{ $sagsbehandler->navn ?? '-' }}</div>
                    </div>

                </div>

            </a>

        @empty
            <div class="text-gray-500 text-sm">
                Ingen sager endnu
            </div>
        @endforelse
    </div>


    <div class="bg-white p-4 rounded-xl shadow col-span-2">
        <h2 class="font-bold text-lg mb-3">Seneste beskeder</h2>

        @forelse($sagerWithNewMessages as $sag)

            @php
                $debitor = $sag->sagerdebitor->first();
                $kreditor = $sag->sagerkreditor->first();
                $sagsbehandler = $sag->sagersagsbehandler->first();
            @endphp

            <a href="{{ route('sager.edit', $sag->id) }}"
            class="block border-b py-3 hover:bg-gray-50 transition">

                <div class="grid grid-cols-5 gap-2 items-center text-sm">

                    <div>
                        <div class="font-semibold">#{{ $sag->id }}</div>
                        <div class="text-xs text-gray-500">
                            {{ $sag->updated_at->diffForHumans() }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-xs">Debitor</div>
                        <div>{{ $debitor->navn ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-xs">Kreditor</div>
                        <div>{{ $kreditor->navn ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500 text-xs">Sagsbehandler</div>
                        <div>{{ $sagsbehandler->navn ?? '-' }}</div>
                    </div>

                    <div class="text-right">
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                            {{ $sag->unread_messages_count }} nye
                        </span>
                    </div>

                </div>

            </a>

        @empty
            <div class="text-gray-500 text-sm">
                Ingen nye beskeder
            </div>
        @endforelse
    </div>

</div>