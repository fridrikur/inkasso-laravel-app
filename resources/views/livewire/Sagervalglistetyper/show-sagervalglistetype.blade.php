<div>
    <a href="{{ route('updatesagervalglistetype', ['sagervalglistetype' => $sagervalglistetype])}}"><h1>{{ $navn }}</h1></a>
    <h2>Liste</h2>
    @if($sagervalglistetype !=null) <!-- opdatering -->
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <thead>
              <tr>
                <th scope="col" class="px-6 py-3">Navn</th>
                <th scope="col" class="px-6 py-3">Forkortelse</th>
                <th scope="col" class="px-6 py-3">Handling</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($sagervalglister as $sagervalgliste)
                @foreach ($sagervalgliste->sagervalglistetype as $liste)
                @if($liste->pivot->type_id==$sagervalglistetype->id)
                <tr wire:key="{{ $sagervalgliste->id }}" class="bg-white border-b dark:bg-gray-800 dark:border-black-700 border-gray-200">
                    <td>
                      <a href="{{ route('updatesagervalgliste', ['sagervalgliste' => $sagervalgliste])}}"><h1>{{ $sagervalgliste->navn }}</a></td>
                    <td> 
                    {{$sagervalgliste->forkortelse}}</td>
                    </td>
                    <td><button wire:click="deletesagervalgliste({{ $sagervalgliste->id }})" wire:confirm="Er du sikker?">Slet</button></td>
                    @endif
                @endforeach
                @endforeach
              </tr>
            </tbody>
          </table>
    </div>
        <div>
            <button type="button" wire:click="opretnysagervalgliste({{request()->sagervalglistetype}})" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
                Tilføj valg
              </button>
        </div>
        @endif
    </form>
    
</div>
