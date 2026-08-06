<div class="bg-white rounded-2xl shadow p-6">

    <h2 class="text-xl font-bold mb-4">
        System Backup
    </h2>

    <button
        wire:click="runBackup"
        wire:loading.attr="disabled"
        class="px-5 py-3 bg-black text-white rounded-xl"
    >
        <span wire:loading.remove>
            Run Backup
        </span>

        <span wire:loading>
            Creating backup...
        </span>
    </button>

    @if(session()->has('success'))
        <div class="mt-4 text-green-600">
            {{ session('success') }}
        </div>
    @endif

</div>