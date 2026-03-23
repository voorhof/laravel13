<?php

use App\Models\Chirp;
use App\Models\User;
use App\Policies\ChirpPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new ChirpPolicy;
});

test('viewAny allows everyone', function () {
    expect($this->policy->viewAny(null))->toBeTrue()
        ->and($this->policy->viewAny(User::factory()->create()))->toBeTrue();
});

test('view allows everyone', function () {
    $chirp = Chirp::factory()->for(User::factory())->create();
    expect($this->policy->view(null, $chirp))->toBeTrue()
        ->and($this->policy->view(User::factory()->create(), $chirp))->toBeTrue();
});

test('create allows authenticated users', function () {
    $user = User::factory()->create();
    expect($this->policy->create($user))->toBeTrue();
});

test('update allows the owner', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    expect($this->policy->update($user, $chirp))->toBeTrue();
});

test('update forbids non-owners', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for(User::factory())->create();

    expect($this->policy->update($user, $chirp))->toBeFalse();
});

test('delete allows the owner', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    expect($this->policy->delete($user, $chirp))->toBeTrue();
});

test('delete forbids non-owners', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for(User::factory())->create();

    expect($this->policy->delete($user, $chirp))->toBeFalse();
});

test('restore is not allowed', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    expect($this->policy->restore($user, $chirp))->toBeFalse();
});

test('forceDelete is not allowed', function () {
    $user = User::factory()->create();
    $chirp = Chirp::factory()->for($user)->create();

    expect($this->policy->forceDelete($user, $chirp))->toBeFalse();
});
