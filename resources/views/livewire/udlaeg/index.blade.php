<!-- resources/views/liveWire/udlaeg/index.blade.php -->
<div>
    <h1>udlaeg Oversigt</h1>
    <table>
        <thead>
            <tr>
                <th>Tekst</th>
                <th>Handling</th>
            </tr>
        </thead>
        <tbody>
            @foreach($udlaegs as $udlaeg)
                <tr>
                    <td>{{ $udlaeg->tekst }}</td>
                    <td>{{ $udlaeg->forkortelse }}</td>
                    <td>
                        <a href="{{ route('udlaeg.show', $udlaeg->id) }}">Vis</a>
                        <a href="{{ route('udlaeg.edit', $udlaeg->id) }}">Rediger</a>
                        <button wire:click="delete({{ $udlaeg->id }})">Slet</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('udlaeg.create') }}">Opret ny udlaeg</a>
</div>