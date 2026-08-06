<div>

<h2 class="text-lg font-semibold mb-6">
Felter som kreditor må udfylde
</h2>

<div class="space-y-3">

@foreach($availableFields as $field => $label)

<label class="flex items-center gap-3">

<input
type="checkbox"
value="{{ $field }}"
wire:model="allowedFields"
class="rounded border-gray-300"
>

<span>
{{ $label }}
</span>

</label>

@endforeach

</div>

<button
wire:click="save"
class="mt-6 bg-blue-600 text-white px-4 py-2 rounded"
>
Gem opsætning
</button>

</div>