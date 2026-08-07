<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;

class CheckModelUsage extends Command
{
    protected $signature = 'model:check-usage {model? : Navnet på en specifik model du vil tjekke (f.eks. Debitor eller Sager)}';
    protected $description = 'Scanner alle Eloquent modeller for at se om de har tilknyttede views eller bruges i frontend/controllers.';

    public function handle(): int
    {
        $targetModel = $this->argument('model');
        $modelFiles = iterator_to_array((new Finder())->files()->in(app_path('Models'))->name('*.php'));

        // 1. TJEK OM DEN ANGIVNE MODEL REELT EKSISTERER
        if ($targetModel) {
            $found = false;
            foreach ($modelFiles as $file) {
                if (strtolower($file->getBasename('.php')) === strtolower($targetModel)) {
                    $found = true;
                    $modelFiles = [$file]; // Begræns søgningen til kun denne model
                    break;
                }
            }

            if (!$found) {
                $this->newLine();
                $this->error("❌ Fejl: Modellen '{$targetModel}.php' blev IKKE fundet i app/Models/");
                return self::FAILURE;
            }
        }

        $this->info('🔍 Indlæser kildekode og views til præcis ordgrænse-analyse...');
        
        $viewsContent = $this->loadDirectoryContent(resource_path('views'));
        $backendContent = $this->loadDirectoryContent(app_path());

        $this->newLine();
        $this->info('📊 Analyserer Eloquent-modeller...');
        $this->line(str_repeat('-', 65));

        $unusedModels = 0;
        $deletableModels = [];

        foreach ($modelFiles as $file) {
            $modelName = $file->getBasename('.php');
            $className = 'App\\Models\\' . $modelName;
            
            $tableName = Str::snake(Str::pluralStudly($modelName));
            if (class_exists($className)) {
                try {
                    $instance = new $className();
                    if (method_exists($instance, 'getTable')) {
                        $tableName = $instance->getTable();
                    }
                } catch (\Throwable $e) {
                    // Ignorer abstrakte klasser eller konstruktør-fejl
                }
            }

            // Præcis ordgrænse-søgning (forhindrer at 'Sag' matcher 'Sager' eller 'Sagsbehandler')
            $viewMatches = $this->countExactOccurrences($viewsContent, [$modelName, $tableName]);
            $backendMatches = $this->countExactOccurrences($backendContent, [$modelName], $file->getRealPath());

            // Tjek om der findes en dedikeret mappe under resources/views/
            $hasDedicatedViewFolder = is_dir(resource_path('views/' . Str::slug($tableName))) 
                || is_dir(resource_path('views/livewire/' . Str::slug($tableName)));

            // Evaluering af status
            if ($viewMatches === 0 && !$hasDedicatedViewFolder) {
                if ($backendMatches === 0) {
                    $this->line("  <fg=red>❌ {$modelName}</> -> <fg=red>Ingen referencer fundet i hverken Views eller Backend!</>");
                    $unusedModels++;
                    $deletableModels[] = [
                        'name' => $modelName,
                        'path' => $file->getRealPath(),
                    ];
                } else {
                    $this->line("  <fg=yellow>⚠️  {$modelName}</> -> Bruges i Backend ({$backendMatches}x), men har <fg=yellow>INGEN direkte visning/views</>.");
                }
            } else {
                $folderNote = $hasDedicatedViewFolder ? ' [Mappe fundet]' : '';
                $this->line("  <fg=green>✓ {$modelName}</> -> Fundet i views ({$viewMatches}x){$folderNote} og backend ({$backendMatches}x).");
            }
        }

        $this->newLine();
        $this->line(str_repeat('=', 65));

        // 2. PROMPT OM SLETNING AF UBRUGTE MODELLER
        if (!empty($deletableModels)) {
            $this->warn("Fundet " . count($deletableModels) . " ubenyttet(de) model(ler).");
            $this->newLine();

            foreach ($deletableModels as $modelInfo) {
                if ($this->confirm("Ønsker du at slette modellen '{$modelInfo['name']}.php' fra app/Models/?", false)) {
                    if (unlink($modelInfo['path'])) {
                        $this->info("✅ Modellen {$modelInfo['name']}.php blev slettet succesfuldt.");
                    } else {
                        $this->error("❌ Kunne ikke slette filen på stien: {$modelInfo['path']}");
                    }
                }
            }
        } else {
            $this->info("Alle skannede modeller er aktuelt i brug.");
        }

        return self::SUCCESS;
    }

    /**
     * Tæller eksakte ord-matches vha. RegEx ordgrænser (\b).
     */
    protected function countExactOccurrences(string $content, array $keywords, ?string $excludeFilePath = null): int
    {
        $searchTarget = $content;

        if ($excludeFilePath && file_exists($excludeFilePath)) {
            $selfContent = file_get_contents($excludeFilePath);
            $pos = strpos($searchTarget, $selfContent);
            if ($pos !== false) {
                $searchTarget = substr_replace($searchTarget, '', $pos, strlen($selfContent));
            }
        }

        $count = 0;
        foreach ($keywords as $word) {
            if (empty($word) || strlen($word) < 2) continue;

            // \b sikrer at vi kun matcher hele ord og ikke delstrenge
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $matches = preg_match_all($pattern, $searchTarget);
            if ($matches !== false) {
                $count += $matches;
            }
        }

        return $count;
    }

    /**
     * Indlæser alt tekstindhold fra en mappe.
     */
    protected function loadDirectoryContent(string $path): string
    {
        if (!is_dir($path)) {
            return '';
        }

        $content = '';
        $files = (new Finder())->files()->in($path)->name(['*.php', '*.blade.php']);

        foreach ($files as $file) {
            $content .= "\n" . $file->getContents();
        }

        return $content;
    }
}