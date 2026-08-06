<div class="max-w-6xl mx-auto">

    <div wire:loading wire:target="search" class="text-sm text-gray-500 mb-2">
        Søger...
    </div>

<input
    type="text"
    wire:model.live="search"
    placeholder="Søg sag..."
    class="mb-4 w-full border rounded-xl p-2"
/>

@if($suggestion)
<div class="mb-3 text-sm text-gray-600">
    Mente du:
    <button
        wire:click="$set('search', '{{ $suggestion }}')"
        class="text-indigo-600 underline"
    >
        {{ $suggestion }}
    </button>
    ?
</div>
@endif

<table class="w-full bg-white shadow rounded-xl">

<thead>
<tr class="border-b">
<th class="p-3 text-left">Sagsnr</th>
<th class="p-3 text-left">Debitor</th>
<th class="p-3 text-left">Hovedstol</th>
<th class="p-3"></th>
</tr>
</thead>

<tbody>

@foreach($sager as $sag)

<tr
    id="sag-row-{{ $sag->id }}"
    class="border-b hover:bg-gray-50">

<td class="p-3">{{ $sag->sagsnr }}</td>

<td class="p-3">{{ $sag->sagerdebitor->first()->navn ?? '' }}</td>

<td class="p-3">{{ $sag->hovedstol }}</td>

<td class="p-3 text-right">
<a href="{{ route('kreditor.sag.view',$sag->id) }}"
class="text-indigo-600">
Vis
</a>
</td>

</tr>

@endforeach

</tbody>

</table>

@if($sager->isEmpty())
<tr>
    <td colspan="4" class="p-4 text-center text-gray-500">
        Ingen sager fundet
    </td>
</tr>
@endif

{{ $sager->links() }}

<script>
document.addEventListener('DOMContentLoaded', () => {

    const urlParams = new URLSearchParams(window.location.search);

    const created = urlParams.get('created');
    const sagId = urlParams.get('sag_id');

    if (created === '1') {

        Livewire.dispatch('toast', {
            message: 'Sagen blev oprettet og sendt til DKG',
            type: 'success'
        });

        // Highlight row
        if (sagId) {
            setTimeout(() => {
                const row = document.getElementById('sag-row-' + sagId);

                if (row) {
                    row.classList.add('highlight-row');

                    row.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Remove highlight after a few seconds
                    setTimeout(() => {
                        row.classList.remove('highlight-row');
                    }, 4000);
                }
            }, 300);
        }

        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }

});
</script>
<style>
    .highlight-row{
    animation: highlightFade 4s ease;
}

@keyframes highlightFade{
    0%   { background-color:#86efac; }
    50%  { background-color:#dcfce7; }
    100% { background-color:transparent; }
}
    </style>
    </div>
