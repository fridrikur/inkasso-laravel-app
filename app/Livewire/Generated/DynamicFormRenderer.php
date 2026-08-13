<?php

namespace App\Livewire\Generated;

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
use App\Livewire\Forms\SagForm;
use Illuminate\Support\Str;

class DynamicFormRenderer extends Component
{
    public SagForm $form;
    public ?object $record = null;
    public bool $isEditMode = false;
    public FormLayout $layout;
    public array $selectOptions = [];
    
    // Livewire 3 listeners for dynamic functionality
    protected $listeners = [
        'kreditor-changed' => 'onKreditorChanged'
    ];

    public function mount(FormLayout $layout, $recordId = null)
    {
        $this->layout = $layout;
        
        // Load record if editing
        if ($recordId) {
            $modelClass = "App\\Models\\{$layout->model_type}";
            if (class_exists($modelClass)) {
                $this->record = $modelClass::findOrFail($recordId);
                $this->isEditMode = true;
            }
        }
        
        // Initialize form
        if (isset($this->form)) {
            $this->form->sag = $this->record;
            $this->form->sag_id = $this->record?->id;
        }
        
        // Load dropdown options
        $this->loadSelectOptions();
        
        // Fill form if editing
        if ($this->record) {
            $this->fillFormFromRecord();
        }
    }

    /**
     * Load all dropdown options based on layout fields
     */
    protected function loadSelectOptions(): void
    {
        $this->selectOptions = [];
        
        // Only load options if models exist
        if (class_exists(Status::class)) {
            $this->selectOptions['status'] = Status::pluck('tekst', 'id')->toArray();
        }
        
        if (class_exists(Bemaerkning::class)) {
            $this->selectOptions['bemaerkning'] = Bemaerkning::pluck('tekst', 'id')->toArray();
        }
        
        if (class_exists(Ktr::class)) {
            $this->selectOptions['ktr'] = Ktr::pluck('tekst', 'id')->toArray();
        }
        
        if (class_exists(Afslutning::class)) {
            $this->selectOptions['afslutning'] = Afslutning::pluck('tekst', 'id')->toArray();
        }
        
        if (class_exists(Konsulenter::class)) {
            $this->selectOptions['konsulent'] = Konsulenter::pluck('navn', 'id')->toArray();
        }
        
        if (class_exists(Kreditorer::class)) {
            $this->selectOptions['kreditor'] = Kreditorer::pluck('navn', 'id')->toArray();
        }
        
        if (class_exists(Debitorer::class)) {
            $this->selectOptions['debitor'] = Debitorer::pluck('navn', 'id')->toArray();
        }
        
        // Sagsbehandler will be loaded dynamically
        $this->selectOptions['sagsbehandler'] = [];
    }

    /**
     * Fill form from existing record
     */
    protected function fillFormFromRecord(): void
    {
        if (!$this->record || !isset($this->form)) return;

        $allFields = $this->layout->getAllFields();
        
        foreach ($allFields as $field) {
            $fieldKey = $field['key'];
            
            // Skip if property doesn't exist on form
            if (!property_exists($this->form, $fieldKey)) {
                continue;
            }
            
            // Handle different field types
            switch ($field['category'] ?? 'basic') {
                case 'relations':
                    $this->fillRelationField($fieldKey, $field);
                    break;
                    
                case 'financial':
                    $value = $this->record->{$fieldKey} ?? 0;
                    $this->form->{$fieldKey} = $this->formatCurrency($value);
                    break;
                    
                case 'dates':
                    $value = $this->record->{$fieldKey};
                    $this->form->{$fieldKey} = $value ? $value->format('Y-m-d') : '';
                    break;
                    
                default:
                    $this->form->{$fieldKey} = $this->record->{$fieldKey} ?? '';
            }
        }
        
        // Handle special cases for Sager model
        if ($this->layout->model_type === 'Sager') {
            $this->fillSagerRelations();
        }
    }

    /**
     * Fill relation fields
     */
    protected function fillRelationField($fieldKey, $field): void
    {
        if (!isset($field['relation']) || !$this->record) return;
        
        $relationMethod = $field['relation'];
        
        if (method_exists($this->record, $relationMethod)) {
            $relatedRecord = $this->record->{$relationMethod}()->first();
            if (property_exists($this->form, $fieldKey)) {
                $this->form->{$fieldKey} = $relatedRecord?->id ?? '';
            }
        }
    }

    /**
     * Fill Sager specific relations
     */
    protected function fillSagerRelations(): void
    {
        if (!$this->record || !isset($this->form)) return;

        // Fill kreditor data
        if (method_exists($this->record, 'sagerkreditor') && $this->record->sagerkreditor->isNotEmpty()) {
            $kreditor = $this->record->sagerkreditor->first();
            
            if (property_exists($this->form, 'kreditor')) {
                $this->form->kreditor = $kreditor->id;
            }
            if (property_exists($this->form, 'kreditor_navn')) {
                $this->form->kreditor_navn = $kreditor->navn;
            }
            if (property_exists($this->form, 'kreditor_lotusID')) {
                $this->form->kreditor_lotusID = $kreditor->lotusID;
            }
            
            // Load sagsbehandlere for this kreditor
            if (class_exists(Sagsbehandler::class) && method_exists(Sagsbehandler::class, 'forKreditor')) {
                $this->selectOptions['sagsbehandler'] = Sagsbehandler::forKreditor($kreditor->id);
            }
        }

        // Fill debitor data using SagForm method
        if (method_exists($this->form, 'SetDebitor')) {
            $this->form->SetDebitor($this->record);
        }
    }

    /**
     * Livewire 3 property updater for kreditor lookup
     */
    public function updatedFormKreditorLotusId($value)
    {
        if (!empty($value)) {
            $this->dispatch('kreditor-changed', kreditorLotusID: $value);
        } else {
            if (property_exists($this->form, 'kreditor_navn')) {
                $this->form->kreditor_navn = '';
            }
            if (property_exists($this->form, 'kreditor')) {
                $this->form->kreditor = '';
            }
            $this->selectOptions['sagsbehandler'] = [];
            if (property_exists($this->form, 'sagsbehandler')) {
                $this->form->sagsbehandler = '';
            }
        }
    }

    /**
     * Handle kreditor changed event
     */
    public function onKreditorChanged($payload)
    {
        $kreditorLotusID = $payload['kreditorLotusID'] ?? null;

        if (empty($kreditorLotusID)) {
            $this->selectOptions['sagsbehandler'] = [];
            if (property_exists($this->form, 'sagsbehandler')) {
                $this->form->sagsbehandler = '';
            }
            return;
        }

        try {
            if (!class_exists(Kreditorer::class)) {
                throw new \Exception('Kreditorer model not found');
            }
            
            $kreditor = Kreditorer::where('lotusID', $kreditorLotusID)->first();
            
            if ($kreditor) {
                if (property_exists($this->form, 'kreditor_navn')) {
                    $this->form->kreditor_navn = $kreditor->navn;
                }
                if (property_exists($this->form, 'kreditor')) {
                    $this->form->kreditor = $kreditor->id;
                }
                
                if (class_exists(Sagsbehandler::class) && method_exists(Sagsbehandler::class, 'forKreditor')) {
                    $this->selectOptions['sagsbehandler'] = Sagsbehandler::forKreditor($kreditor->id);
                }
                if (property_exists($this->form, 'sagsbehandler')) {
                    $this->form->sagsbehandler = '';
                }
                
                $this->dispatch('toast', 
                    message: "Kreditor fundet: {$kreditor->navn}", 
                    type: 'success'
                );
            } else {
                if (property_exists($this->form, 'kreditor_navn')) {
                    $this->form->kreditor_navn = '';
                }
                if (property_exists($this->form, 'kreditor')) {
                    $this->form->kreditor = '';
                }
                $this->selectOptions['sagsbehandler'] = [];
                if (property_exists($this->form, 'sagsbehandler')) {
                    $this->form->sagsbehandler = '';
                }
                
                $this->dispatch('toast', 
                    message: "Kreditor ikke fundet med LotusID: {$kreditorLotusID}", 
                    type: 'error'
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', 
                message: 'Fejl ved opslag af kreditor: ' . $e->getMessage(), 
                type: 'error'
            );
        }
    }

    /**
     * Auto-calculate financial totals
     */
    public function updatedFormHovedstol()
    {
        $this->calculateTotals();
    }

    public function updatedFormRenter()
    {
        $this->calculateTotals();
    }

    public function updatedFormGebyr()
    {
        $this->calculateTotals();
    }

    public function updatedFormIndbetalt()
    {
        $this->calculateTotals();
    }

    /**
     * Calculate financial totals
     */
    protected function calculateTotals(): void
    {
        if (!isset($this->form)) return;
        
        $hovedstol = $this->parseCurrency($this->form->hovedstol ?? '');
        $renter = $this->parseCurrency($this->form->renter ?? '');
        $gebyr = $this->parseCurrency($this->form->gebyr ?? '');
        $indbetalt = $this->parseCurrency($this->form->indbetalt ?? '');
        
        $ialt = $hovedstol + $renter + $gebyr;
        $restgaeld = $ialt - $indbetalt;
        $restgaeld_dkg = $restgaeld + $gebyr;
        
        if (property_exists($this->form, 'ialt')) {
            $this->form->ialt = $this->formatCurrency($ialt);
        }
        if (property_exists($this->form, 'restgaeld')) {
            $this->form->restgaeld = $this->formatCurrency($restgaeld);
        }
        if (property_exists($this->form, 'restgaeld_dkg')) {
            $this->form->restgaeld_dkg = $this->formatCurrency($restgaeld_dkg);
        }
    }

    /**
     * Parse currency from string
     */
    protected function parseCurrency($value): float
    {
        if (empty($value)) return 0;
        
        // Handle Danish currency format
        $normalized = str_replace(['kr.', ' '], '', $value);
        $normalized = str_replace('.', '', $normalized); // Remove thousands separator
        $normalized = str_replace(',', '.', $normalized); // Replace decimal comma with dot
        
        return (float) $normalized;
    }

    /**
     * Format number to Danish currency format
     */
    protected function formatCurrency($value): string
    {
        if (empty($value)) return 'kr. 0,00';
        
        return 'kr. ' . number_format((float) $value, 2, ',', '.');
    }

    /**
     * Save the record
     */
    public function save()
    {
        try {
            if (!isset($this->form)) {
                throw new \Exception('Form not initialized');
            }
            
            // Validate form
            $validated = [];
            if (method_exists($this->form, 'safeValidate')) {
                $validated = $this->form->safeValidate();
            } else {
                // Fallback validation
                $validated = $this->validate($this->getRules());
            }
            
            $modelClass = "App\\Models\\{$this->layout->model_type}";
            
            if (!class_exists($modelClass)) {
                throw new \Exception("Model {$modelClass} not found");
            }
            
            // Prepare data for saving
            $saveData = $this->prepareSaveData($validated);
            
            if ($this->record) {
                // Update existing record
                $this->record->update($saveData);
            } else {
                // Create new record
                $this->record = $modelClass::create($saveData);
                if (isset($this->form)) {
                    $this->form->sag = $this->record;
                    $this->form->sag_id = $this->record->id;
                }
                $this->isEditMode = true;
            }

            // Save relations if Sager model
            if ($this->layout->model_type === 'Sager') {
                $this->saveRelations();
                $this->saveRelatedModels();
            }

            $this->dispatch('toast', 
                message: 'Record saved successfully!', 
                type: 'success'
            );

        } catch (\Exception $e) {
            $this->dispatch('toast', 
                message: 'Error saving: ' . $e->getMessage(), 
                type: 'error'
            );
        }
    }

    /**
     * Get validation rules
     */
    public function getRules(): array
    {
        $rules = [];
        $allFields = $this->layout->getAllFields();
        
        foreach ($allFields as $field) {
            $fieldKey = $field['key'];
            $fieldRules = [];
            
            // Add basic rules based on field type
            switch ($field['type']) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'currency':
                    $fieldRules[] = 'string';
                    break;
            }
            
            if (!empty($fieldRules)) {
                $rules["form.{$fieldKey}"] = implode('|', $fieldRules);
            }
        }
        
        return $rules;
    }

    /**
     * Prepare data for saving based on field types
     */
    protected function prepareSaveData($validated): array
    {
        $saveData = [];
        $allFields = $this->layout->getAllFields();
        
        foreach ($allFields as $field) {
            $fieldKey = $field['key'];
            $value = $validated["form.{$fieldKey}"] ?? $validated[$fieldKey] ?? null;
            
            if ($value === null || $value === '') {
                continue;
            }
            
            switch ($field['type']) {
                case 'currency':
                    $saveData[$fieldKey] = $this->parseCurrency($value);
                    break;
                    
                case 'date':
                    $saveData[$fieldKey] = $value;
                    break;
                    
                default:
                    $saveData[$fieldKey] = $value;
            }
        }
        
        return $saveData;
    }

    /**
     * Save relation fields (for Sager model)
     */
    protected function saveRelations(): void
    {
        if (!$this->record || $this->layout->model_type !== 'Sager' || !isset($this->form)) return;

        // Only sync if methods exist and form has the property
        if (method_exists($this->record, 'sagerStatus') && property_exists($this->form, 'status') && $this->form->status) {
            $this->record->sagerStatus()->sync([$this->form->status]);
        }

        if (method_exists($this->record, 'sagerbemaerkning') && property_exists($this->form, 'bemaerkning') && $this->form->bemaerkning) {
            $this->record->sagerbemaerkning()->sync([$this->form->bemaerkning]);
        }

        if (method_exists($this->record, 'sagerKTR') && property_exists($this->form, 'ktr') && $this->form->ktr) {
            $this->record->sagerKTR()->sync([$this->form->ktr]);
        }

        if (method_exists($this->record, 'sagerafslutning') && property_exists($this->form, 'afslutning') && $this->form->afslutning) {
            $this->record->sagerafslutning()->sync([$this->form->afslutning]);
        }

        if (method_exists($this->record, 'sagerkonsulent') && property_exists($this->form, 'konsulent') && $this->form->konsulent) {
            $this->record->sagerkonsulent()->sync([$this->form->konsulent]);
        }

        if (method_exists($this->record, 'sagersagsbehandler') && property_exists($this->form, 'sagsbehandler') && $this->form->sagsbehandler) {
            $this->record->sagersagsbehandler()->sync([$this->form->sagsbehandler]);
        }
    }

    /**
     * Save related models (for Sager model)
     */
    protected function saveRelatedModels(): void
    {
        if (!$this->record || $this->layout->model_type !== 'Sager' || !isset($this->form)) return;

        // Save debitor
        if (method_exists($this->form, 'getDebitorDataForSave')) {
            try {
                $debitorData = $this->form->getDebitorDataForSave();
                if (!empty(array_filter($debitorData)) && class_exists(Debitorer::class)) {
                    $debitor = Debitorer::updateOrCreate(
                        ['navn' => $debitorData['navn']],
                        $debitorData
                    );
                    if (method_exists($this->record, 'sagerdebitor')) {
                        $this->record->sagerdebitor()->sync([$debitor->id]);
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail the save
                logger('Error saving debitor: ' . $e->getMessage());
            }
        }

        // Save kreditor
        if (method_exists($this->form, 'getKreditorData')) {
            try {
                $kreditorData = $this->form->getKreditorData();
                if (!empty(array_filter($kreditorData)) && class_exists(Kreditorer::class)) {
                    $kreditor = Kreditorer::updateOrCreate(
                        ['lotusID' => $kreditorData['lotusID']],
                        $kreditorData
                    );
                    if (method_exists($this->record, 'sagerkreditor')) {
                        $this->record->sagerkreditor()->sync([$kreditor->id]);
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail the save
                logger('Error saving kreditor: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get select options for a field
     */
    public function getSelectOptionsForField($fieldKey): array
    {
        return $this->selectOptions[$fieldKey] ?? [];
    }

    /**
     * Check if field should be readonly
     */
    public function isFieldReadonly($field): bool
    {
        return isset($field['readonly']) && $field['readonly'];
    }

    /**
     * Get wire model path for field
     */
    public function getWireModelForField($field): string
    {
        $wireModel = "form.{$field['key']}";
        
        // Use appropriate wire model modifier based on field type
        switch ($field['type']) {
            case 'currency':
                return "wire:model.blur=\"{$wireModel}\"";
            case 'date':
            case 'text':
            case 'email':
            case 'tel':
                return "wire:model.live=\"{$wireModel}\"";
            case 'select':
                return "wire:model=\"{$wireModel}\"";
            default:
                return "wire:model.live=\"{$wireModel}\"";
        }
    }

    public function render()
    {
        return view('livewire.generated.dynamic-form-renderer');
    }
}