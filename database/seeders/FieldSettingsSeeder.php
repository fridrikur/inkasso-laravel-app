<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

// Import fieldsettings models
use App\Models\SagerFieldsetting;
use App\Models\KreditorFieldsetting;
use App\Models\DebitorFieldsetting;
use App\Models\SagsbehandlerFieldsetting;
use App\Models\KonsulentFieldsetting;

class FieldsettingsSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'sagers'         => SagerFieldsetting::class,
            'kreditors'      => KreditorFieldsetting::class,
            'debitors'       => DebitorFieldsetting::class,
            'sagsbehandlers' => SagsbehandlerFieldsetting::class,
            'konsulenters'   => KonsulentFieldsetting::class,
        ];

        foreach ($map as $table => $modelClass) {
            // Skip if the table doesn't exist
            if (!Schema::hasTable($table)) continue;

            $columns = Schema::getColumnListing($table);

            foreach ($columns as $column) {
                // Avoid duplicates
                $modelClass::firstOrCreate(['field_name' => $column], [
                    'alias'      => $column,
                    'visible'    => true,
                    'required'   => false,
                    'readonly'   => false,
                    'roles'      => json_encode([]),
                    'field_type' => 'text',
                    'description'=> '',
                    'legacy'     => '',
                    'section'    => 'general',
                    'column'     => 1,
                    'sort_order' => 0,
                ]);
            }
        }

        $this->command->info('Fieldsettings tables seeded from model tables!');
    }
}
