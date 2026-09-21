<x-layouts.app>
    <div class="container py-10 max-w-5xl mx-auto space-y-8" x-data="brevManager(@js(
        \App\Models\Brev::orderByRaw('CAST(brevpos AS UNSIGNED) asc')->get()->map(function($brev) {
            $filter = \App\Models\BrevFilter::where('brevID', $brev->id)->first();
            return [
                'id' => $brev->id,
                'titel' => $brev->titel,
                'navn' => (bool) ($filter->navn ?? false),
                'adresse' => (bool) ($filter->adresse ?? false),
                'dato' => (bool) ($filter->dato ?? false),
                'sagsnr' => (bool) ($filter->sagsnr ?? false),
                'emne' => (bool) ($filter->emne ?? false),
                'skjulalle' => (bool) ($filter->skjulalle ?? false),
                'visalle' => (bool) ($filter->visalle ?? false),
                'submitting' => false,
                'success' => false
            ];
        })
    ))">
        <!-- Sidetitel -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-2">
            <h1 class="text-xl font-bold text-slate-900">Skjul/vis og sorter brevfelter</h1>
            <p class="text-xs text-slate-500">Træk brevene op eller ned ved hjælp af ikonet yderst til venstre for at ændre rækkefølgen. Fravælg felter for det pågældende brev ved fletning.</p>
            <p class="text-xs font-semibold text-slate-700 pt-1">Tilgængelige felter: Navn, Adresse, Dato, Sagsnr og Emne.</p>
        </div>

        <!-- Samlet Tabel / Liste -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <ul class="divide-y divide-slate-100" @dragover.prevent>
                <template x-for="(brev, index) in breve" :key="brev.id">
                    <li 
                        draggable="true"
                        @dragstart="dragStart(index)"
                        @dragover.prevent
                        @drop="drop(index)"
                        :class="{'opacity-40 border-dashed border-indigo-500 bg-indigo-50/50': draggingIndex === index}"
                        class="p-5 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 hover:bg-slate-50/60 transition-all select-none"
                    >
                        <!-- Venstre side: Træk-håndtag, ID og Titel -->
                        <div class="flex items-center gap-4">
                            <div 
                                title="Træk for at ændre rækkefølge" 
                                class="cursor-grab active:cursor-grabbing text-slate-300 hover:text-slate-600 px-1 py-2 text-base font-bold tracking-tighter"
                            >
                                &#8942;&#8942;
                            </div>

                            <div>
                                <span class="text-xs font-mono text-slate-400" x-text="'#' + brev.id"></span>
                                <h3 class="text-sm font-bold text-slate-800" x-text="brev.titel"></h3>
                            </div>
                        </div>

                        <!-- Højre side: Checkboxes og Gem-knap pr. række -->
                        <form 
                            @submit.prevent="submitForm(brev)"
                            class="flex flex-wrap items-center gap-4 text-xs w-full lg:w-auto justify-end"
                        >
                            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
                                <input type="checkbox" x-model="brev.navn" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Navn
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
                                <input type="checkbox" x-model="brev.adresse" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Adresse
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
                                <input type="checkbox" x-model="brev.dato" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Dato
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
                                <input type="checkbox" x-model="brev.sagsnr" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Sagsnr
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
                                <input type="checkbox" x-model="brev.emne" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"> Emne
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-rose-600 font-bold">
                                <input type="checkbox" x-model="brev.skjulalle" @change="toggleSkjulAlle(brev)" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500"> Skjul alle
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer text-emerald-600 font-bold">
                                <input type="checkbox" x-model="brev.visalle" @change="toggleVisAlle(brev)" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500"> Vis alle
                            </label>

                            <button 
                                type="submit" 
                                class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition shadow-xs cursor-pointer ml-auto lg:ml-0"
                            >
                                <span x-show="!brev.submitting && !brev.success">Udfør</span>
                                <span x-show="brev.submitting">Gemmer...</span>
                                <span x-show="brev.success" class="text-emerald-400">Gemt!</span>
                            </button>
                        </form>
                    </li>
                </template>
            </ul>
        </div>

        <!-- Global Gem Sortering Knap -->
        <div class="flex justify-end pt-2">
            <button 
                type="button" 
                @click="saveSorting"
                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-xs cursor-pointer"
                x-text="sortingSubmitting ? 'Gemmer rækkefølge...' : 'Gem brev rækkefølge'"
            ></button>
        </div>
    </div>

    <script>
        function brevManager(initialBreve) {
            return {
                breve: initialBreve,
                draggingIndex: null,
                sortingSubmitting: false,

                dragStart(index) {
                    this.draggingIndex = index;
                },

                drop(targetIndex) {
                    if (this.draggingIndex === null || this.draggingIndex === targetIndex) return;
                    let movedItem = this.breve.splice(this.draggingIndex, 1)[0];
                    this.breve.splice(targetIndex, 0, movedItem);
                    this.draggingIndex = null;
                },

                toggleSkjulAlle(brev) {
                    if (brev.skjulalle) {
                        brev.adresse = false; brev.emne = false; brev.navn = false; brev.sagsnr = false; brev.dato = false; brev.visalle = false;
                    }
                },

                toggleVisAlle(brev) {
                    if (brev.visalle) {
                        brev.adresse = true; brev.emne = true; brev.navn = true; brev.sagsnr = true; brev.dato = true; brev.skjulalle = false;
                    }
                },

                submitForm(brev) {
                    brev.submitting = true;
                    fetch('/breve/filter/' + brev.id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            navn: brev.navn ? 1 : 0,
                            adresse: brev.adresse ? 1 : 0,
                            dato: brev.dato ? 1 : 0,
                            sagsnr: brev.sagsnr ? 1 : 0,
                            emne: brev.emne ? 1 : 0,
                            skjulalle: brev.skjulalle ? 1 : 0,
                            visalle: brev.visalle ? 1 : 0
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        brev.submitting = false;
                        brev.success = true;
                        setTimeout(() => brev.success = false, 2000);
                    })
                    .catch(err => {
                        brev.submitting = false;
                        alert('Kunne ikke gemme filter indstillinger.');
                    });
                },

                saveSorting() {
                    this.sortingSubmitting = true;
                    let ids = this.breve.map(b => b.id);

                    let formData = new FormData();
                    formData.append('brevpos', ids.join(','));
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('breve.sortering.update') }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.sortingSubmitting = false;
                        location.reload();
                    })
                    .catch(err => {
                        this.sortingSubmitting = false;
                        console.error(err);
                        alert('Kunne ikke gemme rækkefølgen.');
                    });
                }
            }
        }
    </script>
</x-layouts.app>