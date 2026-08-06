<div>
    <h1>Meta</h1>
    <hr>
    @foreach ($meta as $meta)
        <div wire:key="{{ $meta->id }}"> 
            <span><a href="meta/{{ $meta->id }}/update">{{$meta->navn}}</a></span>
        </div>
    @endforeach
</div>