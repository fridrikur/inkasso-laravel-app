<div class="grid md:grid-cols-2 gap-6">

    <div class="bg-white rounded-xl shadow p-4">

        <div class="font-semibold mb-3">
            Top konsulenter
        </div>

        @foreach($konsulentStats as $name => $count)

            <div class="flex justify-between text-sm">

                <span>{{ $name }}</span>

                <span>{{ $count }} sager</span>

            </div>

        @endforeach

    </div>

    <div class="bg-white rounded-xl shadow p-4">

        <div class="font-semibold mb-3">
            Top sagsbehandlere
        </div>

        @foreach($sagsbehandlerStats as $name => $count)

            <div class="flex justify-between text-sm">

                <span>{{ $name }}</span>

                <span>{{ $count }} sager</span>

            </div>

        @endforeach

    </div>

</div>