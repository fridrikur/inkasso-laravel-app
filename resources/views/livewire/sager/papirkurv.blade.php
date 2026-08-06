<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                🗑 Papirkurv
            </h1>

            <p class="text-sm text-gray-500">
                Soft deleted sager
            </p>
        </div>

        <a
            href="{{ route('showsager') }}"
            class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm shadow-sm hover:bg-gray-50"
        >
            Tilbage til sager
        </a>

    </div>

    <x-sager.table
        :sagers="$sagers"
        mode="trash"
    />

    <div class="mt-4">
        {{ $sagers->links() }}
    </div>

</div>