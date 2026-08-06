<button
    wire:click="toggleQuickMenu"
    class="fixed bottom-5 right-5 z-40 bg-black text-green-400 font-mono px-4 py-2 rounded shadow-lg border border-green-500"
>
    QUICK MENU
</button>

<div class="bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-xl p-6 mb-6 shadow">

    <div class="text-lg font-semibold">
        Velkommen tilbage, {{ auth()->user()->name ?? 'Admin' }} 👋
    </div>

    <div class="text-sm opacity-90">
        Her er et overblik over systemet i dag
    </div>

    <div class="mt-3 text-xs opacity-80">
        Session tid: {{ gmdate('H:i:s', $sessionSeconds) }}
    </div>

</div>