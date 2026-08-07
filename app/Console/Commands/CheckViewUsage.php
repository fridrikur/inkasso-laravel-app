<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class CheckViewUsage extends Command
{
    protected $signature = 'view:check-usage {view? : Navnet på den blade-fil eller view-sti du vil tjekke (f.eks. sag-form.blade.php eller livewire.sager.sag-form)}';
    protected $description = 'Søger i projektets kildekode efter referencer til en eller alle Blade-visninger med automatisk undermappesøgning og slettedialog.';

    public function handle(): int
    {
        $targetView = $this->argument('view');

        $this->info('🔍 Indlæser kildekode for søgning...');
        $codebase = $this->loadCodebaseContent();

        if ($targetView) {
            $this->checkSingleView($targetView, $codebase);
        } else {
            $this->checkAllViews($codebase);
        }

        return self::SUCCESS;
    }

    /**
     * Tjekker én specifik blade-fil eller søger på filnavnet i alle undermapper.
     */
    protected function checkSingleView(string $targetView, string $codebase): void
    {
        // Rengør filnavn (sikr at det ender på .blade.php ved søgning)
        $searchFileName = basename(str_replace('.blade.php', '', $targetView)) . '.blade.php';

        // Søg automatisk efter filen i alle undermapper af resources/views/
        $finder = (new Finder())->files()->in(resource_path('views'))->name($searchFileName);
        $foundFiles = iterator_to_array($finder);

        if (empty($foundFiles)) {
            $this->newLine();
            $this->error("❌ Filen '{$searchFileName}' blev IKKE fundet nogen steder under resources/views/");
            return;
        }

        foreach ($foundFiles as $file) {
            $realPath = $file->getRealPath();
            $relativePath = str_replace(resource_path('views/') . '/', '', $realPath);
            $cleanPath = str_replace('.blade.php', '', $relativePath);
            $dotNotation = str_replace('/', '.', $cleanPath);
            $fileNameOnly = $file->getBasename('.blade.php');

            $this->newLine();
            $this->info("🔎 Analyserer: <fg=cyan>resources/views/{$relativePath}</>");
            $this->line(str_repeat('-', 60));

            $matches = $this->findReferences($cleanPath, $dotNotation, $fileNameOnly, $codebase, $realPath);

            if ($matches['count'] > 0) {
                $this->info("✅ View'et benyttes i projektet! Fundet {$matches['count']} reference(r):");
                foreach ($matches['found_patterns'] as $pattern) {
                    $this->line("  <fg=green>• Match:</> {$pattern}");
                }
                $this->newLine();
                $this->comment("💡 Da filen er i brug i projektet, tilbydes sletning ikke automatisk.");
            } else {
                $this->error("⚠️  Ingen referencer fundet til '{$relativePath}' i hele projektet!");
                $this->newLine();

                // Spørg om sletning hvis 0 referencer er fundet
                if ($this->confirm("Ønsker du at slette 'resources/views/{$relativePath}'?", false)) {
                    if (unlink($realPath)) {
                        $this->info("✅ Filen blev slettet succesfuldt.");
                    } else {
                        $this->error("❌ Kunne ikke slette filen på disken.");
                    }
                }
            }
        }
    }

    /**
     * Scanner samtlige views i resources/views for ubenyttede filer.
     */
    protected function checkAllViews(string $codebase): void
    {
        $this->newLine();
        $this->info('📊 Scanner alle .blade.php filer i resources/views/...');
        $this->newLine();

        $viewFiles = (new Finder())->files()->in(resource_path('views'))->name('*.blade.php');
        $deletableViews = [];
        $usedCount = 0;

        foreach ($viewFiles as $file) {
            $relativePath = str_replace(resource_path('views/') . '/', '', $file->getRealPath());
            $cleanPath = str_replace('.blade.php', '', $relativePath);
            $dotNotation = str_replace('/', '.', $cleanPath);
            $fileNameOnly = $file->getBasename('.blade.php');

            $matches = $this->findReferences($cleanPath, $dotNotation, $fileNameOnly, $codebase, $file->getRealPath());

            if ($matches['count'] === 0) {
                $this->line("  <fg=red>❌ Ubrugt view:</> resources/views/{$relativePath}");
                $deletableViews[] = [
                    'relative_path' => 'resources/views/' . $relativePath,
                    'full_path' => $file->getRealPath(),
                ];
            } else {
                $usedCount++;
            }
        }

        $this->newLine();
        $this->line(str_repeat('=', 60));
        $unusedCount = count($deletableViews);

        if ($unusedCount > 0) {
            $this->warn("Scan fuldført: {$usedCount} aktive views fundet, <fg=red>{$unusedCount} tilsyneladende ubrugte views</>.");
            $this->newLine();

            foreach ($deletableViews as $viewInfo) {
                if ($this->confirm("Ønsker du at slette '{$viewInfo['relative_path']}'?", false)) {
                    if (unlink($viewInfo['full_path'])) {
                        $this->info("✅ Filen {$viewInfo['relative_path']} blev slettet.");
                    } else {
                        $this->error("❌ Kunne ikke slette filen: {$viewInfo['full_path']}");
                    }
                }
            }
        } else {
            $this->info("Scan fuldført: Alle {$usedCount} skannede views ser ud til at være i brug.");
        }
    }

    /**
     * Søger efter varianter af view-navnet i kildekoden.
     */
    protected function findReferences(string $cleanPath, string $dotNotation, string $fileNameOnly, string $codebase, ?string $currentFilePath = null): array
    {
        $patterns = [
            "'{$dotNotation}'",
            "\"{$dotNotation}\"",
            "'{$cleanPath}'",
            "\"{$cleanPath}\"",
            "view('{$dotNotation}'",
            "view(\"{$dotNotation}\"",
            "@include('{$dotNotation}'",
            "@extends('{$dotNotation}'",
            "<x-{$dotNotation}",
            "<x-{$fileNameOnly}",
        ];

        $searchTarget = $codebase;

        if ($currentFilePath && file_exists($currentFilePath)) {
            $selfContent = file_get_contents($currentFilePath);
            $pos = strpos($searchTarget, $selfContent);
            if ($pos !== false) {
                $searchTarget = substr_replace($searchTarget, '', $pos, strlen($selfContent));
            }
        }

        $foundPatterns = [];
        $count = 0;

        foreach ($patterns as $pattern) {
            $matches = substr_count($searchTarget, $pattern);
            if ($matches > 0) {
                $count += $matches;
                $foundPatterns[] = "{$pattern} ({$matches}x)";
            }
        }

        return [
            'count' => $count,
            'found_patterns' => $foundPatterns,
        ];
    }

    /**
     * Henter alt tekstindhold fra app/, routes/ og resources/views/.
     */
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