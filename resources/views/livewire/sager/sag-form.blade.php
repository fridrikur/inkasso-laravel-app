<form wire:submit="save">
        
        <div class="columns-xs">
            <div>
                <div>
                    <label for="sagsnummer">Sagsnummer</label>
                    <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model.blur="form.sagsnr" placeholder="sagsnummer"></div>
                    @error('form.sagsnr') <span class="error">{{ $message }}</span> @enderror
                    <input type="hidden" name="token" wire:model="form.token">
                    @error('form.token') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="kreditor">Kreditor</label>
                    <div>
                        <select wire:model="form.kreditor" class="field-sizing-content md:field-sizing-fixed" wire:change="selectKreditorSagsbehandler($event.target.value)" @change="if(!confirm('Er du sikker på at du vil?')) $event.preventDefault()">
                            @foreach (\App\Models\Kreditorer::all() as $state)
                            <option value="{{ $state->id }}" >{{ $state->navn }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.kreditor') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="sagsbehandler">Sagsbehandler</label>
                    <div>
                        <select wire:model="form.sagsbehandler" class="field-sizing-content md:field-sizing-fixed ">
                            @foreach ($sagsbehandlere as $sagsbehandler)
                                @foreach ($sagsbehandler->kreditor as $sagsbehandler)
                                    @if ($sagsbehandler->pivot->kreditor_id == $this->form->kreditor)
                                        @foreach ($sagsbehandler->pivot->where('sagsbehandler_id',  $sagsbehandler->pivot->sagsbehandler_id)->get()  as $state)
                                            <option value="{{ $state->id }}">{{ $sagsbehandler->navn }}</option>
                                        @endforeach
                                    @endif  
                                @endforeach
                                @endforeach
                        </select>
                </div>
                <div>
                    <label for="status">Status</label>
                    <div>
                        <select wire:model="form.status" class="field-sizing-content md:field-sizing-fixed ">
                            @foreach (\App\Models\Status::all() as $state) 
                                <option value="{{ $state->id }}">{{ $state->tekst }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('form.status') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="navn">Navn</label>
                    <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.navn" placeholder="navn" name="navn"></div>
                    @error('form.navn') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="pnr">CPR</label>
                    <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.pnr" placeholder="pnr" name="pnr"></div>
                    @error('form.pnr') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="tlf">Tlf</label>
                    <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.tlf" placeholder="tlf" name="tlf"></div>
                    @error('form.tlf') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="mobil">Mobil</label>
                    <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.mobil" placeholder="mobil" name="mobil"></div>
                    @error('form.mobil') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="co">Co</label>
                    <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.co" placeholder="co" name="co"></div>
                    @error('form.co') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div><label for="postnr">Postnummer</label>
                <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.postnr" placeholder="postnummer" name="postnr"></div>
                @error('form.postnr') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="adresse">Adresse</label>
                <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.adresse" placeholder="adresse" name="adresse"></div>
                @error('form.adresse') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="afdragsordning">Afdragsordning</label><br>
                <input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.afdragsordning"></input>
                @error('form.afdragsordning') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="hovdstol">Hovedstol</label>
                <div><input type="text" class="addThis field-sizing-content md:field-sizing-fixed " placeholder="hovedstol" name="hovedstol" id="hovedstol" wire:model="form.hovedstol" x-mask:dynamic="$money($input, ',')" wire:change="$js.addThis('hovedstol')" x-format-number></div>
                @error('form.hovedstol') <span class="error">{{ $message }}</span> @enderror
                <div >
            </div>
            </div>
            <div><label for="renter">Renter</label>
            <div><input type="text" class="addThis field-sizing-content md:field-sizing-fixed " placeholder="renter" name="renter" id="renter" wire:model="form.renter" x-mask:dynamic="$money($input, ',')" wire:change="$js.addThis('renter')" x-format-number></div>
            @error('form.renter') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="gebyr">Gebyr</label>
                <div><input type="text" class="addThis field-sizing-content md:field-sizing-fixed " placeholder="gebyr" name="gebyr" id="gebyr" wire:model="form.gebyr" x-mask:dynamic="$money($input, ',')" wire:change="$js.addThis('gebyr')" x-format-number></div>
                @error('form.gebyr') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="stelnr">Stelnr</label>
                <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.stelnr" placeholder="stelnr" name="stelnr"></div>
                @error('form.stelnr') <span class="error">{{ $message }}</span> @enderror
            </div>
                    <div><label for="ialt">Ialt</label>
                        <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.ialt" placeholder="ialt" id="ialt" x-mask:dynamic="$money($input, ',')" x-format-number></div>
                         @script
                        <script>    
                            $js('addThis', (obj) => {
                                var v = document.getElementById(obj).value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                document.getElementById(obj).value = new Intl.NumberFormat("de-DE").format(v);
                                var hovedstol = document.getElementById('hovedstol').value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                var renter = document.getElementById('renter').value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                var gebyr = document.getElementById('gebyr').value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                var total = parseFloat(hovedstol) + parseFloat(renter) + parseFloat(gebyr);
                                var ialt = document.getElementById('ialt').value = formatNumber(total);
                                var hovedstol = document.getElementById('hovedstol').value = formatNumber(hovedstol);
                                var renter = document.getElementById('renter').value = formatNumber(renter);
                                var gebyr = document.getElementById('gebyr').value = formatNumber(gebyr);
                                
                                
                                
                                function formatNumber(value) {
                                    let parts = value.toString().split('.');
                                    let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    let decimalPart = parts[1] ? parts[1].padEnd(2, '0').slice(0, 2) : '00';
                                    return integerPart + ',' + decimalPart;
                                }
                            });
                            $js('deductThis', (obj) => {
                                var v = document.getElementById(obj).value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                document.getElementById(obj).value = new Intl.NumberFormat("de-DE").format(v);
                                var indbetalt = document.getElementById('indbetalt').value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                var restgaeld = document.getElementById('restgaeld').value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                //ialt feltet må ikke være tomt
                                var ialt = document.getElementById('ialt').value.toString().replace(/\./g, '').replace(/\,/g, '.');
                                var total = parseFloat(ialt) - parseFloat(indbetalt);
                                var indbetalt = document.getElementById('indbetalt').value = formatNumber(indbetalt);
                                var restgaeld = document.getElementById('restgaeld').value = formatNumber(total);
                                function formatNumber(value) {
                                    let parts = value.toString().split('.');
                                    let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    let decimalPart = parts[1] ? parts[1].padEnd(2, '0').slice(0, 2) : '00';
                                    return integerPart + ',' + decimalPart;
                                }
                            });
                        </script>
                        @endscript
                        @error('form.ialt') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div><label for="startgebyr">Startgebyr</label>
                        <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.startgebyr" placeholder="startgebyr" name="startgebyr" x-mask:dynamic="$money($input, ',')"></div>
                        @error('form.startgebyr') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div><label for="restgaeld">Restgæld</label>
                        <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.restgaeld" placeholder="restgaeld" name="restgaeld" id="restgaeld" x-mask:dynamic="$money($input, ',')" x-format-number></div>
                        @error('form.restgaeld') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                    <label for="restgaeld_dkg">Restgæld DKG</label>
                    <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.restgaeld_dkg" placeholder="restgaeld_dkg" name="restgaeld_dkg" x-format-number></div>
                        @error('form.restgaeld_dkg') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div><label for="indbetalt">Indbetalt</label>
                        <div><input type="text" class="deductThis field-sizing-content md:field-sizing-fixed " wire:model="form.indbetalt" placeholder="indbetalt" name="indbetalt" id="indbetalt" wire:change="$js.deductThis('indbetalt')" x-format-number></div>
                        @error('form.indbetalt') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div><label for="aktiv">Aktiv</label>
                        <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.aktiv" placeholder="aktiv" name="aktiv"></div>
                        @error('form.aktiv') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div><label for="mdlydelse">Månedelig ydelse</label>
                        <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.mdlydelse" placeholder="mdlydelse" name="mdlydelse"></div>
                        @error('form.mdlydelse') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div><label for="n_mdlydelse">Næste månedlige ydelse</label>
                        <div><input type="text" class="field-sizing-content md:field-sizing-fixed " wire:model="form.n_mdlydelse" placeholder="n_mdlydelse" name="n_mdlydelse" x-mask:dynamic="$money($input, ',')" x-format-number></div>
                        @error('form.n_mdlydelse') <span class="error">{{ $message }}</span> @enderror
                    </div>
                <div>
                    <label for="konsulent">Konsulent</label>
                    <div><select wire:model="form.konsulent" class="field-sizing-content md:field-sizing-fixed ">
                        @foreach (\App\Models\Konsulenter::withCount('skjultkonsulent')->having('skjultkonsulent_count',"=", "0")->get() as $state) 
                        <option value="{{ $state->id }}">{{ $state->navn }}</option>
                        @endforeach    
                    </select></div>
                    @error('form.konsulent') <span class="error">{{ $message }}</span> @enderror
                </div>
                    <div>
                        <label for="faktureret">Faktureret</label>
                        <div><input type="date" wire:model="form.faktureret" placeholder="faktureret" name="faktureret"></div>
                        @error('form.faktureret') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="fakturadato">Fakturadato</label>
                        <div><input type="date" wire:model="form.fakturadato" placeholder="fakturadato" name="fakturadato"></div>
                        @error('form.fakturadato') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="modtaget">Modtaget</label>
                        <div><input type="date" wire:model="form.modtaget" placeholder="modtaget" name="modtaget"></div>
                        @error('form.modtaget') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="senesterapport">Seneste rapport</label>
                        <div><input type="date" wire:model="form.senesterapport" placeholder="senesterapport" name="senesterapport"></div>
                        @error('form.senesterapport') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="opgivet">Opgivet</label>
                        <div><input type="date" wire:model="form.opgivet" placeholder="opgivet" name="opgivet"></div>
                        @error('form.opgivet') <span class="error">{{ $message }}</span> @enderror
                    </div>
                        @if($sagervalglistetyper !=null)
                            <liveWire:sagervalglistetype.listesagervalglistetype />
                        @endif
                </div></div>
            <button type="submit" class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded">
            GEM                
            </button>
        </div>
</form>