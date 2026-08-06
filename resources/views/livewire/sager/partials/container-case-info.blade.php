<!-- Pure Livewire 3 - No JavaScript -->

<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">🏢 Kreditor Lookup - Pure Livewire 3</h1>
        
        <!-- Success/Error Messages -->
        <div id="toast-container" class="fixed top-4 right-4 z-50"></div>
        
        <!-- Kreditor Lookup Section -->
        <div class="mb-8 bg-yellow-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">🔍 Kreditor Lookup</h3>
            
            <!-- Kreditor LotusID Input - Pure Livewire -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kreditor Nummer (LotusID) <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       wire:model.blur="form.kreditor_lotusID"
                       placeholder="Indtast kreditor nummer..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Lookup sker automatisk når du forlader feltet</p>
            </div>
            
            <!-- Kreditor Navn (Read-only) -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kreditor Navn</label>
                <input type="text" 
                       wire:model="form.kreditor_navn" 
                       readonly
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed">
            </div>
        </div>

        <!-- Sagsbehandler Section -->
        @if(!empty($this->selectOptions['sagsbehandler']))
            <div class="mb-8 bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold mb-4">👨‍💼 Sagsbehandler</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sagsbehandler <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="form.sagsbehandler" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Vælg sagsbehandler...</option>
                        @foreach($this->selectOptions['sagsbehandler'] as $id => $navn)
                            <option value="{{ $id }}">{{ $navn }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                <p class="text-gray-500 italic">💡 Indtast kreditor LotusID for at se sagsbehandlere</p>
            </div>
        @endif

        <!-- Date Field Test -->
        <div class="mb-8 bg-green-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">📅 Date Field Test</h3>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Test Date Field</label>
                <input type="date" 
                       wire:model.live="form.sagsnr"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Værdi: {{ $form->sagsnr ?? 'NULL' }}</p>
            </div>
        </div>

        <!-- Debug Info -->
        <div class="mt-8 bg-gray-100 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">🔍 Debug Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <strong>Kreditor LotusID:</strong> {{ $form->kreditor_lotusID ?? 'NULL' }}<br>
                    <strong>Kreditor Navn:</strong> {{ $form->kreditor_navn ?? 'NULL' }}<br>
                    <strong>Kreditor ID:</strong> {{ $form->kreditor ?? 'NULL' }}<br>
                </div>
                <div>
                    <strong>Sagsbehandler ID:</strong> {{ $form->sagsbehandler ?? 'NULL' }}<br>
                    <strong>Sagsbehandlere Count:</strong> {{ count($this->selectOptions['sagsbehandler'] ?? []) }}<br>
                    <strong>Methods exist:</strong><br>
                    • onKreditorChanged: {{ method_exists($this, 'onKreditorChanged') ? 'YES ✅' : 'NO ❌' }}<br>
                    • updatedFormKreditorLotusID: {{ method_exists($this, 'updatedFormKreditorLotusID') ? 'YES ✅' : 'NO ❌' }}<br>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex flex-wrap gap-3">
            <button wire:click="save" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                💾 Gem Sag
            </button>
        </div>
    </div>
</div>