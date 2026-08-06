@include('livewire.admin.partials.kpi')
@include('livewire.admin.partials.warnings')

<div class="grid md:grid-cols-2 gap-6 mb-6">

    <div class="bg-white rounded-xl shadow p-4">

    <div class="font-semibold mb-3">
        Sager pr. kreditor
    </div>

        @if(count($recordsByKreditor))
            <div wire:ignore>
                <div
                x-data="{
                    chart: null,
                    init() {
                        const labels = @js(array_keys($recordsByKreditor));
                        const data = @js(array_values($recordsByKreditor));

                        this.chart = new Chart(
                            this.$refs.canvas,
                            {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Antal sager',
                                        data: data,
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            }
                        );
                    }
                }"
                class="h-80"
            >
                <canvas x-ref="canvas"></canvas>
            </div></div>
        @else
            <div class="animate-pulse h-64 bg-gray-100 rounded"></div>
        @endif

    </div>

    <div class="bg-white rounded-xl shadow p-4">

        <div class="font-semibold mb-3">
            Top kreditorer
        </div>

        <div class="space-y-2 max-h-64 overflow-y-auto">

            @foreach($recordsByKreditor as $name => $count)

                <div
                    class="flex justify-between text-sm hover:bg-gray-50 px-2 py-1 rounded cursor-pointer"
                    wire:click="filterByKreditor('{{ $name }}')"
                >
                    <span>{{ $name }}</span>
                    <span class="font-semibold">{{ $count }}</span>
                </div>

            @endforeach

        </div>

    </div>

</div>

@include('livewire.admin.dashboard._overview-tables')
