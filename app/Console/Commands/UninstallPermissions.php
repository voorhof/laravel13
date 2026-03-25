<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('permission:uninstall')]
#[Description('Remove the Spatie Laravel Permission installation and its features from the project')]
class UninstallPermissions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting Permission uninstallation...');

        // 1. Delete Laravel Permission-related files
        $filesToDelete = [
            app_path('Models/Permission.php'),
            app_path('Models/Role.php'),
            app_path('Policies/RolePolicy.php'),
            app_path('Policies/UserPolicy.php'),
            config_path('permission.php'),
            base_path('database/factories/PermissionFactory.php'),
            base_path('database/factories/RoleFactory.php'),
            base_path('database/migrations/0001_01_01_000003_create_permission_tables.php'),
            base_path('database/seeders/PermissionSeeder.php'),
            base_path('tests/Unit/RoleTest.php'),
            base_path('tests/Unit/UserPolicyTest.php'),
        ];

        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
                $this->line("Deleted: $file");
            }
        }

        // 2. Remove references from project files
        $this->removeReferences();

        $this->info('Spatie Laravel Permission has been successfully uninstalled!');
        $this->warn('Please run "composer remove spatie/laravel-permission" to remove the package dependency.');
        $this->warn('You may also want to delete this command file: '.__FILE__);
    }

    /**
     * Remove references in User.php, AppServiceProvider.php, and composer.json.
     */
    private function removeReferences(): void
    {
        // 1. Remove from User.php
        $userPath = app_path('Models/User.php');
        if (file_exists($userPath)) {
            $userContent = file_get_contents($userPath);

            // Remove HasRoles trait import
            $userContent = preg_replace('/^use Spatie\\\\Permission\\\\Traits\\\\HasRoles;(\r?\n)/m', '', $userContent);

            // Remove HasRoles trait usage
            $userContent = preg_replace('/^ {4}use HasRoles;(\r?\n)/m', '', $userContent);

            file_put_contents($userPath, $userContent);
            $this->line('Removed HasRoles from User.php');
        }

        // 2. Remove from AppServiceProvider.php
        $providerPath = app_path('Providers/AppServiceProvider.php');
        if (file_exists($providerPath)) {
            $providerContent = file_get_contents($providerPath);

            // Remove Gate::before block
            $providerContent = preg_replace('/ {8}\/\/ Implicitly grant the "Super Admin" role all permissions[\s\S]*?}\);\r?\n/m', '', $providerContent);

            file_put_contents($providerPath, $providerContent);
            $this->line('Removed Super Admin gate from AppServiceProvider.php');
        }

        // 3. Remove from composer.json
        $composerPath = base_path('composer.json');
        if (file_exists($composerPath)) {
            $composerContent = file_get_contents($composerPath);
            $composer = json_decode($composerContent, true);

            if (isset($composer['require']['spatie/laravel-permission'])) {
                unset($composer['require']['spatie/laravel-permission']);

                file_put_contents(
                    $composerPath,
                    json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n",
                );

                $this->line('Removed spatie/laravel-permission from composer.json');
            }
        }
    }
}
