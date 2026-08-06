<!-- resources/views/liveWire/ktr/show.blade.php -->
<div>
    <h1>Vis KTR</h1>
    <p>Tekst: {{ $ktr->tekst }}</p>
    <p>Forkortelse: {{ $ktr->forkortelse }}</p>
    <a href="{{ route('ktr.index') }}">Tilbage til oversigt</a>
</div>