# Chirper

Chirper is a simple microblogging application built as part of the Laravel Bootcamp.

## Resources

- **Laravel Bootcamp Course:** [Getting Started with Laravel](https://laravel.com/learn/getting-started-with-laravel)
- **Original GitHub Repository:** [laravel/bootcamp](https://github.com/laravel/bootcamp)

## Implementation Details

The implementation is slightly different from the bootcamp course to keep everything separate from the main app:

- Folder structure
- Chirp model uses soft deletes
- `$fillable` Model attributes instead of method
- Named routes
- Other new Laravel 13 features

## Database Seeding

Run Chirper database seed, with or without a database refresh:

- `php artisan db:seed --class=ChirpSeeder`
- `php artisan migrate:fresh --seed --seeder=ChirpSeeder`

## Truncate Database

Artisan command **DeleteAllChirps.php** is provided to truncate the Chirp table inside the database:

```bash
php artisan chirper:delete-chirps
```

## Deleting Chirper

Chirper as a whole is just an example, so Chirper files can be deleted safely.  
This is preferably done in the beginning of project setup.

### Artisan Uninstall Command

You can use the provided Artisan command **UninstallChirper.php** to uninstall Chirper:

```bash
php artisan chirper:uninstall
```
This will delete all Chirper-related files and folders. After deletion, you can manually remove the UninstallChirper.php command.

### Manual Deletion

Using the artisan command above is the recommended way to delete Chirper.  
If you want to do it manually, the following files and folders can be safely deleted:

#### APP
- **Console Commands:**
  - `app/Console/Commands/DeleteAllChirps.php`
  - `app/Console/Commands/UninstallChirper.php`
- **Controllers:**
  - `app/Http/Controllers/ChirpAuthController.php`
  - `app/Http/Controllers/ChirpController.php`
- **Middleware:** `app/Http/Middleware/ChirpAuthMiddleware.php`
- **Model:** `app/Models/Chirp.php`
- **Policy:** `app/Policies/ChirpPolicy.php`

#### DATABASE
- **Factory:** `database/factories/ChirpFactory.php`
- **Migration:** `database/migrations/0001_01_01_000009_create_chirps_table.php`
- **Seeder:** `database/seeders/ChirpSeeder.php`

#### PUBLIC
- **Assets:** `public/chirper` folder

#### RESOURCES
- **CSS:** `resources/css/chirper.css`
- **Views:** `resources/views/chirper` root folder and `resources/views/components/chirper` folder.

#### ROUTES
- **Routes:** `routes/chirper.php`

#### TESTS
- **Feature Tests:** `tests/Feature/Chirper/*`
- **Unit Tests:** `tests/Unit/ChirpTest.php` and `tests/Unit/ChirpPolicyTest.php`

### Post-Deletion Cleanup

When deleting Chirper, then also:

- Remove the `chirps()` `HasMany` relationship method inside the `User` model (+ import statement)
- Remove the Chirper-related tests and import from `tests/Unit/UserTest.php`
- Revert `tests/Pest.php` to only include the `Feature` directory: `->in('Feature')`
- Remove the `chirper.php` routes included inside `web.php`
- Remove its input inside `vite.config.js`
