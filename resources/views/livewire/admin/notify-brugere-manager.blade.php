<div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span>🔔</span> Notifikationer (Medarbejdere)
            </h2>
            <p class="text-[11px] text-slate-500">
                Styr hvem der modtog sagsnotifikationer.
            </p>
        </div>
    </div>

    <div class="space-y-3">
        @forelse ($users as $user)
            <div wire:key="user-notif-{{ $user->id }}" class="flex items-center justify-between p-2.5 rounded-2xl bg-slate-50/60 hover:bg-slate-50 transition border border-slate-100">
                <div class="flex items-center gap-2.5 truncate">
                    <div class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center border border-indigo-100 shrink-0 text-[11px]">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ $user->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                    </div>
                </div>

                <div>
                    {{-- RETTET TOGGLE SWITCH MED KORREKT TRANSITION --}}
                    <button 
                        type="button"
                        wire:click="toggleNotification({{ $user->id }})"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $user->is_notified ? 'bg-indigo-600' : 'bg-slate-300' }}"
                        role="switch"
                        aria-checked="{{ $user->is_notified ? 'true' : 'false' }}"
                    >
                        <span 
                            aria-hidden="true"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $user->is_notified ? 'translate-x-5' : 'translate-x-0' }}"
                        ></span>
                    </button>
                </div>
            </div>
        @empty
            <p class="text-center text-slate-400 text-xs py-4">
                Ingen medarbejdere fundet.
            </p>
        @endforelse
    </div>
</div>