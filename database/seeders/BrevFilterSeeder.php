<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrevFilterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('breve_filter')->truncate();

        $filters = [
            ['id' => 1, 'brevID' => 4, 'adresse' => -1, 'dato' => -1, 'navn' => -1, 'sagsnr' => -1, 'emne' => 0, 'skjulalle' => 0, 'visalle' => 0],
            ['id' => 2, 'brevID' => 8, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 3, 'brevID' => 54, 'adresse' => -1, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => 0, 'visalle' => 0],
            ['id' => 4, 'brevID' => 93, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 5, 'brevID' => 88, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 6, 'brevID' => 56, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 7, 'brevID' => 52, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 8, 'brevID' => 1, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 9, 'brevID' => 58, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 10, 'brevID' => 19, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => 0, 'visalle' => 0],
            ['id' => 11, 'brevID' => 59, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => 0, 'visalle' => 0],
            ['id' => 12, 'brevID' => 87, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => 0, 'visalle' => 0],
            ['id' => 13, 'brevID' => 57, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => 0, 'visalle' => 0],
            ['id' => 14, 'brevID' => 12, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
            ['id' => 15, 'brevID' => 18, 'adresse' => -1, 'dato' => -1, 'navn' => -1, 'sagsnr' => -1, 'emne' => 0, 'skjulalle' => 0, 'visalle' => 0],
            ['id' => 16, 'brevID' => 120, 'adresse' => 0, 'dato' => 0, 'navn' => 0, 'sagsnr' => 0, 'emne' => 0, 'skjulalle' => -1, 'visalle' => 0],
        ];

        foreach ($filters as $filter) {
            DB::table('breve_filter')->updateOrInsert(['id' => $filter['id']], $filter);
        }
    }
}