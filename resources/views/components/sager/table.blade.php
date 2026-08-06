@props([
    'sagers',
    'mode' => null,
])

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    <table class="min-w-full divide-y divide-gray-200">

        {{-- ========================================= --}}
        {{-- HEADER --}}
        {{-- ========================================= --}}
        <thead class="bg-gray-50 sticky top-0 z-10">

            <tr>

                <th
                    wire:click="sortBy('sagers.id')"
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 cursor-pointer hover:bg-gray-100 transition"
                >
                    ID
                </th>

                <th
                    wire:click="sortBy('sagers.sagsnr')"
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 cursor-pointer hover:bg-gray-100 transition"
                >
                    Sagsnr
                </th>

                <th
                    wire:click="sortBy('debitor_navn')"
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 cursor-pointer hover:bg-gray-100 transition"
                >
                    Debitor
                </th>

                <th
                    wire:click="sortBy('kreditor_navn')"
                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 cursor-pointer hover:bg-gray-100 transition"
                >
                    Kreditor
                </th>

                @if($mode === 'live_editing')
                    <th class="px-6 py-3">
                        Redigeres af
                    </th>
                @endif

                <th
                    class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500"
                >
                    Handlinger
                </th>

            </tr>

        </thead>


        {{-- ========================================= --}}
        {{-- BODY --}}
        {{-- ========================================= --}}
        <tbody class="divide-y divide-gray-100 bg-white">

            @forelse($sagers as $sager)

                <tr
                    wire:key="sag-{{ $sager->id }}-{{ $mode }}"
                    class="
                        transition duration-150
                        hover:bg-blue-50/40

                        @if($sager->trashed())
                            opacity-60 bg-gray-50
                        @endif

                        @if($mode === 'unread_messages')
                            bg-yellow-50/30
                        @endif
                    "
                >

                    {{-- ID --}}
                    <td class="px-6 py-4 whitespace-nowrap">

                        <div class="font-semibold text-gray-900">
                            #{{ $sager->id }}
                        </div>

                    </td>


                    {{-- SAGSNR --}}
                    <td class="px-6 py-4 whitespace-nowrap">

                        <div class="flex items-start gap-2">

                            <div>

                                {{-- SAGSNR --}}
                                <div class="text-sm font-medium text-gray-800">
                                    {{ $sager->sagsnr }}
                                </div>

                                {{-- STATUS BADGES --}}
                                <div class="mt-1 flex flex-wrap gap-1">

                                    {{-- PAPIRKURV --}}
                                    @if($sager->trashed())

                                        <span class="
                                            inline-flex items-center gap-1
                                            rounded-full
                                            bg-gray-100
                                            px-2 py-0.5
                                            text-[11px]
                                            font-medium
                                            text-gray-600
                                        ">
                                            🗑 Slettet {{ $sager->deleted_at?->format('d-m-Y') }}
                                        </span>

                                    @endif


                                    {{-- GDPR WARNING --}}
                                    @if($sager->gdpr_status['code'] === 'warning')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-[11px] font-medium text-yellow-700">
                                            ⚠ GDPR snart
                                        </span>
                                    @endif

                                    {{-- GDPR EXPIRED --}}
                                    @if($sager->gdpr_status['code'] === 'expired')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">
                                            🔥 GDPR sletning mulig
                                        </span>
                                    @endif


                                    {{-- UNREAD MESSAGE --}}
                                    @if($mode === 'unread_messages')

                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-700"
                                        >

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.8"
                                                stroke="currentColor"
                                                class="h-3.5 w-3.5">

                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5H4.5A2.25 2.25 0 002.25 6.75m19.5 0v.243a2.25 2.25 0 01-.876 1.782l-7.5 5.625a2.25 2.25 0 01-2.748 0l-7.5-5.625A2.25 2.25 0 012.25 6.993V6.75" />

                                            </svg>

                                            Ny besked

                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </td>


                    {{-- DEBITOR --}}
                    <td class="px-6 py-4 whitespace-nowrap">

                        <div class="text-sm text-gray-700">
                            {{ $sager->debitor_navn }} NAVNET
                        </div>

                    </td>


                    {{-- KREDITOR --}}
                    <td class="px-6 py-4 whitespace-nowrap">

                        <div class="text-sm text-gray-700">
                            {{ $sager->kreditor_navn }}
                        </div>

                    </td>


                    {{-- LIVE EDITING --}}
                    @if($mode === 'live_editing')

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-2">

                                <span class="h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></span>

                                <div>

                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $sager->editor_name ?? 'Ukendt bruger' }}
                                    </div>

                                    <div class="text-xs text-gray-400">
                                        Aktiv nu
                                    </div>

                                </div>

                            </div>

                        </td>

                    @endif


                    {{-- ACTIONS --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right">

                        <div class="flex items-center justify-end gap-2">

                            {{-- ========================================= --}}
                            {{-- TRASH MODE --}}
                            {{-- ========================================= --}}
                            @if($mode === 'trash')

                                {{-- RESTORE --}}
                                <button
                                    wire:click="restoreSag({{ $sager->id }})"
                                    class="inline-flex items-center justify-center rounded-xl border border-green-200 bg-white p-2.5 text-green-600 shadow-sm transition hover:bg-green-50"
                                    title="Gendan sag"
                                >
                                    ♻️
                                </button>


                                {{-- FORCE DELETE --}}
                                @if($sager->isEligibleForGdprDeletion())

                                    <button
                                        wire:click="forceDeleteSag({{ $sager->id }})"
                                        wire:confirm="Permanent GDPR-sletning. Dette kan IKKE fortrydes."
                                        class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-white p-2.5 text-red-600 shadow-sm transition hover:bg-red-50"
                                        title="Permanent GDPR-sletning"
                                    >
                                        🔥
                                    </button>

                                @endif

                            @else

                                {{-- ========================================= --}}
                                {{-- EDIT --}}
                                {{-- ========================================= --}}
                                <div class="relative group">

                                    <a
                                        href="{{ route('sager.edit', ['sag' => $sager]) }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white p-2.5 text-green-500 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 hover:shadow"
                                    >

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.8"
                                            stroke="currentColor"
                                            class="h-4 w-4"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 20.25l1.037-4.5L16.862 3.487z"
                                            />

                                        </svg>

                                    </a>

                                </div>


                                {{-- ========================================= --}}
                                {{-- MOVE TO TRASH --}}
                                {{-- ========================================= --}}
                                <div class="relative group">

                                    <button
                                        wire:click="confirmDelete({{ $sager->id }})"
                                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white p-2.5 text-red-500 shadow-sm transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600 hover:shadow"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke-width="1.8"
                                            stroke="currentColor"
                                            class="h-4 w-4"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M6 7h12M9 7V4h6v3m-7 4v6m4-6v6m4-6v6M5 7h14l-1 13a2 2 0 01-2 2H8a2 2 0 01-2-2L5 7z"
                                            />

                                        </svg>

                                    </button>

                                </div>

                            @endif

                        </div>

                    </td>
                </tr>

            @empty

                {{-- EMPTY STATE --}}
                <tr>

                    <td colspan="6" class="px-6 py-14 text-center">

                        <div class="flex flex-col items-center justify-center">

                            <div class="mb-3 rounded-full bg-gray-100 p-4">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="h-8 w-8 text-gray-400"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M3.75 3v18h16.5"
                                    />

                                </svg>

                            </div>

                            <div class="text-sm font-medium text-gray-700">
                                Ingen sager fundet
                            </div>

                            <div class="mt-1 text-xs text-gray-400">
                                Der er ingen resultater i denne visnings
                            </div>

                        </div>

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>
    

</div>