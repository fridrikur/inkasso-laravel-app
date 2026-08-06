<div>
    <h1 class="text-2xl font-bold mb-4">Oversigt over DKG konsulenter</h1>

{{-- Legend (Blade component) --}}
<x-legend :items="[
    ['value' => 'HK', 'label' => 'Hovedkonsulent', 'color' => 'green'],
    ['value' => 'SK', 'label' => 'Skjult konsulent', 'color' => 'yellow', 'count' => $skCount],
    ['value' => 'NK', 'label' => 'Notifikationskonsulent', 'color' => 'blue', 'count' => $nkCount],
]" />

{{-- Generic DataTable (liveWire) --}}
{{-- <liveWire:data-table :model="$model" :columns="$columns" /> --}}
<liveWire:data-table
  :model="\App\Models\Konsulenter::class"
  :columns="['navn','status','mobil','email']"
/>

    <div class="pt-2 pb-2">
        <x-toggle-container label="Detaljer om konsulenter">
        <div class="basis-64"><div id="hovedkonsulent">
                <p><strong>Hovedkonsulent</strong></p>
                @foreach ($hovedkonsulent->where('hovedkonsulent_count','1') as $konsulent)
                {{ $konsulent->navn}}
                @endforeach
            </div>
            </div>
        <div class="basis-64"><div id="skjultekonsulenter">
            <p><strong>Skjulte konsulenter</strong></p>
            @foreach ($skjultekonsulenter->where('skjultkonsulent_count','1') as $konsulent)
                {{ $konsulent->navn}}
            @endforeach
        </div></div>
        <div class="basis-64">
            <div id="notifikationskonsulenter">
                <p><strong>Notifikationsmodtagere</strong></p>
                @foreach ($notifikationskonsulenter->where('notifikationskonsulent_count','1') as $konsulent)
                {{ $konsulent->navn}}
                @endforeach
            <div id="ikkenotifikationskonsulenter">
                <p><strong>Modtager ikke notifikationer</strong></p>
                @foreach ($notifikationskonsulenter->where('notifikationskonsulent_count','0') as $konsulent)
                {{ $konsulent->navn}}
                @endforeach
            </div>
        </div></div>
    </x-toggle-container>
</div>
  <div class="mt-1">
    {{-- <button type="button" wire:click="opretnykonsulent" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
        Opret ny konsulent
    </button> --}}
  
    <x-global-modal title="Opret ny konsulent" size="lg">
      <x-slot name="trigger">
          <button @click="modalIsOpen = true" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
              Opret ny konsulent
          </button>
      </x-slot>
      <liveWire:konsulenter.create-konsulent />
      <x-slot name="footer">
          <button @click="modalIsOpen = false" class="px-4 py-2 bg-gray-200 rounded">Luk</button>
      </x-slot>
    </x-global-modal>
  </div>
</div>