<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- HEADER --}}
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-slate-900">
            Søg i sager
        </h1>

        <p class="text-slate-500 mt-2">
            Find dine aktive og afsluttede sager
        </p>

    </div>



    {{-- SEARCH --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">

        <div class="flex flex-col md:flex-row gap-4">


            <div class="flex-1">

                <label class="text-sm text-slate-600">
                    Sagsnummer
                </label>

                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Søg efter sagsnummer..."
                    class="mt-1 w-full rounded-lg border-slate-300"
                >

            </div>


            <div class="flex items-end">

                <button
                    wire:click="clearFilters"
                    class="px-5 py-2 rounded-lg border"
                >
                    Ryd filtre
                </button>

            </div>


        </div>

    </div>




    {{-- FILTER BUTTONS --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">

        <h3 class="font-semibold mb-4 mt-8">
            Dato filtre
        </h3>


        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">


            <div>

                <label class="text-sm text-slate-600">
                    Modtaget fra
                </label>

                <input
                    type="date"
                    wire:model.live="modtagetFrom"
                    class="mt-1 w-full rounded-lg border-slate-300"
                >

            </div>


            <div>

                <label class="text-sm text-slate-600">
                    Modtaget til
                </label>

                <input
                    type="date"
                    wire:model.live="modtagetTo"
                    class="mt-1 w-full rounded-lg border-slate-300"
                >

            </div>



            <div>

                <label class="text-sm text-slate-600">
                    Afsluttet fra
                </label>

                <input
                    type="date"
                    wire:model.live="afsluttetFrom"
                    class="mt-1 w-full rounded-lg border-slate-300"
                >

            </div>


            <div>

                <label class="text-sm text-slate-600">
                    Afsluttet til
                </label>

                <input
                    type="date"
                    wire:model.live="afsluttetTo"
                    class="mt-1 w-full rounded-lg border-slate-300"
                >

            </div>


        </div>


        <h2 class="font-semibold mb-4">
            Filtre
        </h2>


        <div class="flex flex-wrap gap-3">


            <button
                wire:click="$set('filter','active')"
                class="
                    px-4 py-2 rounded-lg
                    {{ $filter === 'active'
                        ? 'bg-slate-900 text-white'
                        : 'border'
                    }}
                "
            >
                Aktive sager
            </button>



            <button
                wire:click="$set('filter','closed')"
                class="
                    px-4 py-2 rounded-lg
                    {{ $filter === 'closed'
                        ? 'bg-slate-900 text-white'
                        : 'border'
                    }}
                "
            >
                Afsluttede sager
            </button>



            <button
                wire:click="$set('filter','all')"
                class="
                    px-4 py-2 rounded-lg
                    {{ $filter === 'all'
                        ? 'bg-slate-900 text-white'
                        : 'border'
                    }}
                "
            >
                Alle sager
            </button>


        </div>



        <hr class="my-6">



        <h3 class="font-semibold mb-4">
            Afslutningstype
        </h3>



       <div class="flex flex-wrap gap-3">

    @foreach($afslutninger as $afslutning)

        <button
            wire:click="$set('afslutningId', {{ $afslutning->id }})"
            class="
                px-4 py-2 rounded-lg
                {{ $afslutningId === $afslutning->id
                    ? 'bg-slate-900 text-white'
                    : 'border'
                }}
            "
        >
            {{ $afslutning->tekst }}
        </button>

    @endforeach

</div>$


    </div>





    {{-- RESULTS --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">


        <div class="px-6 py-4 border-b">

            <h2 class="font-semibold">
                Sager
            </h2>

        </div>



        <div class="overflow-x-auto">


            <table class="min-w-full divide-y">


                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-sm">
                            Sagsnr
                        </th>


                        <th class="px-6 py-3 text-left text-sm">
                            Debitor
                        </th>


                        <th class="px-6 py-3 text-left text-sm">
                            Modtaget
                        </th>


                        <th class="px-6 py-3 text-left text-sm">
                            Afsluttet
                        </th>


                        <th class="px-6 py-3 text-left text-sm">
                            Status
                        </th>


                    </tr>

                </thead>



                <tbody class="divide-y">


                    @forelse($sager as $sag)

                        <tr>


                            <td class="px-6 py-4">
                                {{ $sag->sagsnr }}
                            </td>


                            <td class="px-6 py-4">

                                @foreach($sag->sagerdebitor as $debitor)

                                    {{ $debitor->navn }}

                                @endforeach

                            </td>


                            <td class="px-6 py-4">

                                {{ optional($sag->modtaget)->format('d-m-Y') }}

                            </td>


                            <td class="px-6 py-4">

                                {{ optional($sag->afsluttet)->format('d-m-Y') }}

                            </td>


                            <td class="px-6 py-4">


                                @if($sag->afsluttet)

                                    <span class="px-3 py-1 rounded-full text-sm bg-slate-100">
                                        Afsluttet
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-sm bg-green-100">
                                        Aktiv
                                    </span>

                                @endif


                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td colspan="5"
                                class="px-6 py-8 text-center text-slate-500">

                                Ingen sager fundet

                            </td>

                        </tr>


                    @endforelse


                </tbody>


            </table>


        </div>


    </div>



    {{-- PAGINATION --}}
    <div class="mt-6">

        {{ $sager->links() }}

    </div>


</div>