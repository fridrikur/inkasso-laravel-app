<div 
    x-data="{
        toasts: [],
        addToast(message, type = 'info', icon = 'info') {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type, icon, show: true });
            setTimeout(() => this.removeToast(id), 3000);
        },
        removeToast(id) {
            const index = this.toasts.findIndex(t => t.id === id);
            if (index !== -1) this.toasts.splice(index, 1);
        }
    }"
    x-on:toast.window="
        let detail = $event.detail[0] ?? $event.detail;
        addToast(detail.message || 'Ukendt besked', detail.type || 'info', detail.icon || 'info');
    "
    class="fixed top-4 right-4 space-y-2 z-50 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition
            :class="{
                'bg-emerald-600 text-white': toast.type === 'success',
                'bg-rose-600 text-white': toast.type === 'error',
                'bg-amber-600 text-white': toast.type === 'warning',
                'bg-indigo-600 text-white': toast.type === 'info'
            }"
            class="shadow-xl rounded-2xl px-5 py-3.5 flex items-center gap-3 w-80 text-xs font-bold pointer-events-auto border border-white/10"
        >
            <span class="text-base" x-text="toast.type === 'success' ? '✓' : (toast.type === 'error' ? '✕' : (toast.type === 'warning' ? '⚠' : 'ℹ'))"></span>
            <p class="flex-1" x-text="toast.message"></p>
            <button @click="removeToast(toast.id)" class="text-white/80 hover:text-white cursor-pointer text-sm font-bold">
                &times;
            </button>
        </div>
    </template>
</div>