<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Exceptions\RoleAlreadyExists;

uses(RefreshDatabase::class);

test('a role can be created', function () {
    $role = Role::create(['name' => 'admin']);

    expect($role->name)->toBe('admin')
        ->and($role->guard_name)->toBe('web');
});

test('a role can be mass assigned name and guard_name', function () {
    $role = new Role([
        'name' => 'editor',
        'guard_name' => 'api',
    ]);

    expect($role->name)->toBe('editor')
        ->and($role->guard_name)->toBe('api');
});

test('a permission can be created', function () {
    $permission = Permission::create(['name' => 'edit articles']);

    expect($permission->name)->toBe('edit articles')
        ->and($permission->guard_name)->toBe('web');
});

test('a permission can be assigned to a role', function () {
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => 'edit articles']);

    $role->givePermissionTo($permission);

    expect($role->hasPermissionTo('edit articles'))->toBeTrue();
});

test('a role can have multiple permissions', function () {
    $role = Role::create(['name' => 'admin']);
    $permission1 = Permission::create(['name' => 'edit articles']);
    $permission2 = Permission::create(['name' => 'delete articles']);

    $role->givePermissionTo($permission1, $permission2);

    expect($role->permissions)->toHaveCount(2)
        ->and($role->hasPermissionTo('edit articles'))->toBeTrue()
        ->and($role->hasPermissionTo('delete articles'))->toBeTrue();
});

test('a permission can belong to multiple roles', function () {
    $role1 = Role::create(['name' => 'admin']);
    $role2 = Role::create(['name' => 'editor']);
    $permission = Permission::create(['name' => 'edit articles']);

    $role1->givePermissionTo($permission);
    $role2->givePermissionTo($permission);

    expect($permission->roles)->toHaveCount(2)
        ->and($permission->roles->contains($role1))->toBeTrue()
        ->and($permission->roles->contains($role2))->toBeTrue();
});

test('a role name must be unique for a given guard', function () {
    Role::create(['name' => 'admin', 'guard_name' => 'web']);

    expect(fn () => Role::create(['name' => 'admin', 'guard_name' => 'web']))
        ->toThrow(RoleAlreadyExists::class);
});

test('a role can be created with a different guard and same name', function () {
    $role1 = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $role2 = Role::create(['name' => 'admin', 'guard_name' => 'api']);

    expect($role1->name)->toBe('admin')
        ->and($role1->guard_name)->toBe('web')
        ->and($role2->name)->toBe('admin')
        ->and($role2->guard_name)->toBe('api');
});

test('a permission can be revoked from a role', function () {
    $role = Role::create(['name' => 'admin']);
    $permission = Permission::create(['name' => 'edit articles']);

    $role->givePermissionTo($permission);
    expect($role->hasPermissionTo('edit articles'))->toBeTrue();

    $role->revokePermissionTo($permission);
    expect($role->hasPermissionTo('edit articles'))->toBeFalse();
});

test('role and permission factories work', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();

    $role->givePermissionTo($permission);

    expect($role->name)->not->toBeEmpty()
        ->and($permission->name)->not->toBeEmpty()
        ->and($role->hasPermissionTo($permission->name))->toBeTrue();
});

test('a role can be assigned to a user', function () {
    $role = Role::create(['name' => 'admin']);
    $user = App\Models\User::factory()->create();

    $user->assignRole($role);

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($role->users)->toHaveCount(1)
        ->and($role->users->first()->id)->toBe($user->id);
});
