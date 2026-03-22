<?php

namespace App\Console\Commands;

use App\Models\Chirp;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('chirper:uninstall')]
#[Description('Remove the Chirp model and all its features from the project')]
class UninstallChirper extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting Chirper uninstallation...');

        // 1. Delete Chirps from the database
        if (class_exists(Chirp::class)) {
            $this->call('chirper:delete-chirps');
        }

        // 2. Delete Chirp files and directories
        $filesToDelete = [
            // app folder
            app_path('Console/Commands/DeleteAllChirps.php'),
            app_path('Http/Controllers/ChirpAuthController.php'),
            app_path('Http/Controllers/ChirpController.php'),
            app_path('Http/Middleware/ChirpAuthMiddleware.php'),
            app_path('Models/Chirp.php'),
            app_path('Policies/ChirpPolicy.php'),

            // database folder
            database_path('factories/ChirpFactory.php'),
            database_path('migrations/0001_01_01_000009_create_chirps_table.php'),
            database_path('seeders/ChirpSeeder.php'),

            // resources folder
            resource_path('css/chirper.css'),

            // routes folder
            base_path('routes/chirper.php'),
        ];

        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
                $this->line("Deleted: $file");
            }
        }

        $directoriesToDelete = [
            resource_path('views/chirper'),
            resource_path('views/components/chirper'),
            public_path('chirper'),
            base_path('tests/Feature/Chirper'),
        ];

        foreach ($directoriesToDelete as $dir) {
            if (is_dir($dir)) {
                $this->deleteDirectory($dir);
                $this->line("Deleted Directory: $dir");
            }
        }

        // 3. Remove references to Chirp from remaining app files
        $this->removeReferences();

        $this->info('Chirper has been successfully uninstalled!');
        $this->warn('Please run "npm run build" to update your assets.');
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
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (! $this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Remove references in web.php, User.php, and vite.config.js.
     */
    private function removeReferences(): void
    {
        // 1. web.php
        $webPhp = base_path('routes/web.php');
        if (file_exists($webPhp)) {
            $content = file_get_contents($webPhp);
            $newContent = preg_replace("/include __DIR__\.'\/chirper\.php';\r?\n?/", '', $content);
            file_put_contents($webPhp, $newContent);
            $this->line('Removed reference from routes/web.php');
        }

        // 2. User.php
        $userPhp = app_path('Models/User.php');
        if (file_exists($userPhp)) {
            $content = file_get_contents($userPhp);
            // Remove the relationship and its comment
            $newContent = preg_replace("/\s+\/\*\*\s+\* Get the Chirps for the User\.\s+\*\/\s+public function chirps\(\): HasMany\s+\{\s+return \$this->hasMany\(Chirp::class\);\s+\}/s", '', $content);
            // Remove import if exists
            // Since it might not be explicitly imported (same namespace), but it's good to check for HasMany if it's ONLY used for chirps.
            // But User likely uses other things.
            file_put_contents($userPhp, $newContent);
            $this->line('Removed reference from App\Models\User');
        }

        // 3. vite.config.js
        $viteConfig = base_path('vite.config.js');
        if (file_exists($viteConfig)) {
            $content = file_get_contents($viteConfig);
            $newContent = preg_replace("/\s+'resources\/css\/chirper\.css',\s+\/\/ Chirper example/", '', $content);
            file_put_contents($viteConfig, $newContent);
            $this->line('Removed reference from vite.config.js');
        }
    }
}
