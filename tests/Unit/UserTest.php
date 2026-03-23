<?php

use App\Models\Chirp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can be mass assigned name and email', function () {
    $user = new User([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    expect($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com');
});

test('a user has many chirps', function () {
    $user = User::factory()->create();
    Chirp::factory()->count(3)->for($user)->create();

    expect($user->chirps)->toHaveCount(3)
        ->and($user->chirps->first())->toBeInstanceOf(Chirp::class);
});
