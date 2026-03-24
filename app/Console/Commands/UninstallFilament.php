<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('filament:uninstall')]
#[Description('Remove the Filament installation and its features from the project')]
class UninstallFilament extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting Filament uninstallation...');

        // 1. Delete Filament-related files
        $filesToDelete = [
            config_path('filament.php'),
            base_path('FILAMENT.md'),
            base_path('bootstrap/cache/blade-icons.php'),
        ];

        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
                $this->line("Deleted: $file");
            }
        }

        // 2. Delete Filament-related directories
        $directoriesToDelete = [
            app_path('Filament'),
            app_path('Providers/Filament'),
            public_path('css/filament'),
            public_path('fonts/filament'),
            public_path('js/filament'),
            base_path('tests/Feature/Filament'),
        ];

        foreach ($directoriesToDelete as $dir) {
            if (is_dir($dir)) {
                $this->deleteDirectory($dir);
                $this->line("Deleted Directory: $dir");
            }
        }

        // 3. Remove references from project files
        $this->removeReferences();

        $this->info('Filament has been successfully uninstalled!');
        $this->warn('Please run "composer remove filament/filament" to remove the package dependency.');
        $this->warn('You may also want to delete this command file: '.__FILE__);
    }

    /**
     * Recursively delete a directory.
     */
    private function deleteDirectory(string $dir): bool
    {
        if (! file_exists($dir)) {
            return true;
        }

        if (! is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (! $this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Remove references in bootstrap/providers.php, composer.json, and .gitignore.
     */
    private function removeReferences(): void
    {
        // 1. bootstrap/providers.php
        $providersPhp = base_path('bootstrap/providers.php');
        if (file_exists($providersPhp)) {
            $content = file_get_contents($providersPhp);
            $newContent = preg_replace("/\s+App\\\\Providers\\\\Filament\\\\AdminPanelProvider::class,/", '', $content);
            if ($newContent !== $content) {
                file_put_contents($providersPhp, $newContent);
                $this->line('Removed reference from bootstrap/providers.php');
            }
        }

        // 2. composer.json
        $composerJson = base_path('composer.json');
        if (file_exists($composerJson)) {
            $content = file_get_contents($composerJson);
            // Remove scripts added by Filament
            $newContent = preg_replace("/\s+\"@php artisan filament:upgrade\",?/", '', $content);
            $newContent = preg_replace("/\s+\"@php artisan icons:cache\",?/", '', $newContent);

            // Clean up potentially dangling comma if filament:upgrade or icons:cache were at the end of the scripts array
            $newContent = preg_replace('/",\s+]/', "\"\n        ]", $newContent);

            if ($newContent !== $content) {
                file_put_contents($composerJson, $newContent);
                $this->line('Removed Filament scripts from composer.json');
            }
        }

        // 3. .gitignore
        $gitignore = base_path('.gitignore');
        if (file_exists($gitignore)) {
            $content = file_get_contents($gitignore);
            // Matches the section added for Filament resources
            $pattern = "/\r?\n# Filament resources\r?\n\/public\/css\/filament\/\r?\n\/public\/fonts\/filament\/\r?\n\/public\/js\/filament\/\r?\n?/";
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, "\n", $content);
                file_put_contents($gitignore, $newContent);
                $this->line('Removed Filament section from .gitignore');
            }
        }
    }
}
