@props([
    'section',
    'title',
    'summary' => '',
])

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">

    <button
        wire:click="toggleSection('{{ $section }}')"
        class="w-full px-6 py-5 flex justify-between items-center hover:bg-gray-50 transition">

        <div class="text-left">

            <div class="font-semibold text-gray-900">

                {{ $title }}

            </div>

            @if($summary)

                <div class="text-sm text-gray-500">

                    {{ $summary }}

                </div>

            @endif

        </div>

        <svg
            class="w-5 h-5 transition-transform {{ $openSection === $section ? 'rotate-180' : '' }}"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"/>

        </svg>

    </button>

    @if($openSection === $section)

        <div class="border-t p-6">

            {{ $slot }}

        </div>

    @endif

</div>