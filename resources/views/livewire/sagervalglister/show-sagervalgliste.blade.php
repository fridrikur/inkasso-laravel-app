<div>
    <h2>Oversigt over sagsvalglister</h2>
    <table class="table-fixed">
        <thead>
          <tr>
            <th>Navn</th>
            <th>Forkortelse</th>
            <th>Handling</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($sagervalglister as $sagervalgliste)
            <tr wire:key="{{ $sagervalgliste->id }}">
                <td>
                <a href="sagervalglister/{{ $sagervalgliste->id }}/update">{{$sagervalgliste->navn}}</a></td>
                <td> 
                <a href="sagervalglister/{{ $sagervalgliste->id }}/update">{{$sagervalgliste->forkortelse}}</a></td>
                </td>
                <td><button wire:click="deletesagervalgliste({{ $sagervalgliste->id }})" wire:confirm="Er du sikker?">Slet</button></td>
            @endforeach
          </tr>
        </tbody>
      </table>
</div>
    