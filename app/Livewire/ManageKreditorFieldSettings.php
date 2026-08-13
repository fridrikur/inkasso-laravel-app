<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kreditor; // The actual model table
use App\Models\KreditorFieldsetting; // The corresponding fieldsettings table
use Illuminate\Support\Facades\Schema;

class ManageKreditorFieldsettings extends Component
{
    public array $settings = [];

    public function mount()
    {
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        $table = 'kreditors'; // model table name
        $settingsTable = 'kreditor_fieldsettings';

        if (!Schema::hasTable($table)) {
            $this->settings = [];
            return;
        }

        $columns = Schema::getColumnListing($table);
        $this->settings = [];

        foreach ($columns as $column) {
            $field = kreditorFieldsetting::firstOrNew(['field_name' => $column]);

            $this->settings[$column] = [
                'id'          => $field->id,
                'field_name'  => $column,
                'alias'       => $field->alias ?? $column,
                'visible'     => (bool) ($field->visible ?? true),
                'required'    => (bool) ($field->required ?? false),
                'readonly'    => (bool) ($field->readonly ?? false),
                'roles'       => $field->roles ?? [],
                'field_type'  => $field->field_type ?? 'text',
                'description' => $field->description ?? '',
                'section'     => $field->section ?? 'general',
                'column'      => $field->column ?? 1,
                'sort_order'  => $field->sort_order ?? 0,
            ];
        }
    }

    public function save()
    {
        foreach ($this->settings as $data) {
            KreditorFieldsetting::updateOrCreate(
                ['field_name' => $data['field_name']],
                [
                    'alias'      => $data['alias'],
                    'visible'    => $data['visible'],
                    'required'   => $data['required'],
                    'readonly'   => $data['readonly'],
                    'roles'      => $data['roles'],
                    'field_type' => $data['field_type'],
                    'description'=> $data['description'],
                    'section'    => $data['section'],
                    'column'     => $data['column'],
                    'sort_order' => $data['sort_order'],
                ]
            );
        }

        session()->flash('success', 'Kreditor field settings saved!');
    }

    public function render()
    {
        return view('livewire.manage-kreditor-fieldsettings', [
            'settings' => $this->settings
        ]);
    }
}
