<div>
    <h2>Oversigt over sagsvalglistetyper</h2>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead>
          <tr>
            <th>Navn</th>
            <th>Handling</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($sagervalglistetyper as $sagervalglistetype)
            <tr wire:key="{{ $sagervalglistetype->id }}">
                <td>
                  <a href="{{ route('showsagervalglistetype', ['sagervalglistetype' => $sagervalglistetype])}}"><h1>{{ $sagervalglistetype->navn }}</h1></a></td>
                <td><button wire:click="deletesagervalglistetype({{ $sagervalglistetype->id }})" wire:confirm="Er du sikker?">Slet</button></td>
            @endforeach
          </tr>
        </tbody>
      </table>
    <button type="button" wire:click="opretnysagervalglistetype" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
        Opret ny sagervalglistetype
    </button>
    <div x-data="{ open: false }">
        <button @click="open = !open"><strong>Tip</strong></button>
        <div x-show="open">
            Sagervalglistetyper bliver brugt i forbindelse med sager
        </div>
    </div>
</div>