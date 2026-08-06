<div class="grid md:grid-cols-2 gap-6">

    <div class="bg-white rounded-xl shadow p-4">

        <div class="font-semibold mb-3">
            Bruger statistik
        </div>

        <div>Total: {{ $userStats['total'] }}</div>

        <div class="text-green-600">
            Aktive i dag: {{ $userStats['active_today'] }}
        </div>

    </div>

    <div class="bg-white rounded-xl shadow p-4">

        <div class="font-semibold mb-3">
            Roller
        </div>

        @foreach($roleStats as $role => $count)

            <div class="flex justify-between text-sm">

                <span>{{ $role }}</span>

                <span>{{ $count }}</span>

            </div>

        @endforeach

    </div>

</div>