<div class="space-y-6">

    @include('livewire.admin.partials.warnings')

    <div class="bg-white rounded-xl shadow p-4">

        <div class="font-semibold mb-3">
            GDPR Status
        </div>

        <div class="text-red-600">
            Udløbet: {{ $gdprExpired }}
        </div>

        <div class="text-yellow-600">
            Snart: {{ $gdprExpiring }}
        </div>

    </div>

</div>

<button
    wire:click="goToGdprScan"
    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow mt-4"
>
    Scan
</button>