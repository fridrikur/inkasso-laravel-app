<div class="container mx-auto p-6">
    <div class="bg-white shadow-lg rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-800">Manage Sager Field Settings</h2>
            <p class="text-gray-600 mt-1">Configure field properties, visibility, and types for the Sager form</p>
        </div>

        <form wire:submit.prevent="save" class="p-6">
            @if (session()->has('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Field Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Alias
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Visible
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Required
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Readonly
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Field Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Section
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sort Order
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($settings as $key => $field)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $field['field_name'] }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" 
                                        wire:model.lazy="settings.{{ $key }}.alias"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                        placeholder="Enter alias">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="checkbox" 
                                        wire:model="settings.{{ $key }}.visible"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="checkbox" 
                                        wire:model="settings.{{ $key }}.required"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="checkbox" 
                                        wire:model="settings.{{ $key }}.readonly"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select wire:model="settings.{{ $key }}.field_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="tel">Telephone</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                        <option value="datetime-local">DateTime</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="select">Select</option>
                                        <option value="relation">Relation</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="radio">Radio</option>
                                        <option value="file">File</option>
                                        <option value="hidden">Hidden</option>
                                    </select>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select wire:model="settings.{{ $key }}.section"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <option value="general">General Fields</option>
                                        <option value="financial">Financial Fields</option>
                                        <option value="relation">Relation Fields</option>
                                        <option value="debitor">Debitor Fields</option>
                                        <option value="contact">Contact Fields</option>
                                        <option value="notes">Notes Fields</option>
                                    </select>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="number" 
                                        wire:model.lazy="settings.{{ $key }}.sort_order"
                                        class="w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm text-center"
                                        min="0"
                                        step="1">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Total fields: {{ count($settings) }}
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" 
                        wire:click="loadSettings"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Reset
                    </button>
                    
                    <button type="submit"
                        class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Field Type Legend --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-medium text-blue-900 mb-3">Field Type Guide</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div>
                <strong class="text-blue-800">Text:</strong> Standard text input
            </div>
            <div>
                <strong class="text-blue-800">Email:</strong> Email validation
            </div>
            <div>
                <strong class="text-blue-800">Tel:</strong> Phone number input
            </div>
            <div>
                <strong class="text-blue-800">Number:</strong> Numeric input with formatting
            </div>
            <div>
                <strong class="text-blue-800">Date:</strong> Date picker
            </div>
            <div>
                <strong class="text-blue-800">Textarea:</strong> Multi-line text
            </div>
            <div>
                <strong class="text-blue-800">Select:</strong> Dropdown with options
            </div>
            <div>
                <strong class="text-blue-800">Relation:</strong> Database relationship
            </div>
            <div>
                <strong class="text-blue-800">Checkbox:</strong> Boolean true/false
            </div>
        </div>
    </div>
</div>