<!-- resources/views/liveWire/afslutning/show.blade.php -->
<div>
    <h1>Vis afslutnings mulighed</h1>
    <p>Tekst: {{ $afslutning->tekst }}</p>
    <p>Forkortelse: {{ $afslutning->forkortelse }}</p>
    <a href="{{ route('afslutning.index') }}">Tilbage til oversigt</a>
</div>