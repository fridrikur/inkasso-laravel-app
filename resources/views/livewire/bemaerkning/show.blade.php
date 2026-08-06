<!-- resources/views/liveWire/bemaerkning/show.blade.php -->
<div>
    <h1>Vis bemaerkning</h1>
    <p>Tekst: {{ $bemaerkning->tekst }}</p>
    <p>Forkortelse: {{ $bemaerkning->forkortelse }}</p>
    <a href="{{ route('bemaerkning.index') }}">Tilbage til oversigt</a>
</div>