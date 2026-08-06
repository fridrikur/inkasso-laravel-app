<div class="bg-gray-100 p-4 rounded shadow-lg">
    <form wire:submit.prevent="save">

        @foreach($placements as $field)
            @switch($field)
                @case('afsluttet')
                @case('faktureret')
                @case('modtaget')
                @case('senesterapport')
                @case('opgivet')
                    @include('liveWire.sager.fields.date', ['field' => $field, 'form' => $form])
                    @break

                @case('kreditor')
                @case('konsulent')
                @case('sagsbehandler')
                @case('status')
                    @include('liveWire.sager.fields.relationship', ['field' => $field, 'form' => $form, 'options' => $options])
                    @break

                @case('hovedstol')
                @case('renter')
                @case('gebyr')
                @case('ialt')
                @case('startgebyr')
                @case('restgaeld')
                @case('restgaeld_dkg')
                @case('mdlydelse')
                @case('n_mdlydelse')
                    @include('liveWire.sager.fields.masked', ['field' => $field, 'form' => $form])
                    @break

                @default
                    @include('liveWire.sager.fields.text', ['field' => $field, 'form' => $form])
            @endswitch
        @endforeach

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4 hover:bg-blue-600">Gem</button>
    </form>
</div>
