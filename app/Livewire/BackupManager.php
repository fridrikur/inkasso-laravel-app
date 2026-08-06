<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupManager extends Component
{
    public bool $showRestoreModal = false;
    public ?string $selectedBackupForRestore = null;

    // Statistik-state efter fuldført restore
    public bool $showStatsModal = false;
    public ?array $restoreStats = null;

    public function runBackup(): void
    {
        try {
            Artisan::call('db:backup');
            $this->dispatch('toast', message: 'Ny database-backup blev oprettet!', type: 'success', icon: 'check');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Fejl under backup: ' . $e->getMessage(), type: 'error', icon: 'error');
        }
    }

    public function confirmRestore(string $filename): void
    {
        $this->selectedBackupForRestore = $filename;
        $this->showRestoreModal = true;
    }

    public function cancelRestore(): void
    {
        $this->showRestoreModal = false;
        $this->selectedBackupForRestore = null;
    }

    public function restoreBackup(): void
    {
        if (!$this->selectedBackupForRestore) {
            return;
        }

        $filePath = storage_path('app/backups/' . basename($this->selectedBackupForRestore));

        if (!File::exists($filePath)) {
            $this->dispatch('toast', message: 'Backupfilen blev ikke fundet.', type: 'error', icon: 'error');
            $this->cancelRestore();
            return;
        }

        $startTime = microtime(true);

        try {
            $config = config('database.connections.mysql');
            $password = $config['password'] ? "-p'" . addslashes($config['password']) . "'" : '';

            $command = sprintf(
                'mysql -u%s %s -h%s -P%s %s < %s',
                escapeshellarg($config['username']),
                $password,
                escapeshellarg($config['host']),
                escapeshellarg($config['port']),
                escapeshellarg($config['database']),
                escapeshellarg($filePath)
            );

            $returnVar = null;
            $output = [];
            exec($command, $output, $returnVar);

            $executionTime = round(microtime(true) - $startTime, 2);

            if ($returnVar === 0) {
                // Beregn statistik ud fra den nyligt importerede database
                $tables = DB::select('SHOW TABLES');
                $dbName = $config['database'];
                $tableColumnName = "Tables_in_{$dbName}";
                
                $totalRows = 0;
                $tableDetails = [];

                foreach ($tables as $tableObj) {
                    $tableName = $tableObj->$tableColumnName ?? current((array)$tableObj);
                    $rowCount = DB::table($tableName)->count();
                    $totalRows += $rowCount;
                    $tableDetails[] = [
                        'name' => $tableName,
                        'rows' => $rowCount,
                    ];
                }

                $fileSize = File::size($filePath);

                // Gem statistik til visning i modal
                $this->restoreStats = [
                    'filename' => $this->selectedBackupForRestore,
                    'file_size' => $this->formatSize($fileSize),
                    'execution_time' => $executionTime . ' sek.',
                    'table_count' => count($tables),
                    'total_rows' => number_format($totalRows, 0, ',', '.'),
                    'tables' => $tableDetails,
                ];

                $this->showRestoreModal = false;
                $this->showStatsModal = true;

                // Udsend Toast notification
                $this->dispatch('toast', 
                    message: "Databasen blev gendannet! ({$totalRows} rækker importeret over " . count($tables) . " tabeller)", 
                    type: 'success', 
                    icon: 'check'
                );
            } else {
                $this->dispatch('toast', message: 'Fejl under importen via mysql CLI.', type: 'error', icon: 'error');
                $this->cancelRestore();
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Fejl ved gendannelse: ' . $e->getMessage(), type: 'error', icon: 'error');
            $this->cancelRestore();
        }
    }

    public function closeStatsModal(): void
    {
        $this->showStatsModal = false;
        $this->restoreStats = null;
    }

    public function downloadBackup(string $filename): ?BinaryFileResponse
    {
        $filePath = storage_path('app/backups/' . basename($filename));

        if (File::exists($filePath)) {
            return response()->download($filePath);
        }

        $this->dispatch('toast', message: 'Filen kunne ikke findes.', type: 'error', icon: 'error');
        return null;
    }

    public function deleteBackup(string $filename): void
    {
        $filePath = storage_path('app/backups/' . basename($filename));

        if (File::exists($filePath)) {
            File::delete($filePath);
            $this->dispatch('toast', message: 'Backupfilen blev slettet.', type: 'success', icon: 'check');
        } else {
            $this->dispatch('toast', message: 'Kunne ikke finde filen der skulle slettes.', type: 'error', icon: 'error');
        }
    }

    public function getBackupsProperty(): array
    {
        $directory = storage_path('app/backups');

        if (!File::exists($directory)) {
            return [];
        }

        $files = File::files($directory);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatSize($file->getSize()),
                    'raw_size' => $file->getSize(),
                    'created_at' => \Carbon\Carbon::createFromTimestamp($file->getMTime()),
                ];
            }
        }

        usort($backups, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return $backups;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2, ',', '.') . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2, ',', '.') . ' KB';
        }
        return $bytes . ' bytes';
    }

    public function render()
    {
        return view('livewire.backup-manager', [
            'backups' => $this->backups,
        ]);
    }
}