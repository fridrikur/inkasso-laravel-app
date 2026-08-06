<!-- resources/views/liveWire/ktr/index.blade.php -->
<div>
    <h1>KTR Oversigt</h1>
    <table>
        <thead>
            <tr>
                <th>Tekst</th>
                <th>Forkortelse</th>
                <th>Handling</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ktrs as $ktr)
                <tr>
                    <td>{{ $ktr->tekst }}</td>
                    <td>{{ $ktr->forkortelse }}</td>
                    <td>
                        <a href="{{ route('ktr.show', $ktr->id) }}">Vis</a>
                        <a href="{{ route('ktr.edit', $ktr->id) }}">Rediger</a>
                        <button wire:click="delete({{ $ktr->id }})">Slet</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('ktr.create') }}">Opret ny KTR</a>
</div>