<?php

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a chirp can be mass assigned message', function () {
    $chirp = new Chirp(['message' => 'Test message']);

    expect($chirp->message)->toBe('Test message');
});

test('a chirp belongs to a user', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    expect($chirp->user)->toBeInstanceOf(User::class)
        ->and($chirp->user->id)->toBe($user->id);
});
