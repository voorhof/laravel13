<?php

use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
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

    $this->superAdminRole = Role::create(['name' => 'Super Admin']);
    $this->user = User::factory()->create();
    $this->user->assignRole($this->superAdminRole);

    Permission::create(['name' => 'read roles']);
    Permission::create(['name' => 'create roles']);
    Permission::create(['name' => 'update roles']);
    Permission::create(['name' => 'delete roles']);

    // Standard Super Admin doesn't need permissions due to Gate::before,
    // but we can give them just in case or for explicit checks.
    $this->superAdminRole->givePermissionTo(Permission::all());

    actingAs($this->user);
});

it('can list roles', function () {
    $roles = Role::factory()->count(5)->create();

    livewire(ListRoles::class)
        ->assertCanSeeTableRecords($roles)
        ->assertCountTableRecords(6); // 5 factory + 1 Super Admin
});

it('can create a role', function () {
    $permission = Permission::create(['name' => 'test permission']);

    livewire(CreateRole::class)
        ->fillForm([
            'name' => 'Manager',
            'permissions' => [$permission->id],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Role::class, [
        'name' => 'Manager',
    ]);

    $role = Role::where('name', 'Manager')->first();
    expect($role->permissions)->toHaveCount(1)
        ->and($role->hasPermissionTo('test permission'))->toBeTrue();
});

it('can edit a role', function () {
    $role = Role::create(['name' => 'Editor']);
    $permission = Permission::create(['name' => 'edit-sth']);

    livewire(EditRole::class, [
        'record' => $role->getKey(),
    ])
        ->fillForm([
            'name' => 'Senior Editor',
            'permissions' => [$permission->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($role->refresh())
        ->name->toBe('Senior Editor')
        ->permissions->toHaveCount(1);
});

it('can delete a role', function () {
    $role = Role::create(['name' => 'Temporary Role']);

    livewire(EditRole::class, [
        'record' => $role->getKey(),
    ])
        ->callAction('delete');

    $this->assertDatabaseMissing(Role::class, [
        'id' => $role->id,
    ]);
});

it('can search roles by name', function () {
    Role::create(['name' => 'UniqueRoleName']);
    $otherRole = Role::create(['name' => 'Something Else']);

    livewire(ListRoles::class)
        ->searchTable('UniqueRoleName')
        ->assertCanSeeTableRecords(Role::where('name', 'UniqueRoleName')->get())
        ->assertCanNotSeeTableRecords([$otherRole]);
});

it('validates role name is unique', function () {
    Role::create(['name' => 'Existing Role']);

    livewire(CreateRole::class)
        ->fillForm([
            'name' => 'Existing Role',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('forbids access to Super Admin role in edit page', function () {
    // RolePolicy forbids viewing/updating Super Admin
    $superAdmin = Role::where('name', 'Super Admin')->first();

    // Create another user that is NOT Super Admin but has permissions to update roles
    $otherUser = User::factory()->create();
    // Permissions are created in beforeEach, but let's make sure 'update roles' exists
    $updatePermission = Permission::where('name', 'update roles')->first() ?? Permission::create(['name' => 'update roles']);
    $otherUser->givePermissionTo($updatePermission);

    actingAs($otherUser);

    get(EditRole::getUrl(['record' => $superAdmin->getKey()]))
        ->assertForbidden();
});

it('allows Admin role to be updated by Super Admin', function () {
    $adminRole = Role::create(['name' => 'Admin']);

    livewire(EditRole::class, [
        'record' => $adminRole->getKey(),
    ])
        ->assertSuccessful()
        ->fillForm(['name' => 'Admin']) // Keep name same
        ->call('save')
        ->assertHasNoFormErrors();
});
