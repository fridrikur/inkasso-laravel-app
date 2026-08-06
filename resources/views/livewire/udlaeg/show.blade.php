<!-- resources/views/liveWire/udlaeg/show.blade.php -->
<div>
    <h1>Vis udlaeg</h1>
    <p>Tekst: {{ $udlaeg->tekst }}</p>
    <p>Forkortelse: {{ $udlaeg->forkortelse }}</p>
    <a href="{{ route('udlaeg.index') }}">Tilbage til oversigt</a>
</div>