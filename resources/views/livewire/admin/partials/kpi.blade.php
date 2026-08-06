<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Sager</div>
        <div class="text-2xl font-bold">{{ $totalSager }}</div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Kreditorer</div>
        <div class="text-2xl font-bold">{{ $totalKreditorer }}</div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">Brugere</div>
        <div class="text-2xl font-bold">{{ $userStats['total'] ?? 0 }}</div>
        <div class="text-xs text-green-600">
            {{ $userStats['active_today'] ?? 0 }} aktive i dag
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <div class="text-sm text-gray-500">GDPR</div>
        <div class="text-sm text-red-600">
            {{ $gdprExpired }} udløbet
        </div>
        <div class="text-sm text-yellow-600">
            {{ $gdprExpiring }} snart
        </div>
    </div>

</div>