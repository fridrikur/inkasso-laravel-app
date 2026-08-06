<div>
    @foreach($sagervalglistetyper as $sagervalglistetype)
        <div>
            <label for={{ $sagervalglistetype->navn }}>{{ $sagervalglistetype->navn }}</label>
            <div>
                <select wire:model="form.{{ $sagervalglistetype->navn }}" class="field-sizing-content md:field-sizing-fixed ">
                    @foreach ($sagervalglister as $sagervalgliste)
                        @foreach ($sagervalgliste->sagervalglistetype as $liste)
                            @if($liste->pivot->type_id==$sagervalglistetype->id)
                                <option value="{{ $sagervalgliste->id }}">{{ $sagervalgliste->navn }}</option>
                            @endif
                        @endforeach    
                    @endforeach
                </select>
            </div>
        </div>
        @error('form.{{ $sagervalglistetype->navn }}') <span class="error">{{ $message }}</span> @enderror
    @endforeach    
</div>