<div class="grid grid-cols-12 gap-6">

    {{-- LEFT NAVIGATION --}}
    <aside class="col-span-12 lg:col-span-3">

        @include('livewire.admin.dashboard._sidebar-nav')

    </aside>

    {{-- CONTENT --}}
    <div class="col-span-12 lg:col-span-9 space-y-12">
        @if($loadedSections < $totalSections)
            <div class="mb-6">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Indlæser sagsoversigter...</span>
                    <span>{{ $this->loadingPercent }}%</span>
                </div>

                <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-blue-600 transition-all duration-500"
                        style="width: {{ $this->loadingPercent }}%"
                    ></div>
                </div>
            </div>
        @endif
        <section id="overview" class="scroll-mt-24">
            @include('livewire.admin.dashboard._overview')
        </section>

        <section id="users" class="scroll-mt-24">
            @include('livewire.admin.dashboard._users')
        </section>

        <section id="workload" class="scroll-mt-24">
            @include('livewire.admin.dashboard._workload')
        </section>

        <section id="kreditor" class="scroll-mt-24">
            @include('livewire.admin.partials.kreditor')
        </section>

        <section id="system" class="scroll-mt-24">
            @include('livewire.admin.dashboard._system')
        </section>

    </div>

</div>