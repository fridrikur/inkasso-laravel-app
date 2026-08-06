<!-- Layout Preview - Show layout without editing -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Layout Preview</h1>
                <p class="text-gray-600 mt-1">{{ $layout->name }} - {{ $layout->model_type }}</p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('sager.form', $layout) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    🚀 Use This Layout
                </a>
                
                <a href="{{ route('form-builder') }}?load={{ $layout->id }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    ✏️ Edit Layout
                </a>
                
                <a href="{{ route('layouts.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    ← Back to Layouts
                </a>
            </div>
        </div>
        
        <!-- Layout Info -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <strong>Model:</strong> {{ $layout->model_type }}<br>
                    <strong>Grid:</strong> {{ $layout->grid_dimensions['rows'] }}x{{ $layout->grid_dimensions['columns'] }}
                </div>
                <div>
                    <strong>Created:</strong> {{ $layout->created_at->format('M j, Y H:i') }}<br>
                    <strong>Creator:</strong> {{ $layout->creator->name ?? 'Unknown' }}
                </div>
                <div>
                    <strong>Fields:</strong> {{ count($layout->getAllFields()) }}<br>
                    <strong>Status:</strong> 
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                        {{ $layout->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            
            @if($layout->description)
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <strong>Description:</strong> {{ $layout->description }}
                </div>
            @endif
        </div>
        
        <!-- Form Preview -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-center text-gray-600">Form Preview</h3>
            
            <div class="grid gap-6" style="grid-template-columns: repeat({{ $layout->grid_dimensions['columns'] }}, 1fr);">
                @foreach($layout->containers as $containerId => $container)
                    @if(!empty($container['fields']))
                        <!-- Container: {{ $container['title'] }} -->
                        <div class="{{ $container['style']['background'] }} {{ $container['style']['padding'] }} border {{ $container['style']['border'] }} rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-gray-800">{{ $container['title'] }}</h4>
                            
                            <div class="space-y-4">
                                @foreach($container['fields'] as $field)
                                    <div class="field-preview">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $field['label'] }}
                                            @if(isset($field['required']) && $field['required'])
                                                <span class="text-red-500">*</span>
                                            @endif
                                            <span class="text-xs text-gray-500 ml-2">({{ $field['type'] }})</span>
                                        </label>
                                        
                                        @switch($field['type'])
                                            @case('text')
                                            @case('email')
                                            @case('tel')
                                                <input type="{{ $field['type'] }}" 
                                                       placeholder="{{ $field['label'] }}"
                                                       disabled
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                                                @break
                                                
                                            @case('date')
                                                <input type="date" 
                                                       disabled
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                                                @break
                                                
                                            @case('currency')
                                                <input type="text" 
                                                       placeholder="kr. 0,00"
                                                       disabled
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm text-right bg-gray-50 cursor-not-allowed">
                                                @break
                                                
                                            @case('select')
                                                <select disabled class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                                                    <option value="">Select {{ $field['label'] }}...</option>
                                                    <option value="option1">Sample Option 1</option>
                                                    <option value="option2">Sample Option 2</option>
                                                </select>
                                                @break
                                                
                                            @case('textarea')
                                                <textarea rows="{{ $field['rows'] ?? 3 }}"
                                                          placeholder="{{ $field['label'] }}"
                                                          disabled
                                                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 cursor-not-allowed resize-none"></textarea>
                                                @break
                                                
                                            @default
                                                <input type="text" 
                                                       placeholder="{{ $field['label'] }}"
                                                       disabled
                                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 cursor-not-allowed">
                                        @endswitch
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            
            <!-- Preview Save Button -->
            <div class="mt-8 flex justify-end">
                <button disabled class="bg-gray-400 text-white px-8 py-3 rounded-lg font-medium cursor-not-allowed">
                    💾 Save {{ $layout->model_type }} (Preview Mode)
                </button>
            </div>
        </div>
        
        <!-- Field Summary -->
        <div class="mt-6 bg-blue-50 rounded-lg p-4">
            <h4 class="font-semibold text-blue-900 mb-3">Field Summary</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    $fieldsByCategory = collect($layout->getAllFields())->groupBy('category');
                @endphp
                
                @foreach($fieldsByCategory as $category => $fields)
                    <div class="bg-white rounded p-3">
                        <h5 class="font-medium text-gray-800 mb-2 capitalize">
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
                            ({{ count($fields) }})
                        </h5>
                        <div class="text-sm text-gray-600">
                            @foreach($fields as $field)
                                <div class="flex justify-between">
                                    <span>{{ $field['label'] }}</span>
                                    <span class="text-xs bg-gray-200 px-1 rounded">{{ $field['type'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection