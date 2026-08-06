<div style="max-width:500px">
    <h2>Oversigt over autotekster</h2>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead>
          <tr>
            <th>Navn</th>
            <th>Handling</th>
          </tr>
        </thead>
        <tbody>
    @foreach ($autotekster as $autotekst)
    <tr wire:key="{{ $autotekst->id }}">
            <td><a href="autotekster/{{ $autotekst->id }}/update">{{$autotekst->tekst}}</a></td>
            <td><button wire:click="deleteautotekst({{ $autotekst->id }})" wire:confirm="Er du sikker?">Slet</button></td>
    </tr>
    @endforeach
        </tbody>
    </table>
    <div>
        <button type="button" wire:click="opretnyautotekst" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
        Opret ny autotekst
        </button>
    </div>
    <div x-data="{ open: false }">
        <button @click="open = !open" class="inline-flex items-center cursor-help"><strong>Tip</strong></button>
        <div x-show="open">
            Autotekster bliver brugt i forbindelse med udvalgte dialoger
        </div>
    </div>
</div>