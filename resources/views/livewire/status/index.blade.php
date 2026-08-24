<div class="max-w-4xl mx-auto space-y-6">

    {{-- HEADER & OPRET KNAP --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <span>🏷️</span> Oversigt over Statusser
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Administrer statusser og deres forkortelser til sagsoversigten.
            </p>
        </div>

        <button 
            type="button" 
            wire:click="openCreateModal" 
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-sm cursor-pointer shrink-0"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Opret ny status</span>
        </button>
    </div>

    {{-- TABELOVERSIGT --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Navn</th>
                        <th scope="col" class="px-6 py-3.5">Forkortelse</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Handling</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($statuses as $status)
                        <tr wire:key="status-{{ $status->id }}" class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <button 
                                    type="button"
                                    wire:click="openEditModal({{ $status->id }})" 
                                    class="hover:text-indigo-600 transition flex items-center gap-2 group text-left cursor-pointer"
                                >
                                    <span>{{ $status->tekst }}</span>
                                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 text-indigo-500 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $status->forkortelse }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <x-table-actions 
                                    :id="$status->id" 
                                    editAction="openEditModal"
                                    deleteAction="deleteStatus"
                                    deleteConfirm="Er du sikker på, at du vil slette denne status?"
                                    :showView="false"
                                >
                                    {{-- "Vis sager"-knappen placeres automatisk foran rediger/slet via slot --}}
                                    <a 
                                        href="{{ route('admin.sager.status.show', $status->id) }}" 
                                        class="rounded-lg p-2 text-emerald-600 transition hover:bg-emerald-50"
                                        title="Vis alle sager med denne status"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                        </svg>
                                    </a>
                                </x-table-actions>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-slate-400 text-xs">
                                Ingen statusser fundet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TIP BOKS --}}
    <div x-data="{ open: false }" class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-100 text-xs">
        <button @click="open = !open" class="inline-flex items-center gap-2 font-bold text-indigo-900 hover:text-indigo-700 transition cursor-pointer">
            <span class="p-1 rounded-md bg-indigo-100 text-indigo-700">💡</span>
            <span>Tip til statusser</span>
            <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-indigo-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="open" x-cloak class="mt-2 text-indigo-900/80 leading-relaxed border-t border-indigo-100 pt-2">
            Status bliver brugt i forbindelse med sager og vises for kreditor som en specifik kolonne i sagsoversigten.
        </div>
    </div>

    {{-- GENBRUGELIG MODAL TIL BÅDE OPRET OG REDIGER --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- MØRKT / SLØRET OVERLAY --}}
            <div 
                wire:click="closeModal" 
                class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"
            ></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    
                    {{-- MODAL HEADER --}}
                    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm shadow-sm">🏷️</span>
                            <h3 class="text-base font-bold text-slate-900">
                                {{ $statusId ? 'Rediger status' : 'Opret ny status' }}
                            </h3>
                        </div>
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- FORMULAR --}}
                    <form wire:submit.prevent="saveStatus">
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Status Navn / Tekst
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="tekst" 
                                    placeholder="f.eks. Afventer Kreditor" 
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition"
                                    required
                                />
                                @error('tekst') <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Forkortelse
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="forkortelse" 
                                    placeholder="f.eks. AFV_KRED" 
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-mono focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none transition uppercase"
                                    required
                                />
                                @error('forkortelse') <span class="text-xs text-rose-600 mt-1 block font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- FOOTER --}}
                        <div class="bg-slate-50/80 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs transition cursor-pointer"
                            >
                                Annuller
                            </button>

                            <button 
                                type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition shadow-sm cursor-pointer"
                            >
                                {{ $statusId ? 'Gem ændringer' : 'Opret status' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>