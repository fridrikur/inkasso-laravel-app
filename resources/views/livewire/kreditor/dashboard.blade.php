<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-slate-900">
            Velkommen {{ $kreditor->navn }}
        </h1>

        <p class="text-slate-500 mt-2">
            Oversigt over dine sager de seneste 30 dage
        </p>

    </div>

    <div class="mb-8">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        {{-- SEARCH --}}
        <form wire:submit.prevent="search" class="flex-1 max-w-xl">

            <div class="flex">

                <input
                    type="text"
                    wire:model.defer="search"
                    placeholder="Søg efter sagsnummer..."
                    class="mt-1 w-full rounded-lg border-slate-300"
                >

                <button
                    type="submit"
                    class="
                        px-5
                        bg-slate-900
                        text-white
                        rounded-r-lg
                    "
                >
                    Søg
                </button>

            </div>

        </form>

        {{-- CREATE SAG --}}
        <button
            wire:click="createSag"
            class="
                inline-flex
                items-center
                justify-center
                px-5
                py-3
                rounded-lg
                bg-blue-600
                text-white
                font-semibold
                hover:bg-blue-700
            "
        >
            + Opret ny sag
        </button>

    </div>

</div>


    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">

        @foreach($this->closedStats as $title => $value)

            <div class="bg-white rounded-xl shadow p-6">

                <div class="text-sm text-slate-500">
                    {{ $title }}
                </div>

                <div class="text-3xl font-bold mt-2">
                    {{ $value }}
                </div>

            </div>
            
        @endforeach

    </div>



    {{-- CHART --}}
    <div class="bg-white rounded-xl shadow p-6 mb-10">

        <h2 class="text-xl font-semibold mb-6">
            Fordeling af afsluttede sager
            (sidste 30 dage)
        </h2>


        <div class="max-w-md mx-auto">

            <div
    x-data="{
        chart: null,
        init() {
            const labels = @js($this->chartData['labels']);
            const values = @js($this->chartData['values']);

            this.chart = new Chart(this.$refs.canvas, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        }
    }"
    class="h-80"
>
    <canvas x-ref="canvas"></canvas>
</div>

        </div>

    </div>




    {{-- QUICK SEARCH --}}
    <div class="bg-white rounded-xl shadow p-6">

        <h2 class="text-xl font-semibold mb-6">
            Hurtige søgninger
        </h2>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


            <button
                wire:click="showActive"
                class="px-5 py-3 rounded-lg bg-slate-900 text-white"
            >
                Vis aktive sager
            </button>


            <button
                wire:click="showClosed"
                class="px-5 py-3 rounded-lg bg-slate-900 text-white"
            >
                Vis afsluttede sager
            </button>


            <button
                wire:click="showAll"
                class="px-5 py-3 rounded-lg border"
            >
                Vis alle sager
            </button>


        </div>



        <h3 class="font-semibold mt-8 mb-4">
            Afsluttede sager
        </h3>


        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">


            @foreach($afslutninger as $afslutning)
                <a
                    href="{{ route('kreditor.search', [
                        'filter' => 'closed',
                        'afslutning_id' => $afslutning->id,
                    ]) }}"
                    class="..."
                >
                    {{ $afslutning->tekst }}
                </a>
            @endforeach

        </div>


    </div>


</div>