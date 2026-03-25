<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::bootCurrentPanel();

    // Create roles and permissions
    Role::firstOrCreate(['name' => 'Super Admin']);
    Permission::firstOrCreate(['name' => 'read users']);
    Permission::firstOrCreate(['name' => 'create users']);
    Permission::firstOrCreate(['name' => 'update users']);
    Permission::firstOrCreate(['name' => 'delete users']);
});

it('redirects guest users to the login page', function () {
    get('/admin')
        ->assertRedirect('/admin/login');
});

it('can access the admin dashboard when authenticated', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    actingAs($user)
        ->get('/admin')
        ->assertSuccessful()
        ->assertSee('Dashboard');
});

it('can load the login page', function () {
    get('/admin/login')
        ->assertSuccessful()
        ->assertSee('Sign in');
});

it('can login through the admin panel login page', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('Super Admin');

    livewire(Login::class)
        ->fillForm([
            'email' => $user->email,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect('/admin');

    assertAuthenticated();
});

it('can logout of the admin panel', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/admin/logout')
        ->assertRedirect('/admin/login');

    assertGuest();
});

it('can list users', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('read users');
    $users = User::factory()->count(5)->create();

    actingAs($user);

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertCountTableRecords(6);
});

it('can create a user', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $newUserData = User::factory()->make();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => $newUserData->name,
            'email' => $newUserData->email,
            'password' => 'password',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'name' => $newUserData->name,
        'email' => $newUserData->email,
    ]);
});

it('can edit a user', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $targetUser = User::factory()->create();
    $newName = 'Updated Name';

    livewire(EditUser::class, [
        'record' => $targetUser->getKey(),
    ])
        ->fillForm([
            'name' => $newName,
            'password' => 'new-password',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($targetUser->refresh()->name)->toBe($newName);
});

it('can delete a user', function () {
    $user = User::factory()->create();
    $user->assignRole('Super Admin');
    actingAs($user);

    $targetUser = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $targetUser->getKey(),
    ])
        ->callAction('delete');

    expect($targetUser->refresh()->deleted_at)->not->toBeNull();
});

it('can search for users in the table', function () {
    $user = User::factory()->create(['name' => 'Searchable User']);
    $user->givePermissionTo('read users');
    $otherUser = User::factory()->create(['name' => 'Other User']);

    actingAs($user);

    livewire(ListUsers::class)
        ->searchTable('Searchable User')
        ->assertCanSeeTableRecords([$user])
        ->assertCanNotSeeTableRecords([$otherUser]);
});
