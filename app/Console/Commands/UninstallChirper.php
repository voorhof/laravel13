<?php

namespace App\Console\Commands;

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
        if (file_exists(app_path('Models/Chirp.php'))) {
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

            // tests folder
            base_path('tests/Unit/ChirpTest.php'),
            base_path('tests/Unit/ChirpPolicyTest.php'),
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
        $this->cleanupTests();

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
            $pattern = "/\s+\/\*\*\s+\* Get the Chirps for the User\.\s+\*\/\s+public function chirps\(\): HasMany\s+\{\s+return \\\$this->hasMany\(Chirp::class\);\s+}/";
            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, '', $content);
                $this->line('Removed relationship from App\Models\User');
            } else {
                $this->error('Could not find relationship in App\Models\User');
                $newContent = $content;
            }

            // Remove import if exists
            $importPattern = "/use Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany;\r?\n/";
            if (preg_match($importPattern, $newContent)) {
                $newContent = preg_replace($importPattern, '', $newContent);
                $this->line('Removed HasMany import from App\Models\User');
            }

            file_put_contents($userPhp, $newContent);
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

    /**
     * Revert tests/Pest.php and clean up tests/Unit/UserTest.php.
     */
    private function cleanupTests(): void
    {
        // 1. Pest.php
        $pestPhp = base_path('tests/Pest.php');
        if (file_exists($pestPhp)) {
            $content = file_get_contents($pestPhp);
            $newContent = str_replace("->in('Feature', 'Unit')", "->in('Feature')", $content);
            if ($newContent !== $content) {
                file_put_contents($pestPhp, $newContent);
                $this->line('Reverted tests/Pest.php to only include Feature directory');
            }
        }

        // 2. UserTest.php
        $userTestPhp = base_path('tests/Unit/UserTest.php');
        if (file_exists($userTestPhp)) {
            $content = file_get_contents($userTestPhp);

            // Remove the Chirp import
            $content = preg_replace("/use App\\\\Models\\\\Chirp;\r?\n/", '', $content);

            // Remove the "a user has many chirps" test
            $testPattern = "/test\('a user has many chirps', function \(\) \{[\s\S]*?}\);\r?\n?\r?\n?/";
            $content = preg_replace($testPattern, '', $content);

            file_put_contents($userTestPhp, $content);
            $this->line('Removed Chirp test from tests/Unit/UserTest.php');
        }
    }
}
