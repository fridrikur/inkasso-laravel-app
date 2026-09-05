<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\DropdownDataSeeder;

class ImportSystemCommand extends Command
{
    protected $signature = 'import:system {--user=} {--kreditor=} {--konsulent=} {--sagsbehandler=} {--debitor=} {--sager=}';
    protected $description = 'Kører komplet system-import af alle SQL-filer i baggrunden';

    public function handle()
    {
        $statusFile = storage_path('app/system_import_status.json');
        $dbConfig = config('database.connections.' . config('database.default'));

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 5, 'message' => 'Starter system-import...']));

        $tasks = [
            'Kreditorer'     => ['cmd' => 'import:kreditorer',     'opt' => $this->option('kreditor')],
            'Brugere'        => ['cmd' => 'import:users',         'opt' => $this->option('user')],
            'Sagsbehandlere' => ['cmd' => 'import:sagsbehandlere', 'opt' => $this->option('sagsbehandler')],
            'Debitorer'      => ['cmd' => 'import:debitorer',      'opt' => $this->option('debitor')],
            'Sager'          => ['cmd' => 'import:sager',          'opt' => $this->option('sager')],
        ];

        $outputLog = [];
        $total = count($tasks);
        $current = 0;

        foreach ($tasks as $name => $task) {
            $current++;
            $progress = (int) (($current / $total) * 80);
            
            if ($task['opt']) {
                $filePath = storage_path($task['opt']);
                File::put($statusFile, json_encode(['status' => 'running', 'progress' => $progress, 'message' => "Importerer {$name}..."]));

                if (file_exists($filePath)) {
                    $exitCode = Artisan::call($task['cmd'], ['file' => $filePath]);
                    if ($exitCode === 0) {
                        $outputLog[] = "✅ {$name}: Fuldført";
                    } else {
                        $outputLog[] = "❌ {$name}: Fejlede";
                    }
                } else {
                    $outputLog[] = "⚠️ {$name}: Fil ikke fundet";
                }
            }
        }

        File::put($statusFile, json_encode(['status' => 'running', 'progress' => 90, 'message' => 'Kører dropdown seeder...']));
        try {
            (new DropdownDataSeeder())->run();
            $outputLog[] = "✅ Dropdown data: Fuldført";
        } catch (\Throwable $e) {
            $outputLog[] = "❌ Dropdown data fejl: " . $e->getMessage();
        }

        File::put($statusFile, json_encode([
            'status' => 'completed', 
            'progress' => 100, 
            'message' => 'System-import fuldført succesfuldt!'
        ]));

        return 0;
    }
}