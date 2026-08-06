@if(!empty($systemWarnings))
    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6">
        <div class="font-semibold mb-2">⚠ System advarsler</div>

        <ul class="list-disc ml-5 text-sm">
            @foreach($systemWarnings as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        </ul>
    </div>
@endif