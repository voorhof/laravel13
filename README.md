<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Laravel 13 - Starter template

A go-to starter kit for Laravel 13 projects. This includes **Spatie Permission**, a **Filament** admin panel, **Chirper** example from the Laravel Bootcamp,...

All these pre-installed features have default settings, and each component can be removed with extra provided artisan commands.  
If you wish to remove a component, please do so as early as possible in the development process.

- `php artisan chirper:uninstall`
- `php artisan filament:uninstall`
- `php artisan permission:uninstall`

## Requirements

- **PHP** 8.3+
- **Composer**
- **Node.js** & **NPM**
- **[Laravel Herd](https://herd.laravel.com)** (Recommended)

## Installation

To get started with this starter template, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/voorhof/laravel13.git
   cd laravel13
   ```

2. **Run the setup command:**
   You can use the built-in setup script to handle the initial configuration:
   ```bash
   composer run setup
   ```
   This script will:
    - Install PHP dependencies.
    - Create your `.env` file from `.env.example`. _(default values have been commented out)_
    - Generate an application key.
    - Run database migrations (using SQLite by default).
    - Install NPM dependencies.
    - Build frontend assets.

3. **(Optional) Seed the database:**
   To create a default test user (`test@example.com` / `password`):
   ```bash
   php artisan db:seed
   ```

4. **Serve the application:**
   Since this project is optimized for **Laravel Herd**, it is automatically available at:
   `https://laravel13.test`

   If you're not using Herd, you can use the development command:
   ```bash
   composer run dev
   ```
   This will concurrently start the PHP server, the queue listener, and the Vite development server.

## User model

The User model was given the SoftDeletes trait and the deleted_at timestamp added to its migration file.

## Roles

The application uses the [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v7/introduction) package to manage user roles and permissions. The following roles are defined:

- **Super Admin**: Has all permissions, defined by a Gate::before inside the AppServiceProvider.
- **Admin**: Full crud permissions for users and roles.
- **Subscriber**: No permissions.

Seeding these roles and permissions is optional. You can run the seeder to populate the database with the default roles and permissions.

- `php artisan db:seed --class=PermissionSeeder`
- `php artisan migrate:fresh --seed --seeder=PermissionSeeder`

If you wish not to use this package, run the following command to remove it:

```bash
php artisan permission:uninstall
```

## Filament

[Filament](https://filamentphp.com/) is installed, and a basic admin panel configured, available at /admin URL.

For more information on the implementation, and optional removal instructions, please read documentation inside [FILAMENT.md](FILAMENT.md).


## Chirper

This project includes the "Getting Started with Laravel" bootcamp course from [Laravel Learn](https://laravel.com/learn/).

Chirper is a simple microblogging application built as part of the Laravel Bootcamp. It is an example implementation with features like soft deletes, named routes, and custom folder structures to keep it separate from the main application.

For more information on the implementation, database seeding, and removal instructions, please refer to the [CHIRPER.md](CHIRPER.md) file.

## AI

**Laravel Boost** is included, and this line is added inside post-update-cm to composer.json:   
`@php artisan boost:update --ansi`  
This will regenerate the Laravel Boost resources.  
https://laravel.com/docs/13.x/boost#keeping-boost-resources-updated

## Testing

This project uses [Pest PHP](https://pestphp.com) for testing. To run the full test suite, use:

```bash
composer run test
```

Or run tests directly with Artisan:

```bash
php artisan test
```

## License

The Laravel 13 Starter template is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
