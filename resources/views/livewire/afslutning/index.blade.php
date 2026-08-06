<!-- resources/views/liveWire/afslutning/index.blade.php -->
<div>
    <h1>Afslutning Oversigt</h1>
    <table>
        <thead>
            <tr>
                <th>Tekst</th>
                <th>Forkortelse</th>
                <th>Handling</th>
            </tr>
        </thead>
        <tbody>
            @foreach($afslutnings as $afslutning)
                <tr>
                    <td>{{ $afslutning->tekst }}</td>
                    <td>{{ $afslutning->forkortelse }}</td>
                    <td>
                        <a href="{{ route('afslutning.show', $afslutning->id) }}">Vis</a>
                        <a href="{{ route('afslutning.edit', $afslutning->id) }}">Rediger</a>
                        <button wire:click="delete({{ $afslutning->id }})">Slet</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('afslutning.create') }}">Opret ny afslutning</a>
</div>