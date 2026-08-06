<!-- Dynamic Form Renderer - Safe Version -->
<div>
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50"></div>
    
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $isEditMode ? 'Edit' : 'Create' }} {{ $layout->model_type }}
                    </h1>
                    <p class="text-gray-600 mt-1">Layout: {{ $layout->name }}</p>
                </div>
                
                <div class="flex gap-3">
                    <button wire:click="save" 
                            wire:loading.attr="disabled"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium shadow-sm">
                        <span wire:loading.remove>💾 Save</span>
                        <span wire:loading>⏳ Saving...</span>
                    </button>
                </div>
            </div>
            
            <!-- Dynamic Grid -->
            @if($layout->containers && count($layout->containers) > 0)
                <div class="grid gap-6" style="grid-template-columns: repeat({{ $layout->grid_dimensions['columns'] ?? 4 }}, 1fr);">
                    @foreach($layout->containers as $containerId => $container)
                        @if(isset($container['fields']) && !empty($container['fields']))
                            <!-- Container: {{ $container['title'] ?? 'Untitled Container' }} -->
                            <div class="{{ $container['style']['background'] ?? 'bg-gray-50' }} {{ $container['style']['padding'] ?? 'p-4' }} border {{ $container['style']['border'] ?? 'border-gray-300' }} rounded-lg">
                                <h3 class="text-lg font-semibold mb-4 text-gray-800">{{ $container['title'] ?? 'Container' }}</h3>
                                
                                <div class="space-y-4">
                                    @foreach($container['fields'] as $field)
                                        @if(isset($field['key']) && isset($field['label']) && isset($field['type']))
                                            <div class="field-wrapper">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                    {{ $field['label'] }}
                                                    @if(isset($field['required']) && $field['required'])
                                                        <span class="text-red-500">*</span>
                                                    @endif
                                                </label>
                                                
                                                @switch($field['type'])
                                                    @case('text')
                                                    @case('email')
                                                    @case('tel')
                                                        <input type="{{ $field['type'] }}" 
                                                               {!! $this->getWireModelForField($field) !!}
                                                               placeholder="{{ $field['label'] }}"
                                                               @if($this->isFieldReadonly($field)) readonly @endif
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm {{ $this->isFieldReadonly($field) ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500' }}">
                                                        @break
                                                        
                                                    @case('date')
                                                        <input type="date" 
                                                               {!! $this->getWireModelForField($field) !!}
                                                               @if($this->isFieldReadonly($field)) readonly @endif
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm {{ $this->isFieldReadonly($field) ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500' }}">
                                                        @break
                                                        
                                                    @case('currency')
                                                        <input type="text" 
                                                               {!! $this->getWireModelForField($field) !!}
                                                               placeholder="kr. 0,00"
                                                               @if($this->isFieldReadonly($field)) readonly @endif
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-right {{ $this->isFieldReadonly($field) ? 'bg-gray-100 cursor-not-allowed font-semibold' : 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500' }}">
                                                        @break
                                                        
                                                    @case('select')
                                                        @php
                                                            $options = $this->getSelectOptionsForField($field['key']);
                                                        @endphp
                                                        
                                                        <select {!! $this->getWireModelForField($field) !!}
                                                                @if($this->isFieldReadonly($field)) disabled @endif
                                                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm {{ $this->isFieldReadonly($field) ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500' }}">
                                                            <option value="">Select {{ $field['label'] }}...</option>
                                                            @if(is_array($options))
                                                                @foreach($options as $value => $label)
                                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @break
                                                        
                                                    @case('textarea')
                                                        <textarea {!! $this->getWireModelForField($field) !!}
                                                                  rows="{{ $field['rows'] ?? 3 }}"
                                                                  placeholder="{{ $field['label'] }}"
                                                                  @if($this->isFieldReadonly($field)) readonly @endif
                                                                  class="w-full border border-gray-300 rounded px-3 py-2 text-sm {{ $this->isFieldReadonly($field) ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500' }} resize-none"></textarea>
                                                        @break
                                                        
                                                    @default
                                                        <input type="text" 
                                                               {!! $this->getWireModelForField($field) !!}
                                                               placeholder="{{ $field['label'] }}"
                                                               @if($this->isFieldReadonly($field)) readonly @endif
                                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm {{ $this->isFieldReadonly($field) ? 'bg-gray-100 cursor-not-allowed' : 'focus:ring-2 focus:ring-blue-500 focus:border-blue-500' }}">
                                                @endswitch
                                                
                                                @error('form.' . $field['key'])
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <!-- No containers message -->
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">📋</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No form fields configured</h3>
                    <p class="text-gray-600 mb-6">This layout doesn't have any fields configured yet.</p>
                    <a href="{{ route('form-builder') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                        Configure Layout
                    </a>
                </div>
            @endif
            
            <!-- Footer Info -->
            @if($layout->containers && count($layout->containers) > 0)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <div>
                            @if($record)
                                Record ID: {{ $record->id }} | 
                                Last updated: {{ $record->updated_at ? $record->updated_at->format('d-m-Y H:i') : 'Never' }}
                            @else
                                New {{ $layout->model_type }} - not saved yet
                            @endif
                        </div>
                        
                        <div>
                            Layout: {{ $layout->name }} | 
                            Grid: {{ $layout->grid_dimensions['rows'] ?? 3 }}x{{ $layout->grid_dimensions['columns'] ?? 4 }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast Notifications Script -->
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('toast', (event) => {
            const toast = document.createElement('div');
            toast.className = `mb-2 p-4 rounded-lg shadow-lg max-w-md transition-all duration-300`;
            
            if (event.type === 'success') {
                toast.className += ' bg-green-100 border-l-4 border-green-500 text-green-700';
                toast.innerHTML = `
                    <div class="flex items-center">
                        <span class="text-lg mr-2">✅</span>
                        <span class="font-medium">${event.message}</span>
                    </div>
                `;
            } else if (event.type === 'error') {
                toast.className += ' bg-red-100 border-l-4 border-red-500 text-red-700';
                toast.innerHTML = `
                    <div class="flex items-center">
                        <span class="text-lg mr-2">❌</span>
                        <span class="font-medium">${event.message}</span>
                    </div>
                `;
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
                }, 5000);
            }
        });
    });
</script>