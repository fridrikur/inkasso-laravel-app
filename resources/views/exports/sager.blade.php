<table>
    <thead>
        <tr>
            @foreach($columns as $field)
                <th>{{ \App\Models\Sager::alias($field) ?? ucfirst($field) }}</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach($sager as $sag)
            <tr>

                @foreach($columns as $field)

                    @php
                        $value = match ($field) {

                            'sagsnr' => $sag->sagsnr,

                            'modtaget' => optional($sag->modtaget)?->format('d-m-Y'),

                            'afsluttet' => optional($sag->afsluttet)?->format('d-m-Y'),

                            'debitor' => optional($sag->sagerdebitor->first())->navn,

                            'kreditor' => optional($sag->sagerkreditor->first())->navn,

                            'status' => optional($sag->sagerStatus->first())->tekst,

                            'afslutning' => optional($sag->sagerAfslutning->first())->tekst,

                            'sagsbehandler' => optional($sag->sagersagsbehandler->first())->navn,

                            'konsulent' => optional($sag->sagerkonsulent->first())->navn,

                            default => '',
                        };
                    @endphp

                    <td>{{ $value }}</td>

                @endforeach

            </tr>
        @endforeach
    </tbody>
</table>