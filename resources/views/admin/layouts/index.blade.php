<!-- Layout Index - List all saved layouts -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Form Layouts</h1>
                <p class="text-gray-600 mt-1">Manage your custom form layouts</p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('form-builder') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    ➕ Create New Layout
                </a>
            </div>
        </div>
        
        @if($layouts->isEmpty())
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No layouts created yet</h3>
                <p class="text-gray-600 mb-6">Create your first form layout to get started</p>
                <a href="{{ route('form-builder') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                    Create Layout
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($layouts as $layout)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $layout->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $layout->model_type }}</p>
                            </div>
                            
                            <div class="flex gap-1">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                                    {{ $layout->grid_dimensions['rows'] }}x{{ $layout->grid_dimensions['columns'] }}
                                </span>
                            </div>
                        </div>
                        
                        @if($layout->description)
                            <p class="text-sm text-gray-600 mb-4">{{ $layout->description }}</p>
                        @endif
                        
                        <div class="text-xs text-gray-500 mb-4">
                            Created: {{ $layout->created_at->format('M j, Y') }} by {{ $layout->creator->name ?? 'Unknown' }}
                        </div>
                        
                        <div class="flex gap-2">
                            <!-- Use Layout -->
                            <a href="{{ route('sager.form', $layout) }}" 
                               class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded text-sm">
                                🚀 Use Layout
                            </a>
                            
                            <!-- Preview -->
                            <a href="{{ route('layouts.preview', $layout) }}" 
                               class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-3 rounded text-sm">
                                👁️
                            </a>
                            
                            <!-- Edit -->
                            <a href="{{ route('form-builder') }}?load={{ $layout->id }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded text-sm">
                                ✏️
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection