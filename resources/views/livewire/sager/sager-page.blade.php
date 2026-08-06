@extends('layouts.app') {{-- if you use layouts --}}
@section('content')

<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-bold mb-4">Sorter felter</h1>

    {{-- liveWire component --}}
    <liveWire:sager.sager-form />
</div>

@endsection