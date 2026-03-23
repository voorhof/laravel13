<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// -----------------------------------------------------------------------
// Registration
// -----------------------------------------------------------------------

it('shows the registration page for guests', function () {
    $this->get(route('chirps.register'))
        ->assertSuccessful();
});

it('redirects authenticated users away from the registration page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('chirps.register'))
        ->assertRedirect('/');
});

it('registers a new user and redirects to the chirps feed', function () {
    $this->post(route('chirps.register.post'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirect(route('chirps.index'));

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('fails registration when name is missing', function () {
    $this->post(route('chirps.register.post'), [
        'name' => '',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('name');
});

it('fails registration when email is invalid', function () {
    $this->post(route('chirps.register.post'), [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

it('fails registration when name is too long', function () {
    $this->post(route('chirps.register.post'), [
        'name' => str_repeat('a', 256),
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('name');
});

it('fails registration when email is already taken', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post(route('chirps.register.post'), [
        'name' => 'Test User',
        'email' => 'taken@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

it('fails registration when password is too short', function () {
    $this->post(route('chirps.register.post'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');
});

it('fails registration when passwords do not match', function () {
    $this->post(route('chirps.register.post'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors('password');
});

// -----------------------------------------------------------------------
// Login
// -----------------------------------------------------------------------

it('shows the login page for guests', function () {
    $this->get(route('chirps.login'))
        ->assertSuccessful();
});

it('redirects authenticated users away from the login page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('chirps.login'))
        ->assertRedirect('/');
});

it('logs in a user with valid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $this->post(route('chirps.login.post'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('chirps.index'));

    $this->assertAuthenticatedAs($user);
});

it('fails login with wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $this->post(route('chirps.login.post'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('fails login when user does not exist', function () {
    $this->post(route('chirps.login.post'), [
        'email' => 'not@exists.com',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('fails login when email is missing', function () {
    $this->post(route('chirps.login.post'), [
        'email' => '',
        'password' => 'password',
    ])->assertSessionHasErrors('email');
});

it('fails login when password is missing', function () {
    $this->post(route('chirps.login.post'), [
        'email' => 'test@example.com',
        'password' => '',
    ])->assertSessionHasErrors('password');
});

// -----------------------------------------------------------------------
// Logout
// -----------------------------------------------------------------------

it('logs out an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('chirps.logout'))
        ->assertRedirect(route('chirps.index'));

    $this->assertGuest();
});

it('redirects guests away from the logout route', function () {
    $this->post(route('chirps.logout'))
        ->assertRedirect(route('chirps.login'));
});
