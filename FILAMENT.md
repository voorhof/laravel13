# Filament Implementation Tutorial

This tutorial provides an overview of the Filament implementation in this project, which includes a basic admin panel and a user management resource.

## Overview

Filament v5 has been installed to provide a powerful and customizable admin panel. The implementation focuses on clean code separation by moving form and table definitions into their own dedicated classes.

### Installation Details

The following commands were used to set up Filament:

```bash
composer require filament/filament:"~5.4"
php artisan filament:install --panels
php artisan vendor:publish --tag=filament-config
php artisan icons:cache
php artisan make:filament-resource User --generate --soft-deletes
```

You can find the config file at `config/filament.php`.

## Project Structure

We follow a structured approach for our resources to keep the main resource class clean and manageable.

```text
app/Filament/Resources/Users/
├── Pages/              # Specific pages for the resource (List, Create, Edit)
├── Schemas/            # Form definitions using Filament Schemas
│   └── UserForm.php    # Defines the fields for creating/editing users
├── Tables/             # Table definitions
│   └── UsersTable.php  # Defines columns, filters, and actions for the user list
└── UserResource.php    # Main resource class connecting everything
```

## Key Components

### 1. Admin Panel Provider
Located at `app/Providers/Filament/AdminPanelProvider.php`, this is the heart of the Filament configuration. It defines:
- The path to access the panel (`/admin`).
- Colors, navigation, and middleware.
- Automated discovery of resources, pages, and widgets.

### 2. User Resource
Located at `app/Filament/Resources/Users/UserResource.php`, this class defines the model (`User`) and how it's represented in the navigation.

In Filament v5, we use the `form(Schema $schema)` method to define our forms:

```php
public static function form(Schema $schema): Schema
{
    return UserForm::configure($schema);
}
```

### 3. User Form (Schema)
Located at `app/Filament/Resources/Users/Schemas/UserForm.php`. Instead of defining the form directly in the resource, we use a dedicated schema class. This makes it easier to reuse or modify the form logic.

### 4. Users Table
Located at `app/Filament/Resources/Users/Tables/UsersTable.php`. This class handles the table configuration, including:
- **Columns**: Searchable name and email, formatted dates.
- **Filters**: Filter for verified users and a `TrashedFilter` for soft deletes.
- **Actions**: Edit, Delete, Force Delete, and Restore.

## Working with Soft Deletes

Since the `User` resource was generated with `--soft-deletes`, it includes:
- `TrashedFilter` in the table.
- `SoftDeletingScope` handling in the `UserResource`.
- Restore and Force Delete actions in the table's bulk actions.

## Accessing the Admin Panel

The admin panel is available at:
[https://laravel13.test/admin](https://laravel13.test/admin)

(Note: Ensure you have a user with appropriate permissions or that the environment is set up for local development access.)

## Adding a New Resource

To add a new resource following this pattern:
1. Generate the resource: `php artisan make:filament-resource YourModel --generate`
2. Create `Schemas` and `Tables` directories within the new resource folder.
3. Move the form and table logic into dedicated classes (e.g., `YourModelForm` and `YourModelTable`).
4. Update `YourModelResource` to use these classes.

## Deleting Filament

Filament is a basic pre-installation with default settings. If you wish not to use it, Filament files can be deleted safely.  
This is preferably done in the beginning of project setup.

### Artisan Uninstall Command

You can use the provided Artisan command **UninstallFilament.php** to uninstall Filament:

```bash
php artisan filament:uninstall
```
This will delete all Filament-related files and folders. After deletion, you can manually remove the UninstallFilament.php command.
