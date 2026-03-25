<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new UserPolicy;

    // Create standard roles
    Role::create(['name' => 'Super Admin']);
    Role::create(['name' => 'Admin']);

    // Create permissions
    Permission::create(['name' => 'read users']);
    Permission::create(['name' => 'create users']);
    Permission::create(['name' => 'update users']);
    Permission::create(['name' => 'delete users']);
});

test('viewAny allows users with read users permission', function () {
    $user = User::factory()->create();
    expect($this->policy->viewAny($user))->toBeFalse();

    $user->givePermissionTo('read users');
    expect($this->policy->viewAny($user))->toBeTrue();
});

test('view allows users with read users permission', function () {
    $user = User::factory()->create();
    $model = User::factory()->create();
    expect($this->policy->view($user, $model))->toBeFalse();

    $user->givePermissionTo('read users');
    expect($this->policy->view($user, $model))->toBeTrue();
});

test('create allows users with create users permission', function () {
    $user = User::factory()->create();
    expect($this->policy->create($user))->toBeFalse();

    $user->givePermissionTo('create users');
    expect($this->policy->create($user))->toBeTrue();
});

test('update follows hierarchical rules', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $admin->givePermissionTo('update users');

    $regularUser = User::factory()->create();
    $regularUser->givePermissionTo('update users');

    $otherUser = User::factory()->create();

    // Super Admin can update anyone
    expect($this->policy->update($superAdmin, $superAdmin))->toBeTrue();
    expect($this->policy->update($superAdmin, $admin))->toBeTrue();
    expect($this->policy->update($superAdmin, $regularUser))->toBeTrue();

    // Admin can update Admins and regular users, but not Super Admins
    expect($this->policy->update($admin, $superAdmin))->toBeFalse();
    expect($this->policy->update($admin, $admin))->toBeTrue();
    expect($this->policy->update($admin, $regularUser))->toBeTrue();

    // Regular user with permission can update others, but not Super Admin or Admin
    expect($this->policy->update($regularUser, $superAdmin))->toBeFalse();
    expect($this->policy->update($regularUser, $admin))->toBeFalse();
    expect($this->policy->update($regularUser, $otherUser))->toBeTrue();

    // User without permission cannot update anyone
    expect($this->policy->update($otherUser, $otherUser))->toBeFalse();
});

test('delete follows hierarchical rules', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('Super Admin');

    $admin = User::factory()->create();
    $admin->assignRole('Admin');
    $admin->givePermissionTo('delete users');

    $regularUser = User::factory()->create();
    $regularUser->givePermissionTo('delete users');

    $otherUser = User::factory()->create();

    // Super Admin can delete anyone
    expect($this->policy->delete($superAdmin, $superAdmin))->toBeTrue();
    expect($this->policy->delete($superAdmin, $admin))->toBeTrue();
    expect($this->policy->delete($superAdmin, $regularUser))->toBeTrue();

    // Admin can delete Admins and regular users, but not Super Admins
    expect($this->policy->delete($admin, $superAdmin))->toBeFalse();
    expect($this->policy->delete($admin, $admin))->toBeTrue();
    expect($this->policy->delete($admin, $regularUser))->toBeTrue();

    // Regular user with permission can delete others, but not Super Admin or Admin
    expect($this->policy->delete($regularUser, $superAdmin))->toBeFalse();
    expect($this->policy->delete($regularUser, $admin))->toBeFalse();
    expect($this->policy->delete($regularUser, $otherUser))->toBeTrue();

    // User without permission cannot delete anyone
    expect($this->policy->delete($otherUser, $otherUser))->toBeFalse();
});
