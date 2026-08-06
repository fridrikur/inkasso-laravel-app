<div>
    @foreach ($kreditor as $kreditorer)
        <h2>Oversigt over brugere for: {{ $kreditorer->navn }}</h2>
        <hr>
        @foreach ($kreditorer->kreditorer as $user)
        <div wire:key="{{ $kreditorer->id }}">
            <span>
                <a href="{{ route('updateuser', ['user' => $user])}}">{{ $user->name }}</a>
            </span>
        </div>
        @endforeach
    @endforeach
</div>