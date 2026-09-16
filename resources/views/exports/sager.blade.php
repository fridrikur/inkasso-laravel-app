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

                            'debitor' => optional($sag->debitor->first())->navn,

                            'kreditor' => optional($sag->kreditor->first())->navn,

                            'status' => optional($sag->status->first())->tekst,

                            'afslutning' => optional($sag->afslutning->first())->tekst,

                            'sagsbehandler' => optional($sag->sagsbehandler->first())->navn,

                            'konsulent' => optional($sag->konsulent->first())->navn,

                            default => '',
                        };
                    @endphp

                    <td>{{ $value }}</td>

                @endforeach

            </tr>
        @endforeach
    </tbody>
</table>