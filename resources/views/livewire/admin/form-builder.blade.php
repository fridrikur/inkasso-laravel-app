<!-- Form Builder - Stable Version Without DOM Issues -->
<div class="min-h-screen bg-gray-50 p-4" wire:key="form-builder-{{ $refreshCounter }}">
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>
    
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm mb-6 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Form Builder</h1>
                    <p class="text-gray-600 mt-1">Design custom form layouts with drag & drop</p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="togglePreview" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg">
                        {{ $previewMode ? '🔧 Edit Mode' : '👁️ Preview' }}
                    </button>
                    <button wire:click="generateComponent" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                        🚀 Generate Code
                    </button>
                </div>
            </div>
        </div>

        @if(!$previewMode)
            <!-- Configuration Panel -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                <!-- Grid Configuration -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-4">📐 Grid Configuration</h3>
                    
                    <div class="space-y-4">
                        <!-- Layout Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layout Name</label>
                            <input type="text" 
                                   wire:model="layoutName"
                                   placeholder="Enter layout name..."
                                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <!-- Model Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                            <select wire:model.live="selectedModel" 
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                @foreach($availableModels as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Grid Dimensions -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rows</label>
                                <input type="number" 
                                       wire:model.live="gridRows"
                                       wire:change="updateGridDimensions"
                                       min="1" max="6" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Columns</label>
                                <input type="number" 
                                       wire:model.live="gridColumns"
                                       wire:change="updateGridDimensions"
                                       min="1" max="6" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea wire:model="layoutDescription"
                                      rows="3"
                                      placeholder="Layout description..."
                                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <!-- Save Button -->
                        <button wire:click="saveLayout" 
                                wire:loading.attr="disabled"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">
                            <span wire:loading.remove>💾 Save Layout</span>
                            <span wire:loading>⏳ Saving...</span>
                        </button>
                        
                        <!-- Manual Refresh Button -->
                        <button wire:click="refreshComponent" 
                                class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 rounded-lg font-medium text-sm">
                            🔄 Refresh Component
                        </button>
                    </div>
                </div>

                <!-- Available Fields -->
                <div class="lg:col-span-3 bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-4">🧩 Available Fields - {{ $availableModels[$selectedModel] }}</h3>
                    
                    <!-- Field Categories -->
                    @php
                        $categories = collect($availableFields)->groupBy('category');
                    @endphp
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($categories as $category => $fields)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-800 mb-3 capitalize">
                                    @switch($category)
                                        @case('basic') 📝 Basic @break
                                        @case('financial') 💰 Financial @break
                                        @case('dates') 📅 Dates @break
                                        @case('contact') 📞 Contact @break
                                        @case('relations') 🔗 Relations @break
                                        @case('asset') 🚗 Asset @break
                                        @case('billing') 🧾 Billing @break
                                        @default {{ ucfirst($category) }}
                                    @endswitch
                                </h4>
                                
                                <div class="space-y-2">
                                    @foreach($fields as $fieldKey => $field)
                                        <!-- Stable field item with simple wire:key -->
                                        <div class="field-item bg-gray-50 border border-gray-200 rounded p-2 cursor-move hover:bg-blue-50 hover:border-blue-300 transition-colors"
                                             wire:key="field-{{ $fieldKey }}"
                                             draggable="true"
                                             data-field-key="{{ $fieldKey }}"
                                             data-field-label="{{ $field['label'] }}"
                                             data-field-type="{{ $field['type'] }}"
                                             data-field-category="{{ $field['category'] }}">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium">{{ $field['label'] }}</span>
                                                <div class="flex flex-col items-end">
                                                    <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded mb-1">
                                                        {{ $field['type'] }}
                                                    </span>
                                                    <span class="text-xs text-blue-600 font-mono">
                                                        {{ $fieldKey }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Grid -->
        <div class="bg-white rounded-lg shadow-sm p-6" wire:key="grid-{{ $refreshCounter }}">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold">
                    @if($previewMode)
                        👁️ Form Preview - {{ $layoutName ?: 'Untitled Layout' }}
                    @else
                        🎯 Form Grid - {{ $gridRows }}x{{ $gridColumns }}
                    @endif
                </h3>
                
                @if(!$previewMode)
                    <div class="text-sm text-gray-600">
                        Drag fields from above into containers below
                        <span class="ml-2 text-xs bg-blue-100 px-2 py-1 rounded">
                            Refresh: {{ $refreshCounter }}
                        </span>
                    </div>
                @endif
            </div>
            
            <!-- Grid Container -->
            <div class="grid gap-4" style="grid-template-columns: repeat({{ $gridColumns }}, 1fr);">
                @for($row = 0; $row < $gridRows; $row++)
                    @for($col = 0; $col < $gridColumns; $col++)
                        @php
                            $containerId = "container_{$row}_{$col}";
                            $container = $containers[$containerId] ?? null;
                        @endphp
                        
                        @if($container)
                            <!-- Stable container with simple wire:key -->
                            <div class="container-drop-zone {{ $container['style']['background'] }} {{ $container['style']['border'] }} {{ $container['style']['padding'] }} border-2 border-dashed rounded-lg min-h-32 transition-all duration-200"
                                 wire:key="container-{{ $containerId }}"
                                 data-container-id="{{ $containerId }}"
                                 data-row="{{ $row }}"
                                 data-col="{{ $col }}">
                                
                                @if(!$previewMode)
                                    <!-- Container Header (Edit Mode) -->
                                    <div class="flex justify-between items-center mb-3 pb-2 border-b border-gray-200">
                                        <input type="text" 
                                               wire:model.blur="containers.{{ $containerId }}.title"
                                               class="text-sm font-medium bg-transparent border-none p-0 focus:ring-0 focus:border-blue-500"
                                               placeholder="Container Title">
                                        
                                        <div class="flex gap-1">
                                            <!-- Container Style Options -->
                                            <select wire:model.live="containers.{{ $containerId }}.style.background"
                                                    class="text-xs border-gray-300 rounded">
                                                <option value="bg-gray-50">Gray</option>
                                                <option value="bg-blue-50">Blue</option>
                                                <option value="bg-green-50">Green</option>
                                                <option value="bg-yellow-50">Yellow</option>
                                                <option value="bg-purple-50">Purple</option>
                                                <option value="bg-red-50">Red</option>
                                            </select>
                                            
                                            <!-- Debug button -->
                                            <button wire:click="debugContainer('{{ $containerId }}')"
                                                    class="text-xs text-blue-600 hover:text-blue-800">
                                                🐛
                                            </button>
                                            
                                            <button wire:click="clearContainer('{{ $containerId }}')"
                                                    class="text-xs text-red-600 hover:text-red-800">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <!-- Container Header (Preview Mode) -->
                                    @if(!empty($container['fields']))
                                        <h4 class="text-lg font-semibold mb-4 text-gray-800">{{ $container['title'] }}</h4>
                                    @endif
                                @endif
                                
                                <!-- Container Fields -->
                                <div class="space-y-3">
                                    @if(empty($container['fields']))
                                        @if(!$previewMode)
                                            <div class="text-center text-gray-400 py-8">
                                                <div class="text-2xl mb-2">📦</div>
                                                <div class="text-sm">Drop fields here</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $containerId }}</div>
                                                <div class="text-xs text-gray-400 mt-1">Fields: {{ count($container['fields']) }}</div>
                                            </div>
                                        @endif
                                    @else
                                        @foreach($container['fields'] as $fieldIndex => $field)
                                            <!-- Stable field with simple wire:key -->
                                            <div class="field-in-container bg-white border border-gray-200 rounded p-3 {{ !$previewMode ? 'hover:border-blue-300' : '' }}"
                                                 wire:key="field-{{ $containerId }}-{{ $fieldIndex }}">
                                                @if(!$previewMode)
                                                    <!-- Edit Mode Field -->
                                                    <div class="flex justify-between items-center">
                                                        <div>
                                                            <span class="font-medium text-sm">{{ $field['label'] }}</span>
                                                            <span class="text-xs text-gray-500 ml-2">({{ $field['type'] }})</span>
                                                            <span class="text-xs text-blue-600 font-mono ml-2">{{ $field['key'] }}</span>
                                                        </div>
                                                        <button wire:click="removeFieldFromContainer('{{ $containerId }}', {{ $fieldIndex }})"
                                                                class="text-red-600 hover:text-red-800 text-sm">
                                                            ❌
                                                        </button>
                                                    </div>
                                                @else
                                                    <!-- Preview Mode Field -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            {{ $field['label'] }}
                                                        </label>
                                                        
                                                        @switch($field['type'])
                                                            @case('text')
                                                            @case('email')
                                                            @case('tel')
                                                                <input type="{{ $field['type'] }}" 
                                                                       placeholder="{{ $field['label'] }}"
                                                                       @if(isset($field['readonly']) && $field['readonly']) readonly @endif
                                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm {{ isset($field['readonly']) && $field['readonly'] ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-2 focus:ring-blue-500' }}">
                                                                @break
                                                                
                                                            @case('date')
                                                                <input type="date" 
                                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                                                @break
                                                                
                                                            @case('currency')
                                                                <input type="text" 
                                                                       placeholder="kr. 0,00"
                                                                       @if(isset($field['readonly']) && $field['readonly']) readonly @endif
                                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-right {{ isset($field['readonly']) && $field['readonly'] ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-2 focus:ring-blue-500' }}">
                                                                @break
                                                                
                                                            @case('select')
                                                                <select class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                                                    <option value="">Select {{ $field['label'] }}...</option>
                                                                    <option value="option1">Option 1</option>
                                                                    <option value="option2">Option 2</option>
                                                                </select>
                                                                @break
                                                                
                                                            @default
                                                                <input type="text" 
                                                                       placeholder="{{ $field['label'] }}"
                                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                                        @endswitch
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endfor
                @endfor
            </div>
            
            @if($previewMode && !empty(array_filter(array_column($containers, 'fields'))))
                <!-- Preview Save Button -->
                <div class="mt-8 flex justify-end">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium">
                        💾 Save {{ $availableModels[$selectedModel] }}
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Stable JavaScript without $refresh calls -->
<script>
document.addEventListener('livewire:init', () => {
    let draggedElement = null;
    let draggedFieldData = null;
    
    // Handle field dragging from available fields
    document.addEventListener('dragstart', (e) => {
        if (e.target.classList.contains('field-item')) {
            draggedElement = e.target;
            
            // Extract field data from data attributes
            draggedFieldData = {
                key: e.target.getAttribute('data-field-key'),
                label: e.target.getAttribute('data-field-label'),
                type: e.target.getAttribute('data-field-type'),
                category: e.target.getAttribute('data-field-category')
            };
            
            e.target.style.opacity = '0.5';
            
            // Debug logging
            console.log('🚀 Drag started:', {
                element: e.target,
                fieldData: draggedFieldData
            });
            
            // Validate field key
            if (!draggedFieldData.key || draggedFieldData.key === 'null' || draggedFieldData.key === '') {
                console.error('❌ Invalid field key detected:', draggedFieldData);
                alert('Error: Invalid field key. Please refresh the page and try again.');
                return false;
            }
        }
    });
    
    document.addEventListener('dragend', (e) => {
        if (e.target.classList.contains('field-item')) {
            e.target.style.opacity = '1';
            draggedElement = null;
            draggedFieldData = null;
        }
    });
    
    // Handle container drop zones
    document.addEventListener('dragover', (e) => {
        if (e.target.closest('.container-drop-zone')) {
            e.preventDefault();
            e.target.closest('.container-drop-zone').classList.add('border-blue-500', 'bg-blue-50');
        }
    });
    
    document.addEventListener('dragleave', (e) => {
        if (e.target.closest('.container-drop-zone')) {
            e.target.closest('.container-drop-zone').classList.remove('border-blue-500', 'bg-blue-50');
        }
    });
    
    document.addEventListener('drop', (e) => {
        const dropZone = e.target.closest('.container-drop-zone');
        if (dropZone && draggedFieldData) {
            e.preventDefault();
            
            const containerId = dropZone.getAttribute('data-container-id');
            
            // Debug logging
            console.log('📦 Drop event:', {
                containerId: containerId,
                fieldKey: draggedFieldData.key,
                fieldData: draggedFieldData
            });
            
            // Validate before calling Livewire
            if (!draggedFieldData.key || !containerId) {
                console.error('❌ Missing required data:', {
                    fieldKey: draggedFieldData.key,
                    containerId: containerId
                });
                alert('Error: Missing field or container data');
                return;
            }
            
            // Call Livewire method to add field to container
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'))
                .call('addFieldToContainer', draggedFieldData.key, containerId)
                .then((result) => {
                    console.log('✅ Field added successfully:', result);
                })
                .catch((error) => {
                    console.error('❌ Error adding field:', error);
                    alert('Error adding field: ' + error.message);
                });
            
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        }
    });
    
    // Listen for field-added-success event
    Livewire.on('field-added-success', () => {
        console.log('🎉 Field added successfully - component will refresh automatically');
    });
    
    // Toast notifications
    Livewire.on('toast', (event) => {
        const toast = document.createElement('div');
        toast.className = `mb-2 p-4 rounded-lg shadow-lg max-w-md transition-all duration-300`;
        
        if (event.type === 'success') {
            toast.className += ' bg-green-100 border-l-4 border-green-500 text-green-700';
            toast.innerHTML = `✅ ${event.message}`;
        } else if (event.type === 'error') {
            toast.className += ' bg-red-100 border-l-4 border-red-500 text-red-700';
            toast.innerHTML = `❌ ${event.message}`;
        } else if (event.type === 'info') {
            toast.className += ' bg-blue-100 border-l-4 border-blue-500 text-blue-700';
            toast.innerHTML = `ℹ️ ${event.message}`;
        } else if (event.type === 'warning') {
            toast.className += ' bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700';
            toast.innerHTML = `⚠️ ${event.message}`;
        }
        
        const container = document.getElementById('toast-container');
        if (container) {
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }, 4000);
        }
    });
    
    // Handle code generation modal
    Livewire.on('show-generated-code', (event) => {
        // Create modal to show generated code
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-full overflow-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Generated Code</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">✕</button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h4 class="font-semibold mb-2">Livewire Component (PHP)</h4>
                            <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-64"><code>${event.component.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</code></pre>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold mb-2">Blade View (HTML)</h4>
                            <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-64"><code>${event.view.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</code></pre>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button onclick="this.closest('.fixed').remove()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">Close</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    });
});
</script>