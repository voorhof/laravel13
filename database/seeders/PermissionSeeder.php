<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'read users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'read roles']);
        Permission::create(['name' => 'update roles']);
        Permission::create(['name' => 'delete roles']);

        Permission::create(['name' => 'access dashboard']);

        // update the cache to know about the newly created permissions (required if using WithoutModelEvents in seeders)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create roles and assign created permissions
        Role::create(['name' => 'Subscriber']); // No default permissions for this role
        Role::create(['name' => 'Super Admin']); // Super Admin gets all permissions because the Gate::before() function in AppServiceProvider.php

        Role::create(['name' => 'Admin'])
            ->givePermissionTo([
                'access dashboard',
                'create users',
                'read users',
                'update users',
                'delete users',
                'create roles',
                'read roles',
                'update roles',
                'delete roles',
            ]);

        // Create admin user
        $admin = User::create([
            'name' => config('voorhof.admin.name'),
            'email' => config('voorhof.admin.email'),
            'password' => Hash::make(config('voorhof.admin.password')),
        ]);
        $admin->email_verified_at = now();
        $admin->save();
        $admin->assignRole('Admin');
    }
}
