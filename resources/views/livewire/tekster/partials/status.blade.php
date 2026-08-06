<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

    <div class="bg-white rounded-2xl shadow-sm p-6">

        <div class="text-sm text-slate-500">
            Statustekster
        </div>

        <div class="text-4xl font-bold mt-2">
            {{ $statuses->count() }}
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">

        <div class="text-sm text-slate-500">
            Autotekster
        </div>

        <div class="text-4xl font-bold mt-2">
            {{ $autotekster->count() }}
        </div>

    </div>

</div>