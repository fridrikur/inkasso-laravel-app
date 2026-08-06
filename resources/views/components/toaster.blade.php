<!-- Toast Container -->
<div 
    x-data="{
        toasts: [],
        addToast(message, type = 'info', icon = 'info') {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type, icon, show: true });
            setTimeout(() => this.removeToast(id), 3000); // auto-hide after 3s
        },
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) this.toasts.splice(index, 1);
        }
    }"
    x-on:notify.window="
        addToast($event.detail.message ?? 'Ukendt besked', $event.detail.type ?? 'info', $event.detail.icon ?? 'info')
    "
    class="fixed top-4 right-4 space-y-2 z-50"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition
            :class="{
                'bg-green-100 border border-green-400 text-green-800': toast.type === 'success',
                'bg-red-100 border border-red-400 text-red-800': toast.type === 'error',
                'bg-blue-100 border border-blue-400 text-blue-800': toast.type === 'info'
            }"
            class="shadow-lg rounded-lg p-4 flex items-center space-x-3 w-80"
        >
            <!-- Icons -->
            <template x-if="toast.type === 'success'">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
            </template>

            <!-- Message -->
            <p x-text="toast.message" class="flex-1 font-medium"></p>

            <!-- Close button -->
            <button @click="removeToast(toast.id)" class="text-gray-500 hover:text-gray-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>