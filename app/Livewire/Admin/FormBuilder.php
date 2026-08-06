<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\FormLayout;
use App\Models\Sager;
use App\Models\Kreditorer;
use App\Models\Debitorer;
use App\Models\Sagsbehandler;
use App\Models\Konsulenter;
use App\Models\Status;
use App\Models\Bemaerkning;
use App\Models\Afslutning;
use App\Models\Ktr;

class FormBuilder extends Component
{
    // Grid configuration
    public $gridRows = 3;
    public $gridColumns = 4;
    public $selectedModel = 'Sager';
    public $layoutName = '';
    public $layoutDescription = '';
    
    // Grid structure - array of containers
    public $containers = [];
    
    // Available fields for selected model
    public $availableFields = [];
    
    // Available models
    public $availableModels = [
        'Sager' => 'Sager',
        'Kreditorer' => 'Kreditor',
        'Debitorer' => 'Debitor', 
        'Sagsbehandler' => 'Sagsbehandler',
        'Konsulenter' => 'Konsulent'
    ];
    
    // Drag and drop state
    public $draggedContainer = null;
    public $draggedField = null;
    
    // Preview mode
    public $previewMode = false;
    
    // Force refresh counter to trigger re-renders
    public $refreshCounter = 0;
    
    public function mount()
    {
        $this->initializeGrid();
        $this->loadAvailableFields();
    }
    
    /**
     * Initialize empty grid structure
     */
    public function initializeGrid()
    {
        $this->containers = [];
        
        for ($row = 0; $row < $this->gridRows; $row++) {
            for ($col = 0; $col < $this->gridColumns; $col++) {
                $containerId = "container_{$row}_{$col}";
                $this->containers[$containerId] = [
                    'id' => $containerId,
                    'row' => $row,
                    'col' => $col,
                    'title' => "Container {$row}-{$col}",
                    'fields' => [],
                    'style' => [
                        'background' => 'bg-gray-50',
                        'border' => 'border-gray-300',
                        'padding' => 'p-4'
                    ]
                ];
            }
        }
    }
    
    /**
     * Load available fields based on selected model
     */
    public function loadAvailableFields()
    {
        switch ($this->selectedModel) {
            case 'Sager':
                $this->availableFields = $this->getSagerFields();
                break;
            case 'Kreditorer':
                $this->availableFields = $this->getKreditorFields();
                break;
            case 'Debitorer':
                $this->availableFields = $this->getDebitorFields();
                break;
            case 'Sagsbehandler':
                $this->availableFields = $this->getSagsbehandlerFields();
                break;
            case 'Konsulenter':
                $this->availableFields = $this->getKonsulentFields();
                break;
        }
    }
    
    /**
     * Get Sager model fields
     */
    protected function getSagerFields()
    {
        return [
            // Basic fields
            'sagsnr' => ['label' => 'Sagsnummer', 'type' => 'text', 'category' => 'basic'],
            'hovedstol' => ['label' => 'Hovedstol', 'type' => 'currency', 'category' => 'financial'],
            'renter' => ['label' => 'Renter', 'type' => 'currency', 'category' => 'financial'],
            'gebyr' => ['label' => 'Gebyr', 'type' => 'currency', 'category' => 'financial'],
            'ialt' => ['label' => 'I alt', 'type' => 'currency', 'category' => 'financial', 'readonly' => true],
            'startgebyr' => ['label' => 'Startgebyr', 'type' => 'currency', 'category' => 'financial'],
            'restgaeld' => ['label' => 'Restgæld', 'type' => 'currency', 'category' => 'financial', 'readonly' => true],
            'restgaeld_dkg' => ['label' => 'Restgæld DKG', 'type' => 'currency', 'category' => 'financial'],
            'restgaeld_kreditor' => ['label' => 'Restgæld Kreditor', 'type' => 'currency', 'category' => 'financial'],
            'indbetalt' => ['label' => 'Indbetalt', 'type' => 'currency', 'category' => 'financial'],
            'mdlydelse' => ['label' => 'Månedlig ydelse', 'type' => 'currency', 'category' => 'financial'],
            'n_mdlydelse' => ['label' => 'Ny månedlig ydelse', 'type' => 'currency', 'category' => 'financial'],
            'afdragsordning' => ['label' => 'Afdragsordning', 'type' => 'text', 'category' => 'basic'],
            'stelnr' => ['label' => 'Stelnummer', 'type' => 'text', 'category' => 'asset'],
            'aktiv' => ['label' => 'Aktiv', 'type' => 'text', 'category' => 'asset'],
            'fakturanr' => ['label' => 'Fakturanummer', 'type' => 'text', 'category' => 'billing'],
            
            // Date fields
            'afsluttet' => ['label' => 'Afsluttet', 'type' => 'date', 'category' => 'dates'],
            'faktureret' => ['label' => 'Faktureret', 'type' => 'date', 'category' => 'dates'],
            'betalt' => ['label' => 'Betalt', 'type' => 'date', 'category' => 'dates'],
            'fakturadato' => ['label' => 'Fakturadato', 'type' => 'date', 'category' => 'dates'],
            'modtaget' => ['label' => 'Modtaget', 'type' => 'date', 'category' => 'dates'],
            'senesterapport' => ['label' => 'Seneste rapport', 'type' => 'date', 'category' => 'dates'],
            'opgivet' => ['label' => 'Opgivet', 'type' => 'date', 'category' => 'dates'],
            
            // Relation fields
            'kreditor' => ['label' => 'Kreditor', 'type' => 'select', 'category' => 'relations', 'relation' => 'sagerkreditor'],
            'debitor' => ['label' => 'Debitor', 'type' => 'select', 'category' => 'relations', 'relation' => 'sagerdebitor'],
            'sagsbehandler' => ['label' => 'Sagsbehandler', 'type' => 'select', 'category' => 'relations', 'relation' => 'sagersagsbehandler'],
            'konsulent' => ['label' => 'Konsulent', 'type' => 'select', 'category' => 'relations', 'relation' => 'sagerkonsulent'],
            'status' => ['label' => 'Status', 'type' => 'select', 'category' => 'relations', 'options' => Status::class],
            'bemaerkning' => ['label' => 'Bemærkning', 'type' => 'select', 'category' => 'relations', 'options' => Bemaerkning::class],
            'afslutning' => ['label' => 'Afslutning', 'type' => 'select', 'category' => 'relations', 'options' => Afslutning::class],
            'ktr' => ['label' => 'KTR', 'type' => 'select', 'category' => 'relations', 'options' => Ktr::class],
        ];
    }
    
    /**
     * Get Kreditor fields
     */
    protected function getKreditorFields()
    {
        return [
            'navn' => ['label' => 'Navn', 'type' => 'text', 'category' => 'basic'],
            'lotusID' => ['label' => 'Lotus ID', 'type' => 'text', 'category' => 'basic'],
            'adresse' => ['label' => 'Adresse', 'type' => 'text', 'category' => 'contact'],
            'postnr' => ['label' => 'Postnummer', 'type' => 'text', 'category' => 'contact'],
            'by' => ['label' => 'By', 'type' => 'text', 'category' => 'contact'],
            'email' => ['label' => 'Email', 'type' => 'email', 'category' => 'contact'],
            'tlf' => ['label' => 'Telefon', 'type' => 'tel', 'category' => 'contact'],
        ];
    }
    
    /**
     * Get Debitor fields
     */
    protected function getDebitorFields()
    {
        return [
            'navn' => ['label' => 'Navn', 'type' => 'text', 'category' => 'basic'],
            'co' => ['label' => 'C/O', 'type' => 'text', 'category' => 'basic'],
            'adresse' => ['label' => 'Adresse', 'type' => 'text', 'category' => 'contact'],
            'postnr' => ['label' => 'Postnummer', 'type' => 'text', 'category' => 'contact'],
            'by' => ['label' => 'By', 'type' => 'text', 'category' => 'contact'],
            'email' => ['label' => 'Email', 'type' => 'email', 'category' => 'contact'],
            'tlf' => ['label' => 'Telefon', 'type' => 'tel', 'category' => 'contact'],
            'mobil' => ['label' => 'Mobil', 'type' => 'tel', 'category' => 'contact'],
            'pnr' => ['label' => 'Personnummer', 'type' => 'text', 'category' => 'basic'],
            'adropl' => ['label' => 'Adresse oplysning', 'type' => 'text', 'category' => 'contact'],
        ];
    }
    
    /**
     * Get Sagsbehandler fields
     */
    protected function getSagsbehandlerFields()
    {
        return [
            'navn' => ['label' => 'Navn', 'type' => 'text', 'category' => 'basic'],
            'email' => ['label' => 'Email', 'type' => 'email', 'category' => 'contact'],
            'tlf' => ['label' => 'Telefon', 'type' => 'tel', 'category' => 'contact'],
            'mobil' => ['label' => 'Mobil', 'type' => 'tel', 'category' => 'contact'],
        ];
    }
    
    /**
     * Get Konsulent fields
     */
    protected function getKonsulentFields()
    {
        return [
            'navn' => ['label' => 'Navn', 'type' => 'text', 'category' => 'basic'],
            'email' => ['label' => 'Email', 'type' => 'email', 'category' => 'contact'],
            'tlf' => ['label' => 'Telefon', 'type' => 'tel', 'category' => 'contact'],
            'mobil' => ['label' => 'Mobil', 'type' => 'tel', 'category' => 'contact'],
        ];
    }
    
    /**
     * Update grid dimensions
     */
    public function updateGridDimensions()
    {
        $this->initializeGrid();
        $this->forceRefresh();
    }
    
    /**
     * Update selected model and reload fields
     */
    public function updatedSelectedModel()
    {
        $this->loadAvailableFields();
        $this->forceRefresh();
    }
    
    /**
     * Force refresh by incrementing counter
     */
    public function forceRefresh()
    {
        $this->refreshCounter++;
    }
    
    /**
     * Add field to container - STABLE VERSION
     */
    public function addFieldToContainer($fieldKey, $containerId)
    {
        try {
            // Debug logging
            logger("Adding field to container", [
                'fieldKey' => $fieldKey,
                'containerId' => $containerId,
                'availableFields' => array_keys($this->availableFields),
                'containerExists' => isset($this->containers[$containerId]),
                'fieldExists' => isset($this->availableFields[$fieldKey])
            ]);
            
            // Validate container exists
            if (!isset($this->containers[$containerId])) {
                $this->dispatch('toast', 
                    message: "Container not found: {$containerId}", 
                    type: 'error'
                );
                return false;
            }
            
            // Validate field exists
            if (!isset($this->availableFields[$fieldKey])) {
                $this->dispatch('toast', 
                    message: "Field not found: {$fieldKey}. Available fields: " . implode(', ', array_keys($this->availableFields)), 
                    type: 'error'
                );
                return false;
            }
            
            // Get field data
            $field = $this->availableFields[$fieldKey];
            $field['key'] = $fieldKey;
            $field['id'] = uniqid('field_');
            
            // Check if field already exists in container
            foreach ($this->containers[$containerId]['fields'] as $existingField) {
                if ($existingField['key'] === $fieldKey) {
                    $this->dispatch('toast', 
                        message: "Field '{$field['label']}' already exists in this container", 
                        type: 'warning'
                    );
                    return false;
                }
            }
            
            // Add field to container
            $this->containers[$containerId]['fields'][] = $field;
            
            // Force refresh using counter
            $this->forceRefresh();
            
            // Log success
            logger("Field added successfully", [
                'fieldKey' => $fieldKey,
                'containerId' => $containerId,
                'containerFieldCount' => count($this->containers[$containerId]['fields'])
            ]);
            
            $this->dispatch('toast', 
                message: "Field '{$field['label']}' added to container", 
                type: 'success'
            );
            
            // Dispatch simple success event
            $this->dispatch('field-added-success');
            
            return true;
            
        } catch (\Exception $e) {
            logger("Error adding field", [
                'error' => $e->getMessage(),
                'fieldKey' => $fieldKey,
                'containerId' => $containerId
            ]);
            
            $this->dispatch('toast', 
                message: 'Error adding field: ' . $e->getMessage(), 
                type: 'error'
            );
            
            return false;
        }
    }
    
    /**
     * Remove field from container
     */
    public function removeFieldFromContainer($containerId, $fieldIndex)
    {
        if (isset($this->containers[$containerId]['fields'][$fieldIndex])) {
            $field = $this->containers[$containerId]['fields'][$fieldIndex];
            unset($this->containers[$containerId]['fields'][$fieldIndex]);
            $this->containers[$containerId]['fields'] = array_values($this->containers[$containerId]['fields']);
            
            // Force refresh
            $this->forceRefresh();
            
            $this->dispatch('toast', 
                message: "Field '{$field['label']}' removed", 
                type: 'info'
            );
        }
    }
    
    /**
     * Clear all fields from container
     */
    public function clearContainer($containerId)
    {
        if (isset($this->containers[$containerId])) {
            $fieldCount = count($this->containers[$containerId]['fields']);
            $this->containers[$containerId]['fields'] = [];
            
            // Force refresh
            $this->forceRefresh();
            
            $this->dispatch('toast', 
                message: "Cleared {$fieldCount} fields from container", 
                type: 'info'
            );
        }
    }
    
    /**
     * Move container to new position
     */
    public function moveContainer($fromContainerId, $toRow, $toCol)
    {
        $toContainerId = "container_{$toRow}_{$toCol}";
        
        if (!isset($this->containers[$fromContainerId]) || !isset($this->containers[$toContainerId])) {
            return;
        }
        
        // Swap containers
        $tempContainer = $this->containers[$fromContainerId];
        $this->containers[$fromContainerId] = $this->containers[$toContainerId];
        $this->containers[$toContainerId] = $tempContainer;
        
        // Update container positions
        $this->containers[$fromContainerId]['row'] = $tempContainer['row'];
        $this->containers[$fromContainerId]['col'] = $tempContainer['col'];
        $this->containers[$toContainerId]['row'] = $toRow;
        $this->containers[$toContainerId]['col'] = $toCol;
        
        $this->forceRefresh();
        
        $this->dispatch('toast', 
            message: 'Container moved successfully', 
            type: 'success'
        );
    }
    
    /**
     * Update container title
     */
    public function updateContainerTitle($containerId, $title)
    {
        if (isset($this->containers[$containerId])) {
            $this->containers[$containerId]['title'] = $title;
        }
    }
    
    /**
     * Update container style
     */
    public function updateContainerStyle($containerId, $styleProperty, $value)
    {
        if (isset($this->containers[$containerId])) {
            $this->containers[$containerId]['style'][$styleProperty] = $value;
        }
    }
    
    /**
     * Toggle preview mode
     */
    public function togglePreview()
    {
        $this->previewMode = !$this->previewMode;
        $this->forceRefresh();
    }
    
    /**
     * Debug method to check container state
     */
    public function debugContainer($containerId)
    {
        $container = $this->containers[$containerId] ?? null;
        
        logger("Debug container", [
            'containerId' => $containerId,
            'container' => $container,
            'fieldCount' => $container ? count($container['fields']) : 0
        ]);
        
        $this->dispatch('toast', 
            message: "Container {$containerId} has " . ($container ? count($container['fields']) : 0) . " fields", 
            type: 'info'
        );
    }
    
    /**
     * Manual refresh method
     */
    public function refreshComponent()
    {
        $this->forceRefresh();
        $this->dispatch('toast', 
            message: 'Component refreshed', 
            type: 'info'
        );
    }
    
    /**
     * Save layout to database
     */
    public function saveLayout()
    {
        $this->validate([
            'layoutName' => 'required|string|max:255',
            'selectedModel' => 'required|string',
        ]);
        
        $layoutData = [
            'name' => $this->layoutName,
            'description' => $this->layoutDescription,
            'model' => $this->selectedModel,
            'grid_rows' => $this->gridRows,
            'grid_columns' => $this->gridColumns,
            'containers' => $this->containers,
            'created_by' => auth()->id(),
        ];
        
        try {
            FormLayout::create([
                'name' => $this->layoutName,
                'description' => $this->layoutDescription,
                'model_type' => $this->selectedModel,
                'layout_data' => json_encode($layoutData),
                'is_active' => true,
                'created_by' => auth()->id() ?? 1, // Fallback for testing
            ]);
            
            $this->dispatch('toast', 
                message: "Layout '{$this->layoutName}' saved successfully!", 
                type: 'success'
            );
            
            // Reset form
            $this->layoutName = '';
            $this->layoutDescription = '';
            
        } catch (\Exception $e) {
            $this->dispatch('toast', 
                message: 'Error saving layout: ' . $e->getMessage(), 
                type: 'error'
            );
        }
    }
    
    /**
     * Load existing layout
     */
    public function loadLayout($layoutId)
    {
        try {
            $layout = FormLayout::findOrFail($layoutId);
            $layoutData = json_decode($layout->layout_data, true);
            
            $this->layoutName = $layout->name;
            $this->layoutDescription = $layout->description;
            $this->selectedModel = $layout->model_type;
            $this->gridRows = $layoutData['grid_rows'];
            $this->gridColumns = $layoutData['grid_columns'];
            $this->containers = $layoutData['containers'];
            
            $this->loadAvailableFields();
            $this->forceRefresh();
            
            $this->dispatch('toast', 
                message: "Layout '{$layout->name}' loaded successfully!", 
                type: 'success'
            );
        } catch (\Exception $e) {
            $this->dispatch('toast', 
                message: 'Error loading layout: ' . $e->getMessage(), 
                type: 'error'
            );
        }
    }
    
    /**
     * Export layout as JSON
     */
    public function exportLayout()
    {
        $layoutData = [
            'name' => $this->layoutName,
            'description' => $this->layoutDescription,
            'model' => $this->selectedModel,
            'grid_rows' => $this->gridRows,
            'grid_columns' => $this->gridColumns,
            'containers' => $this->containers,
            'exported_at' => now()->toISOString(),
        ];
        
        $this->dispatch('download-json', [
            'filename' => 'form-layout-' . \Illuminate\Support\Str::slug($this->layoutName) . '.json',
            'data' => $layoutData
        ]);
    }
    
    /**
     * Generate Livewire component code
     */
    public function generateComponent()
    {
        $componentCode = $this->generateLivewireComponent();
        $viewCode = $this->generateBladeView();
        
        $this->dispatch('show-generated-code', [
            'component' => $componentCode,
            'view' => $viewCode
        ]);
    }
    
    /**
     * Generate Livewire component PHP code
     */
    protected function generateLivewireComponent()
    {
        $modelClass = "App\\Models\\{$this->selectedModel}";
        $componentName = "Generated" . $this->selectedModel . "Form";
        
        $code = "<?php\n\n";
        $code .= "namespace App\\Livewire\\Generated;\n\n";
        $code .= "use Livewire\\Component;\n";
        $code .= "use {$modelClass};\n";
        $code .= "use App\\Livewire\\Forms\\SagForm;\n\n";
        $code .= "class {$componentName} extends Component\n";
        $code .= "{\n";
        $code .= "    public SagForm \$form;\n";
        $code .= "    public ?{$this->selectedModel} \$record = null;\n";
        $code .= "    public bool \$isEditMode = false;\n\n";
        
        // Add mount method
        $code .= "    public function mount(?{$this->selectedModel} \$record = null)\n";
        $code .= "    {\n";
        $code .= "        \$this->record = \$record;\n";
        $code .= "        \$this->isEditMode = \$record !== null;\n";
        $code .= "        \n";
        $code .= "        if (\$this->record) {\n";
        $code .= "            \$this->fillFormFromRecord();\n";
        $code .= "        }\n";
        $code .= "    }\n\n";
        
        // Add save method
        $code .= "    public function save()\n";
        $code .= "    {\n";
        $code .= "        \$validated = \$this->form->safeValidate();\n";
        $code .= "        \n";
        $code .= "        if (\$this->record) {\n";
        $code .= "            \$this->record->update(\$validated);\n";
        $code .= "        } else {\n";
        $code .= "            \$this->record = {$this->selectedModel}::create(\$validated);\n";
        $code .= "            \$this->isEditMode = true;\n";
        $code .= "        }\n";
        $code .= "        \n";
        $code .= "        \$this->dispatch('toast', message: 'Record saved successfully!', type: 'success');\n";
        $code .= "    }\n\n";
        
        // Add fillFormFromRecord method
        $code .= "    protected function fillFormFromRecord()\n";
        $code .= "    {\n";
        foreach ($this->containers as $container) {
            foreach ($container['fields'] as $field) {
                $code .= "        \$this->form->{$field['key']} = \$this->record->{$field['key']} ?? '';\n";
            }
        }
        $code .= "    }\n\n";
        
        $code .= "    public function render()\n";
        $code .= "    {\n";
        $code .= "        return view('liveWire.generated.dynamic-form-renderer');\n";
        $code .= "    }\n";
        $code .= "}\n";
        
        return $code;
    }
    
    /**
     * Generate Blade view code
     */
    protected function generateBladeView()
    {
        $code = "<div class=\"max-w-7xl mx-auto p-6\">\n";
        $code .= "    <div class=\"bg-white rounded-lg shadow-lg p-6\">\n";
        $code .= "        <h1 class=\"text-2xl font-bold mb-6\">\n";
        $code .= "            {{ \$isEditMode ? 'Edit' : 'Create' }} {$this->selectedModel}\n";
        $code .= "        </h1>\n\n";
        
        // Generate grid
        $code .= "        <div class=\"grid grid-cols-{$this->gridColumns} gap-6\">\n";
        
        for ($row = 0; $row < $this->gridRows; $row++) {
            for ($col = 0; $col < $this->gridColumns; $col++) {
                $containerId = "container_{$row}_{$col}";
                $container = $this->containers[$containerId];
                
                if (!empty($container['fields'])) {
                    $code .= "            <!-- {$container['title']} -->\n";
                    $code .= "            <div class=\"{$container['style']['background']} {$container['style']['padding']} border {$container['style']['border']} rounded-lg\">\n";
                    $code .= "                <h3 class=\"text-lg font-semibold mb-4\">{$container['title']}</h3>\n";
                    
                    foreach ($container['fields'] as $field) {
                        $code .= $this->generateFieldCode($field);
                    }
                    
                    $code .= "            </div>\n";
                }
            }
        }
        
        $code .= "        </div>\n\n";
        
        // Add save button
        $code .= "        <div class=\"mt-6 flex justify-end\">\n";
        $code .= "            <button wire:click=\"save\" \n";
        $code .= "                    wire:loading.attr=\"disabled\"\n";
        $code .= "                    class=\"bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg\">\n";
        $code .= "                <span wire:loading.remove>Save</span>\n";
        $code .= "                <span wire:loading>Saving...</span>\n";
        $code .= "            </button>\n";
        $code .= "        </div>\n";
        $code .= "    </div>\n";
        $code .= "</div>\n";
        
        return $code;
    }
    
    /**
     * Generate field HTML code
     */
    protected function generateFieldCode($field)
    {
        $code = "                <div class=\"mb-4\">\n";
        $code .= "                    <label class=\"block text-sm font-medium text-gray-700 mb-1\">{$field['label']}</label>\n";
        
        switch ($field['type']) {
            case 'text':
            case 'email':
            case 'tel':
                $code .= "                    <input type=\"{$field['type']}\" \n";
                $code .= "                           wire:model.live=\"form.{$field['key']}\"\n";
                if (isset($field['readonly']) && $field['readonly']) {
                    $code .= "                           readonly\n";
                    $code .= "                           class=\"w-full border border-gray-300 rounded px-3 py-2 bg-gray-100 cursor-not-allowed\">\n";
                } else {
                    $code .= "                           class=\"w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500\">\n";
                }
                break;
                
            case 'date':
                $code .= "                    <input type=\"date\" \n";
                $code .= "                           wire:model.live=\"form.{$field['key']}\"\n";
                $code .= "                           class=\"w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500\">\n";
                break;
                
            case 'currency':
                $code .= "                    <input type=\"text\" \n";
                $code .= "                           wire:model.blur=\"form.{$field['key']}\"\n";
                if (isset($field['readonly']) && $field['readonly']) {
                    $code .= "                           readonly\n";
                    $code .= "                           class=\"w-full border border-gray-300 rounded px-3 py-2 text-right bg-gray-100 cursor-not-allowed\">\n";
                } else {
                    $code .= "                           class=\"w-full border border-gray-300 rounded px-3 py-2 text-right focus:ring-2 focus:ring-blue-500\">\n";
                }
                break;
                
            case 'select':
                $code .= "                    <select wire:model=\"form.{$field['key']}\" \n";
                $code .= "                            class=\"w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500\">\n";
                $code .= "                        <option value=\"\">Select {$field['label']}...</option>\n";
                $code .= "                        <!-- Add options here -->\n";
                $code .= "                    </select>\n";
                break;
        }
        
        $code .= "                </div>\n";
        
        return $code;
    }
    
    public function render()
    {
        return view('liveWire.admin.form-builder');
    }
}