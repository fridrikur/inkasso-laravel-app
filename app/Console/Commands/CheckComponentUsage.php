<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Str;

class CheckComponentUsage extends Command
{
    protected $signature = 'component:check-usage {component? : Navnet på en specifik Livewire-komponent}';
    protected $description = 'Scanner Livewire-komponenter for ubenyttede klasser og views med case-insensitive søgning.';

    public function handle(): int
    {
        $targetComponent = $this->argument('component');
        $livewirePath = app_path('Livewire');

        if (!is_dir($livewirePath)) {
            $this->error("❌ Mappen 'app/Livewire' blev ikke fundet.");
            return self::FAILURE;
        }

        $this->info('🔍 Indlæser kildekode, routes og views...');
        $codebase = $this->loadCodebaseContent();

        $componentFiles = iterator_to_array(
            (new Finder())->files()->in($livewirePath)->name('*.php')
        );

        $this->newLine();
        $this->info('📊 Analyserer Livewire-komponenter med case-insensitive match...');
        $this->line(str_repeat('-', 70));

        $unusedComponents = [];

        foreach ($componentFiles as $file) {
            $realPath = $file->getRealPath();
            
            $relativePath = str_replace([app_path('Livewire/'), '.php'], '', $realPath);
            $className = 'App\\Livewire\\' . str_replace('/', '\\', $relativePath);
            $shortClassName = $file->getBasename('.php');

            // Generer k-bab-notationer (f.eks. Bemaerkningindex -> bemaerkningindex & bemaerkning-index)
            $dotNotationKebab = collect(explode('/', $relativePath))
                ->map(fn ($segment) => Str::kebab($segment))
                ->implode('.');

            $dotNotationDirect = str_replace('/', '.', strtolower($relativePath));

            $expectedViewPath = resource_path('views/livewire/' . str_replace('.', '/', $dotNotationKebab) . '.blade.php');
            if (!file_exists($expectedViewPath)) {
                $expectedViewPath = resource_path('views/livewire/' . str_replace('.', '/', $dotNotationDirect) . '.blade.php');
            }
            $hasMatchingView = file_exists($expectedViewPath);

            if ($targetComponent) {
                $targetClean = strtolower(str_replace(['/', '\\'], '.', $targetComponent));
                if ($targetClean !== strtolower($dotNotationKebab) && 
                    $targetClean !== strtolower($dotNotationDirect) && 
                    strtolower($targetComponent) !== strtolower($shortClassName)) {
                    continue;
                }
            }

            // Søg efter referencer i kildekoden (case-insensitive)
            $matches = $this->findComponentReferences(
                $shortClassName, 
                $dotNotationKebab, 
                $dotNotationDirect, 
                $className, 
                $codebase, 
                $realPath
            );

            if ($matches['count'] > 0) {
                $patternSummary = implode(', ', $matches['found_patterns']);
                $this->line("  <fg=green>✓ {$shortClassName}</> (<fg=gray>{$dotNotationKebab}</>) -> <fg=gray>Aktiv ({$matches['count']}x: {$patternSummary})</>");
            } else {
                $viewNote = $hasMatchingView ? ' [Blade-view fundet]' : ' [Intet view]';
                $this->line("  <fg=red>❌ {$shortClassName}</> (<fg=gray>{$dotNotationKebab}</>) -> <fg=red>0 referencer fundet!</>{$viewNote}");

                $unusedComponents[] = [
                    'class_name' => $shortClassName,
                    'dot_notation' => $dotNotationKebab,
                    'php_path' => $realPath,
                    'relative_php' => 'app/Livewire/' . $relativePath . '.php',
                    'view_path' => $hasMatchingView ? $expectedViewPath : null,
                    'relative_view' => $hasMatchingView ? str_replace(resource_path('views/'), 'resources/views/', $expectedViewPath) : null,
                ];
            }
        }

        $this->newLine();
        $this->line(str_repeat('=', 70));

        if (!empty($unusedComponents)) {
            $this->warn("Fundet " . count($unusedComponents) . " ubenyttet(de) Livewire-komponent(er).");
            $this->newLine();

            foreach ($unusedComponents as $comp) {
                $this->line("<fg=yellow;options=bold>Komponent: {$comp['class_name']}</> (<fg=gray>{$comp['dot_notation']}</>)");
                $this->line("PHP-fil: {$comp['relative_php']}");
                if ($comp['relative_view']) {
                    $this->line("View-fil: {$comp['relative_view']}");
                }

                if ($this->confirm("Ønsker du at slette komponenten '{$comp['class_name']}' (inkl. evt. tilhørende Blade-view)?", false)) {
                    if (unlink($comp['php_path'])) {
                        $this->info("✅ Slettede PHP-fil: {$comp['relative_php']}");
                    }

                    if ($comp['view_path'] && file_exists($comp['view_path'])) {
                        if (unlink($comp['view_path'])) {
                            $this->info("✅ Slettede Blade-view: {$comp['relative_view']}");
                        }
                    }
                }

                $this->line(str_repeat('-', 50));
            }
        } else {
            $this->info("Scan fuldført: Alle analyserede Livewire-komponenter ser ud til at være i brug.");
        }

        return self::SUCCESS;
    }

    /**
     * Søger efter samtlige mønstre uafhængigt af store/små bogstaver.
     */
    protected function findComponentReferences(
        string $shortClassName, 
        string $dotKebab, 
        string $dotDirect, 
        string $fullClassName, 
        string $codebase, 
        string $currentFilePath
    ): array {
        $searchTarget = $codebase;

        if (file_exists($currentFilePath)) {
            $selfContent = file_get_contents($currentFilePath);
            $pos = strpos($searchTarget, $selfContent);
            if ($pos !== false) {
                $searchTarget = substr_replace($searchTarget, '', $pos, strlen($selfContent));
            }
        }

        $searchTerms = array_unique([
            $shortClassName,
            $dotKebab,
            $dotDirect,
            str_replace('\\', '\\\\', $fullClassName),
            str_replace('\\', '/', $fullClassName),
        ]);

        $foundPatterns = [];
        $count = 0;

        foreach ($searchTerms as $term) {
            if (empty($term) || strlen($term) < 2) continue;

            // Regex-søgning med /i flag (case-insensitive) og ordgrænser (\b)
            $pattern = '/\b' . preg_quote($term, '/') . '\b/i';
            $matches = preg_match_all($pattern, $searchTarget);

            if ($matches !== false && $matches > 0) {
                $count += $matches;
                $foundPatterns[] = "{$term} ({$matches}x)";
            }
        }

        return [
            'count' => $count,
            'found_patterns' => $foundPatterns,
        ];
    }

    protected function loadCodebaseContent(): string
    {
        $content = '';
        $directories = [
            app_path(),
            base_path('routes'),
            resource_path('views'),
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