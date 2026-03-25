<?php

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::bootCurrentPanel();

    Role::create(['name' => 'Super Admin']);
    Permission::create(['name' => 'view posts']);
    Permission::create(['name' => 'edit posts']);
    Permission::create(['name' => 'delete posts']);
});

it('syncs all permissions to Admin role when updated', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $role = Role::create(['name' => 'Admin']);

    // Initially give it no permissions (or just one)
    $role->syncPermissions(['view posts']);
    expect($role->permissions)->toHaveCount(1);

    livewire(EditRole::class, [
        'record' => $role->getKey(),
    ])
        ->fillForm([
            'name' => 'Admin',
            'permissions' => [], // Empty selection in UI
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($role->refresh()->permissions)->toHaveCount(3)
        ->and($role->hasPermissionTo('view posts'))->toBeTrue()
        ->and($role->hasPermissionTo('edit posts'))->toBeTrue()
        ->and($role->hasPermissionTo('delete posts'))->toBeTrue();
});

it('syncs all permissions to Admin role when created', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    livewire(CreateRole::class)
        ->fillForm([
            'name' => 'Admin',
            'permissions' => [], // Empty selection in UI
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $role = Role::where('name', 'Admin')->first();
    expect($role->permissions)->toHaveCount(3);
});

it('allows Admin users to perform actions because they have all permissions', function () {
    $adminRole = Role::create(['name' => 'Admin']);
    // The sync logic is in Filament pages, so manual creation doesn't sync unless we use the Filament page or trigger it.
    // But here we want to test that if they HAVE the permissions, they can do things even without Gate::before.
    $adminRole->syncPermissions(Permission::all());

    $user = User::factory()->create();
    $user->assignRole('Admin');

    actingAs($user);

    // Try to access a page that requires a permission, e.g., the roles index.
    // Assuming RolePolicy checks for 'read roles' permission.
    Permission::create(['name' => 'read roles']);
    $adminRole->givePermissionTo('read roles');

    // We need to make sure the user has the permission.
    expect($user->can('read roles'))->toBeTrue();

    get('/admin/roles')
        ->assertSuccessful();
});

it('does not sync all permissions to non-Admin roles', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $viewPermission = Permission::where('name', 'view posts')->first();

    livewire(CreateRole::class)
        ->fillForm([
            'name' => 'Editor',
            'permissions' => [$viewPermission->id],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $role = Role::where('name', 'Editor')->first();
    expect($role->permissions)->toHaveCount(1);
});
