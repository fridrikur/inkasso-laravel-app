<div class="p-6 space-y-4">


<h2 class="text-xl font-bold">Import / Export Debitorer</h2>


<input type="file" wire:model="file" class="border p-2 rounded">


@if($importedCount)
<div class="bg-green-50 p-3 rounded border">
✓ Importeret {{ $importedCount }} / {{ count($rows) }} rækker
</div>
@endif


@if($preview)
<table class="table-auto w-full text-sm border">
<thead>
<tr class="bg-gray-100">
<th>✓</th>
@foreach($headers as $h)<th>{{ $h }}</th>@endforeach
</tr>
</thead>
<tbody>
@foreach($rows as $i => $row)
<tr class="{{ in_array($i, $importedRows) ? 'bg-green-50' : '' }}">
<td class="text-center">{!! in_array($i, $importedRows) ? '✓' : '–' !!}</td>
@foreach($headers as $h)
<td>{{ $row[$h] ?? '' }}</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>


<div class="flex gap-2">
<button wire:click="import" class="bg-green-600 text-white px-4 py-2 rounded">Importér</button>
<button wire:click="exportCsv" class="bg-blue-600 text-white px-4 py-2 rounded">Export CSV</button>
<button wire:click="exportJson" class="bg-gray-700 text-white px-4 py-2 rounded">Export JSON</button>
</div>


@if(count($skipped))
<div class="bg-yellow-50 p-3 border rounded">
<strong>Ignorerede rækker:</strong> {{ count($skipped) }}
</div>
@endif
@endif


</div>