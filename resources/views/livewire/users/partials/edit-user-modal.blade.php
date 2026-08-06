<div class="fixed inset-0 z-50">
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
        wire:click="closeModal"
    ></div>

    {{-- Modal wrapper --}}
    <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-3xl rounded-3xl border border-slate-200 bg-white shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-8 py-6 border-b border-slate-200 bg-slate-50">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">
                        Rediger bruger
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Opdater brugeroplysninger, rolle og evt. kreditor
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="closeModal"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition"
                >
                    ✕
                </button>
            </div>

            {{-- Content --}}
            <div class="p-8">
                @livewire(
                    'users.update-user',
                    ['userId' => $activeUserId],
                    key('update-user-'.$activeUserId)
                )
            </div>
        </div>
    </div>
</div>