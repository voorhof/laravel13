<?php

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// -----------------------------------------------------------------------
// Index
// -----------------------------------------------------------------------

it('shows the chirps feed to guests', function () {
    $this->get(route('chirps.index'))
        ->assertSuccessful();
});

it('shows the chirps feed to authenticated users', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('chirps.index'))
        ->assertSuccessful();
});

it('displays chirps on the feed', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->get(route('chirps.index'))
        ->assertSuccessful()
        ->assertSee($chirp->message);
});

// -----------------------------------------------------------------------
// Store
// -----------------------------------------------------------------------

it('allows authenticated users to create a chirp', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('chirps.store'), ['message' => 'Hello Chirper!'])
        ->assertRedirect(route('chirps.index'));

    $this->assertDatabaseHas('chirps', [
        'user_id' => $user->id,
        'message' => 'Hello Chirper!',
    ]);
});

it('redirects guests to login when trying to create a chirp', function () {
    $this->post(route('chirps.store'), ['message' => 'Hello Chirper!'])
        ->assertRedirect(route('chirps.login'));
});

it('fails to store a chirp when message is missing', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('chirps.store'), ['message' => ''])
        ->assertSessionHasErrors('message');
});

it('fails to store a chirp when message exceeds 255 characters', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('chirps.store'), ['message' => str_repeat('a', 256)])
        ->assertSessionHasErrors('message');
});

it('stores a chirp when message is exactly 255 characters', function () {
    $user = User::factory()->create();
    $message = str_repeat('a', 255);

    $this->actingAs($user)
        ->post(route('chirps.store'), ['message' => $message])
        ->assertRedirect(route('chirps.index'));

    $this->assertDatabaseHas('chirps', ['user_id' => $user->id, 'message' => $message]);
});

it('updates a chirp when message is exactly 255 characters', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();
    $message = str_repeat('b', 255);

    $this->actingAs($user)
        ->patch(route('chirps.update', $chirp), ['message' => $message])
        ->assertRedirect(route('chirps.index'));

    $this->assertDatabaseHas('chirps', ['id' => $chirp->id, 'message' => $message]);
});

// -----------------------------------------------------------------------
// Show
// -----------------------------------------------------------------------

it('redirects to the feed when viewing a chirp as a guest', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->get(route('chirps.show', $chirp))
        ->assertRedirect(route('chirps.login'));
});

it('redirects to the feed when viewing a chirp as an authenticated user', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs(User::factory()->create())
        ->get(route('chirps.show', $chirp))
        ->assertRedirect(route('chirps.index'));
});

// -----------------------------------------------------------------------
// Edit
// -----------------------------------------------------------------------

it('shows the edit form to the chirp owner', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('chirps.edit', $chirp))
        ->assertSuccessful()
        ->assertSee($chirp->message);
});

it('forbids a non-owner from accessing the edit form', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs(User::factory()->create())
        ->get(route('chirps.edit', $chirp))
        ->assertForbidden();
});

it('redirects guests to login when accessing the edit form', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->get(route('chirps.edit', $chirp))
        ->assertRedirect(route('chirps.login'));
});

// -----------------------------------------------------------------------
// Update
// -----------------------------------------------------------------------

it('allows the owner to update their chirp', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    $this->actingAs($user)
        ->patch(route('chirps.update', $chirp), ['message' => 'Updated message'])
        ->assertRedirect(route('chirps.index'));

    $this->assertDatabaseHas('chirps', ['id' => $chirp->id, 'message' => 'Updated message']);
});

it('forbids a non-owner from updating a chirp', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs(User::factory()->create())
        ->patch(route('chirps.update', $chirp), ['message' => 'Hacked!'])
        ->assertForbidden();
});

it('redirects guests to login when trying to update a chirp', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->patch(route('chirps.update', $chirp), ['message' => 'Updated message'])
        ->assertRedirect(route('chirps.login'));
});

it('fails to update a chirp when message is missing', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    $this->actingAs($user)
        ->patch(route('chirps.update', $chirp), ['message' => ''])
        ->assertSessionHasErrors('message');
});

it('fails to update a chirp when message exceeds 255 characters', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    $this->actingAs($user)
        ->patch(route('chirps.update', $chirp), ['message' => str_repeat('a', 256)])
        ->assertSessionHasErrors('message');
});

// -----------------------------------------------------------------------
// Destroy
// -----------------------------------------------------------------------

it('allows the owner to delete their chirp', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('chirps.destroy', $chirp))
        ->assertRedirect(route('chirps.index'));

    $this->assertSoftDeleted('chirps', ['id' => $chirp->id]);
});

it('forbids a non-owner from deleting a chirp', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('chirps.destroy', $chirp))
        ->assertForbidden();
});

it('redirects guests to login when trying to delete a chirp', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();

    $this->delete(route('chirps.destroy', $chirp))
        ->assertRedirect(route('chirps.login'));
});

it('soft deletes the chirp and keeps it in the database', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('chirps.destroy', $chirp));

    $this->assertSoftDeleted('chirps', ['id' => $chirp->id]);
    $this->assertDatabaseHas('chirps', ['id' => $chirp->id]);
});
