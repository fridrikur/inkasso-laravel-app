<!-- resources/views/liveWire/bemaerkning/index.blade.php -->
<div>
    <h1>bemaerkning Oversigt</h1>
    <table>
        <thead>
            <tr>
                <th>Tekst</th>
                <th>Forkortelse</th>
                <th>Handling</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bemaerknings as $bemaerkning)
                <tr>
                    <td>{{ $bemaerkning->tekst }}</td>
                    <td>{{ $bemaerkning->forkortelse }}</td>
                    <td>
                        <a href="{{ route('bemaerkning.show', $bemaerkning->id) }}">Vis</a>
                        <a href="{{ route('bemaerkning.edit', $bemaerkning->id) }}">Rediger</a>
                        <button wire:click="delete({{ $bemaerkning->id }})">Slet</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('bemaerkning.create') }}">Opret ny bemaerkning</a>
</div>