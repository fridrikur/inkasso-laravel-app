<div class="space-y-6 animate-pulse">

    {{-- Page header --}}
    <div class="h-8 w-1/3 bg-gray-200 rounded"></div>

    {{-- Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Column --}}
        <div class="space-y-4">
            @foreach(range(1,6) as $i)
                <div>
                    <div class="h-4 w-1/3 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
            @endforeach
        </div>

        {{-- Column --}}
        <div class="space-y-4">
            @foreach(range(1,6) as $i)
                <div>
                    <div class="h-4 w-1/3 bg-gray-200 rounded mb-2"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- Action buttons --}}
    <div class="flex justify-end gap-3 pt-6">
        <div class="h-10 w-24 bg-gray-200 rounded"></div>
        <div class="h-10 w-32 bg-gray-200 rounded"></div>
    </div>

</div>
