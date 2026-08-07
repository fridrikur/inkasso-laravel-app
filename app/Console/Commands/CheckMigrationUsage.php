<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Finder\Finder;
use ReflectionClass;
use Illuminate\Database\Eloquent\Model;

class CheckMigrationUsage extends Command
{
    protected $signature = 'migration:check-usage {table? : Navnet på en specifik tabel du vil analysere}';
    protected $description = 'Præcis analyse af databasetabeller med automatisk registrering af pakke/vendor-modeller.';

    protected array $systemTables = [
        'migrations',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
    ];

    public function handle(): int
    {
        $targetTable = $this->argument('table');

        $this->info('🔍 Indlæser kildekode og kortlægger lokale samt vendor Eloquent-modeller...');

        $codebase = $this->loadCodebaseContent();

        // 1. Kortlæg modeller (både app/Models/ og importerede vendor-modeller som Spatie Activity)
        $modelMap = $this->buildModelAndPivotMap($codebase);

        $migrationFiles = iterator_to_array((new Finder())->files()->in(database_path('migrations'))->name('*.php'));
        $tables = Schema::getTableListing();

        $this->newLine();
        $this->info('📊 Analyserer databasetabeller...');
        $this->line(str_repeat('-', 75));

        $unusedTables = [];

        foreach ($tables as $table) {
            if (in_array($table, $this->systemTables)) {
                continue;
            }

            if ($targetTable && strtolower($targetTable) !== strtolower($table)) {
                continue;
            }

            $reasons = [];
            $isUsed = false;

            // A. TJEK OM TABELLEN TILHØRER EN LOKAL ELLER VENDOR MODEL
            if (isset($modelMap['tables_to_models'][$table])) {
                $associatedModels = $modelMap['tables_to_models'][$table];
                
                foreach ($associatedModels as $modelClass) {
                    $shortModelName = class_basename($modelClass);
                    
                    // Tjek om selve Model-klassen/navnet bruges i kildekoden
                    $modelMatches = $this->countExactOccurrences($codebase, $shortModelName);

                    if ($modelMatches > 0) {
                        $isUsed = true;
                        $reasons[] = "Model '{$shortModelName}' ({$modelClass}) aktiv i koden ({$modelMatches}x)";
                    }
                }
            }

            // B. TJEK OM TABELLEN ER EN REGISTRERET PIVOT-TABEL
            if (isset($modelMap['pivot_tables'][$table])) {
                $isUsed = true;
                $definedIn = implode(', ', $modelMap['pivot_tables'][$table]);
                $reasons[] = "Pivot-tabel defineret i relationer på: {$definedIn}";
            }

            // C. TJEK FOR DIREKTE STRENG-MATCHES PÅ TABELNAVNET
            $tableMatches = $this->countExactOccurrences($codebase, $table);
            if ($tableMatches > 0) {
                $isUsed = true;
                $reasons[] = "Direkte tabel-reference i koden ({$tableMatches}x)";
            }

            // KONKLUSION FOR TABELLEN
            $matchingMigration = $this->findMigrationFileForTable($table, $migrationFiles);

            if ($isUsed) {
                $reasonText = implode(' | ', $reasons);
                $this->line("  <fg=green>✓ {$table}</> -> <fg=gray>{$reasonText}</>");
            } else {
                $migrationNote = $matchingMigration ? ' [Migration fundet]' : ' [Ingen migration]';
                $this->line("  <fg=red>❌ {$table}</> -> <fg=red>0 referencer i Modeller, Vendor-pakker eller Kildekode!</>{$migrationNote}");

                $unusedTables[] = [
                    'table' => $table,
                    'migration_file' => $matchingMigration,
                ];
            }
        }

        $this->newLine();
        $this->line(str_repeat('=', 75));

        // INTERAKTIV PROMPT
        if (!empty($unusedTables)) {
            $this->warn("Fundet " . count($unusedTables) . " ubenyttet(de) tabel(ler).");
            $this->newLine();

            foreach ($unusedTables as $info) {
                $tableName = $info['table'];
                $migration = $info['migration_file'];

                $this->line("<fg=yellow;options=bold>Tabel til overvejelse: {$tableName}</>");
                if ($migration) {
                    $this->line("Tilrettelagt via migration: database/migrations/" . $migration->getFilename());
                }

                if ($this->confirm("Ønsker du at slette tabellen '{$tableName}' permanent fra MySQL?", false)) {
                    try {
                        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                        Schema::dropIfExists($tableName);
                        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                        $this->info("✅ Tabellen '{$tableName}' blev slettet fra databasen.");
                    } catch (\Exception $e) {
                        $this->error("❌ Kunne ikke slette tabel: " . $e->getMessage());
                    }
                }

                if ($migration && $this->confirm("Ønsker du OGSÅ at slette migrationsfilen '{$migration->getFilename()}'?", false)) {
                    if (unlink($migration->getRealPath())) {
                        $this->info("✅ Migrationsfilen {$migration->getFilename()} blev slettet.");
                    } else {
                        $this->error("❌ Kunne ikke slette migrationsfilen.");
                    }
                }

                $this->line(str_repeat('-', 50));
            }
        } else {
            $this->info("Scan fuldført: Alle analyserede tabeller har aktive Modeller eller kildekode-referencer.");
        }

        return self::SUCCESS;
    }

    /**
     * Dyb-skanner app/Models samt alle `use`-statements i projektet for at opdage både lokale og vendor-modeller.
     */
    protected function buildModelAndPivotMap(string $codebase): array
    {
        $tablesToModels = [];
        $pivotTables = [];
        $scannedClasses = [];

        // 1. Find alle lokale modeller under app/Models/
        $modelFiles = (new Finder())->files()->in(app_path('Models'))->name('*.php');
        foreach ($modelFiles as $file) {
            // Tjek at filen reelt eksisterer på disken
            if (!file_exists($file->getRealPath())) {
                continue;
            }

            $relativePath = str_replace([app_path() . '/', '.php'], '', $file->getRealPath());
            $scannedClasses[] = 'App\\' . str_replace('/', '\\', $relativePath);
        }

        // 2. Scan kildekoden for `use ...;` linjer for at opdage vendor-modeller
        if (preg_match_all('/use\s+([A-Za-z0-9_\\\\]+);/', $codebase, $matches)) {
            foreach ($matches[1] as $importedClass) {
                $scannedClasses[] = trim($importedClass);
            }
        }

        $scannedClasses = array_unique($scannedClasses);

        // 3. Evaluer hver klasse og hent dens faktiske tabelnavn ($model->getTable())
        foreach ($scannedClasses as $className) {
            try {
                // Tjek om klassen findes uden at kaste uventede include-fejl
                if (!class_exists($className)) {
                    continue;
                }

                $reflection = new ReflectionClass($className);
                if ($reflection->isAbstract()) {
                    continue;
                }

                if ($reflection->isSubclassOf(Model::class)) {
                    /** @var Model $instance */
                    $instance = new $className();
                    $table = $instance->getTable();
                    $tablesToModels[$table][] = $className;
                }
            } catch (\Throwable $e) {
                // Skjul fejl hvis klassen/filen mangler eller ikke kan instansieres
                continue;
            }
        }

        // 4. Scan koden i app/ for belongsToMany pivot-tabeller
        $phpFiles = (new Finder())->files()->in(app_path())->name('*.php');
        foreach ($phpFiles as $file) {
            $fileContent = $file->getContents();
            if (preg_match_all("/belongsToMany\s*\([^,]+,\s*['\"]([^'\"]+)['\"]/i", $fileContent, $matches)) {
                foreach ($matches[1] as $pivotTable) {
                    $pivotTables[$pivotTable][] = $file->getBasename('.php');
                }
            }
        }

        return [
            'tables_to_models' => $tablesToModels,
            'pivot_tables' => $pivotTables,
        ];
    }

    protected function findMigrationFileForTable(string $table, array $migrationFiles): ?\Symfony\Component\Finder\SplFileInfo
    {
        $patterns = [
            "Schema::create('{$table}'",
            "Schema::create(\"{$table}\"",
            "Schema::table('{$table}'",
            "Schema::table(\"{$table}\"",
        ];

        foreach ($migrationFiles as $file) {
            $content = $file->getContents();
            foreach ($patterns as $pattern) {
                if (str_contains($content, $pattern)) {
                    return $file;
                }
            }
        }

        return null;
    }

    protected function countExactOccurrences(string $content, string $term): int
    {
        if (empty($term) || strlen($term) < 2) return 0;

        $pattern = '/\b' . preg_quote($term, '/') . '\b/i';
        $matches = preg_match_all($pattern, $content);

        return $matches !== false ? $matches : 0;
    }

    protected function loadCodebaseContent(): string
    {
        $content = '';
        $directories = [
            app_path(),
            base_path('routes'),
            resource_path('views'),
            database_path('seeders'),
        ];

        foreach ($directories as $dir) {
            if (is_dir($dir)) {
                $files = (new Finder())->files()->in($dir)->name(['*.php', '*.blade.php']);
                foreach ($files as $file) {
                    $content .= "\n" . $file->getContents();
                }
            }
        }

        return $content;
    }
}