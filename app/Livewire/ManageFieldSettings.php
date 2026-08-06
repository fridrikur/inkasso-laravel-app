<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Schema;

class ManageFieldsettings extends Component
{
    public string $activeModel = 'sagers'; // current tab
    public array $models = [];             // available tabs
    public array $settings = [];           // field settings for current tab

    protected $queryString = ['activeModel'];

    public function mount()
    {
        // List all models you want tabs for
        $this->models = ['sagers', 'debitors', 'kreditors', 'sagsbehandlers', 'konsulenters'];

        // Ensure activeModel is valid
        if (!in_array($this->activeModel, $this->models)) {
            $this->activeModel = $this->models[0];
        }

        $this->loadSettingsForModel($this->activeModel);
    }

    public function updatedActiveModel($value)
    {
        if (!in_array($value, $this->models)) return;

        $this->activeModel = $value;
        $this->loadSettingsForModel($value);
    }

    protected function loadSettingsForModel(string $modelName)
    {
        $table = "{$modelName}_fieldsettings";

        if (!Schema::hasTable($table)) {
            $this->settings = [];
            return;
        }

        $columns = Schema::getColumnListing($table);
        $this->settings = [];

        foreach ($columns as $column) {
            // Use the column name as key to avoid duplicates
            $key = "{$modelName}_{$column}";

            $this->settings[$key] = [
                'field_name' => $column,
                'alias'      => null,
                'visible'    => true,
                'required'   => false,
                'readonly'   => false,
                'roles'      => [],
                'field_type' => 'text',
                'description'=> '',
                'legacy'     => '',
                'section'    => 'general',
                'column'     => 1,
                'sort_order' => 0,
            ];
        }
    }

    public function save()
    {
        $table = "{$this->activeModel}_fieldsettings";

        foreach ($this->settings as $settingData) {
            if (empty($settingData['field_name'])) continue;

            \DB::table($table)->updateOrInsert(
                ['field_name' => $settingData['field_name']],
                [
                    'alias'      => $settingData['alias'] ?? $settingData['field_name'],
                    'visible'    => $settingData['visible'] ?? 1,
                    'required'   => $settingData['required'] ?? 0,
                    'readonly'   => $settingData['readonly'] ?? 0,
                    'roles'      => json_encode($settingData['roles'] ?? []),
                    'field_type' => $settingData['field_type'] ?? 'text',
                    'description'=> $settingData['description'] ?? null,
                    'legacy'     => $settingData['legacy'] ?? null,
                    'section'    => $settingData['section'] ?? 'general',
                    'column'     => $settingData['column'] ?? 1,
                    'sort_order' => $settingData['sort_order'] ?? 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        session()->flash('success', 'Field settings saved!');
    }

    public function render()
    {
        return view('liveWire.manage-field-settings', [
            'settings'    => $this->settings,
            'models'      => $this->models,
            'activeModel' => $this->activeModel,
        ]);
    }
}
