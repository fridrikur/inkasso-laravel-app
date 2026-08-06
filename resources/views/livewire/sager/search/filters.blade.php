<div class="sticky top-4 z-20">
@if($activeFilterTab === 'status')
    @include('livewire.sager.search.filters.status')
@endif

@if($activeFilterTab === 'finance')
    @include('livewire.sager.search.filters.financial')
@endif

@if($activeFilterTab === 'parties')
    @include('livewire.sager.search.filters.parties')
@endif

@if($activeFilterTab === 'case')
    @include('livewire.sager.search.filters.case')
@endif

@if($activeFilterTab === 'dates')
    @include('livewire.sager.search.filters.dates')
@endif
</div>