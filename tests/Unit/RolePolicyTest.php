<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new RolePolicy;
});

test('viewAny allows users with read roles permission', function () {
    Permission::create(['name' => 'read roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('read roles');

    expect($this->policy->viewAny($user))->toBeTrue();
});

test('viewAny forbids users without read roles permission', function () {
    $user = User::factory()->create();

    expect($this->policy->viewAny($user))->toBeFalse();
});

test('view forbids access to Super Admin role', function () {
    Permission::create(['name' => 'read roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('read roles');
    $role = Role::create(['name' => 'Super Admin']);

    expect($this->policy->view($user, $role))->toBeFalse();
});

test('view allows users with read roles permission for other roles', function () {
    Permission::create(['name' => 'read roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('read roles');
    $role = Role::factory()->create(['name' => 'Editor']);

    expect($this->policy->view($user, $role))->toBeTrue();
});

test('view forbids users without read roles permission', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    expect($this->policy->view($user, $role))->toBeFalse();
});

test('create allows users with create roles permission', function () {
    Permission::create(['name' => 'create roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('create roles');

    expect($this->policy->create($user))->toBeTrue();
});

test('create forbids users without create roles permission', function () {
    $user = User::factory()->create();

    expect($this->policy->create($user))->toBeFalse();
});

test('update forbids access to Super Admin role', function () {
    Permission::create(['name' => 'update roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('update roles');
    $role = Role::create(['name' => 'Super Admin']);

    expect($this->policy->update($user, $role))->toBeFalse();
});

test('update allows Super Admin or Admin to update Admin role', function () {
    $superAdminRole = Role::create(['name' => 'Super Admin']);
    $adminRole = Role::create(['name' => 'Admin']);

    $user1 = User::factory()->create();
    $user1->assignRole($superAdminRole);

    $user2 = User::factory()->create();
    $user2->assignRole($adminRole);

    expect($this->policy->update($user1, $adminRole))->toBeTrue()
        ->and($this->policy->update($user2, $adminRole))->toBeTrue();
});

test('update forbids others from updating Admin role', function () {
    Permission::create(['name' => 'update roles']);
    $adminRole = Role::create(['name' => 'Admin']);
    $user = User::factory()->create();
    $user->givePermissionTo('update roles');

    expect($this->policy->update($user, $adminRole))->toBeFalse();
});

test('update allows users with update roles permission for other roles', function () {
    Permission::create(['name' => 'update roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('update roles');
    $role = Role::factory()->create(['name' => 'Editor']);

    expect($this->policy->update($user, $role))->toBeTrue();
});

test('update forbids users without update roles permission', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    expect($this->policy->update($user, $role))->toBeFalse();
});

test('delete forbids access to Super Admin role', function () {
    Permission::create(['name' => 'delete roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('delete roles');
    $role = Role::create(['name' => 'Super Admin']);

    expect($this->policy->delete($user, $role))->toBeFalse();
});

test('delete allows Super Admin or Admin to delete Admin role', function () {
    $superAdminRole = Role::create(['name' => 'Super Admin']);
    $adminRole = Role::create(['name' => 'Admin']);

    $user1 = User::factory()->create();
    $user1->assignRole($superAdminRole);

    $user2 = User::factory()->create();
    $user2->assignRole($adminRole);

    expect($this->policy->delete($user1, $adminRole))->toBeTrue()
        ->and($this->policy->delete($user2, $adminRole))->toBeTrue();
});

test('deleting forbids others from deleting Admin role', function () {
    Permission::create(['name' => 'delete roles']);
    $adminRole = Role::create(['name' => 'Admin']);
    $user = User::factory()->create();
    $user->givePermissionTo('delete roles');

    expect($this->policy->delete($user, $adminRole))->toBeFalse();
});

test('delete allows users with delete roles permission for other roles', function () {
    Permission::create(['name' => 'delete roles']);
    $user = User::factory()->create();
    $user->givePermissionTo('delete roles');
    $role = Role::factory()->create(['name' => 'Editor']);

    expect($this->policy->delete($user, $role))->toBeTrue();
});

test('delete forbids users without delete roles permission', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();

    expect($this->policy->delete($user, $role))->toBeFalse();
});
