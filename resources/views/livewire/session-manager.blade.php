<div 
    x-data 
    x-init="
        let timeout = 900000; // 15 min
        let warning = 870000; // 14.5 min

        let timer;
        let warningTimer;

        function reset() {
            clearTimeout(timer);
            clearTimeout(warningTimer);

            $wire.resetTimer();

            warningTimer = setTimeout(() => {
                $wire.showWarning();
            }, warning);

            timer = setTimeout(() => {
                window.location.href = '{{ route('login') }}?timeout=1';
            }, timeout);
        }

        ['click','keydown','mousemove','scroll'].forEach(event => {
            window.addEventListener(event, reset);
        });

        reset();
    "
>

    @if($showWarning)
        <div class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div class="bg-white p-6 rounded-xl">
                <p>Session udløber om {{ $countdown }} sekunder</p>

                <button wire:click="extendSession" class="btn btn-primary">
                    Forlæng session
                </button>
            </div>
        </div>

        <script>
            setInterval(() => {
                @this.tick();
            }, 1000);
        </script>
    @endif

</div>