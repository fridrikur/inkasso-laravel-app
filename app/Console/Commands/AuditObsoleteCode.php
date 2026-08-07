<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class AuditObsoleteCode extends Command
{
    protected $signature = 'app:audit-obsolete';
    protected $description = 'Scanner projektet for ubrugte tabeller, forældede Modeller og døde relationer.';

    public function handle(): int
    {
        $this->info('🔍 Starter konsoliderings-analyse af projektet...');
        $this->newLine();

        $this->checkOrphanedTables();
        $this->checkMissingTablesForModels();
        $this->checkUnusedModelsInCodebase();

        $this->newLine();
        $this->info('✅ Analyse fuldført!');

        return self::SUCCESS;
    }

    /**
     * 1. Find tabeller i MySQL som IKKE har en tilhørende Eloquent Model
     */
    protected function checkOrphanedTables(): void
    {
        $this->sectionHeader('1. Tjekker for ubrugte/forældede databasetabeller');

        $tables = Schema::getTableListing();
        $ignoreTables = ['migrations', 'failed_jobs', 'personal_access_tokens', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches'];
        
        $modelFiles = collect((new Finder())->files()->in(app_path('Models')))->map(fn ($file) => $file->getBasename('.php'));
        
        $orphanedTables = [];

        foreach ($tables as $table) {
            if (in_array($table, $ignoreTables)) {
                continue;
            }

            // Gæt modelnavn ud fra tabelnavn (f.eks. sagervalglistes -> Sagervalgliste)
            $expectedModel = Str::studly(Str::singular($table));

            if (!$modelFiles->contains($expectedModel)) {
                $orphanedTables[] = $table;
            }
        }

        if (empty($orphanedTables)) {
            $this->line('  <fg=green>✓ Ingen forældede tabeller fundet.</>');
        } else {
            foreach ($orphanedTables as $table) {
                $this->line("  <fg=yellow>⚠️ Tabellen '$table' har ingen direkte matchende Model i app/Models.</>");
            }
        }
    }

    /**
     * 2. Find Modeller hvor databasetabellen er slettet eller mangler
     */
    protected function checkMissingTablesForModels(): void
    {
        $this->newLine();
        $this->sectionHeader('2. Tjekker for Modeller uden eksisterende databasetabel');

        $modelFiles = (new Finder())->files()->in(app_path('Models'));

        foreach ($modelFiles as $file) {
            $className = 'App\\Models\\' . $file->getBasename('.php');
            
            if (!class_exists($className)) {
                continue;
            }

            try {
                $model = new $className();
                if ($model instanceof \Illuminate\Database\Eloquent\Model) {
                    $table = $model->getTable();
                    if (!Schema::hasTable($table)) {
                        $this->line("  <fg=red>❌ Modellen '{$file->getBasename()}' peger på tabellen '$table', som IKKE eksisterer i databasen!</>");
                    }
                }
            } catch (\Throwable $e) {
                // Hop over abstrakte klasser eller traits
            }
        }
    }

    /**
     * 3. Tjek om Modeller overhovedet refereres i app/ mappen
     */
    protected function checkUnusedModelsInCodebase(): void
    {
        $this->newLine();
        $this->sectionHeader('3. Tjekker for Modeller der aldrig refereres i kildekoden');

        $modelFiles = (new Finder())->files()->in(app_path('Models'));
        $appFiles = (new Finder())->files()->in(app_path())->exclude('Models');

        $searchContent = '';
        foreach ($appFiles as $file) {
            $searchContent .= $file->getContents();
        }

        foreach ($modelFiles as $file) {
            $modelName = $file->getBasename('.php');
            
            // Tjek hvor mange gange Model-navnet optræder uden for Models-mappen
            $occurrences = substr_count($searchContent, $modelName);

            if ($occurrences === 0) {
                $this->line("  <fg=yellow>⚠️ Modellen '$modelName' benyttes 0 gange i app/ (Controllers, Livewire, FormRequests osv.).</>");
            }
        }
    }

    protected function sectionHeader(string $title): void
    {
        $this->line("<fg=cyan;options=bold>$title</>");
        $this->line(str_repeat('-', strlen($title)));
    }
}