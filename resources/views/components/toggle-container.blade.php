<div x-data="{ open: false }" class="w-full space-y-2">
    <!-- Toggle Button -->
    <button
        type="button"
        @click="open = !open"
        class="flex justify-between items-center w-full px-5 py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
        <span class="text-gray-700 dark:text-gray-200 font-medium">{{ $label }}</span>
        <svg :class="{'rotate-180': open}" class="w-5 h-5 text-gray-500 dark:text-gray-400 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Toggle Content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 max-h-0"
        x-transition:enter-end="opacity-100 max-h-96"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 max-h-96"
        x-transition:leave-end="opacity-0 max-h-0"
        class="overflow-hidden bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-5 shadow-inner"
    >
        {{ $slot }}
    </div>
</div>
