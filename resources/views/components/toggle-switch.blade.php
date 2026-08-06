<div x-data="{ on: @entangle($attributes->wire('model')) }" class="flex items-center space-x-3">
    <button
        type="button"
        @click="on = !on"
        :class="on ? 'bg-green-500' : 'bg-gray-300'"
        class="relative inline-flex h-6 w-12 items-center rounded-full transition-colors"
    >
        <span
            :class="on ? 'translate-x-6' : 'translate-x-1'"
            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
        ></span>
    </button>

    <!-- Hidden checkbox so it submits in a normal <form> too -->
    
        <input type="checkbox" 
           name="{{ $name }}" 
           x-model="on" 
           value="1" 
           class="hidden" />
</div>
