<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Skaber en hurtig lokal SQL-dump af databasen i storage/app/backups';

    public function handle(): int
    {
        $this->info('📦 Starter database-backup...');

        $config = config('database.connections.mysql');
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'db_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filePath = $backupDir . '/' . $filename;

        // Byg mysqldump kommando ud fra .env konfigurationen
        $password = $config['password'] ? "-p'" . addslashes($config['password']) . "'" : '';
        $command = sprintf(
            'mysqldump -u%s %s -h%s -P%s %s > %s',
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

        if ($returnVar === 0) {
            $this->info("✅ Backup gennemført!");
            $this->line("📍 Fil gemt i: <fg=yellow>storage/app/backups/{$filename}</>");
            return self::SUCCESS;
        }

        $this->error('❌ Der opstod en fejl ved udførelse af mysqldump. Tjek om mysqldump er installeret på dit system.');
        return self::FAILURE;
    }
}