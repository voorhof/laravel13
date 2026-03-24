<?php

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->policy = new UserPolicy;
});

test('viewAny allows everyone authenticated', function () {
    $user = User::factory()->create();
    expect($this->policy->viewAny($user))->toBeTrue();
});

test('view allows everyone authenticated', function () {
    $user = User::factory()->create();
    $model = User::factory()->create();
    expect($this->policy->view($user, $model))->toBeTrue();
});

test('create allows everyone authenticated', function () {
    $user = User::factory()->create();
    expect($this->policy->create($user))->toBeTrue();
});

test('update allows everyone authenticated', function () {
    $user = User::factory()->create();
    $model = User::factory()->create();
    expect($this->policy->update($user, $model))->toBeTrue();
});

test('delete allows everyone authenticated', function () {
    $user = User::factory()->create();
    $model = User::factory()->create();
    expect($this->policy->delete($user, $model))->toBeTrue();
});

test('restore allows everyone authenticated', function () {
    $user = User::factory()->create();
    $model = User::factory()->create();
    expect($this->policy->restore($user, $model))->toBeTrue();
});

test('forceDelete allows everyone authenticated', function () {
    $user = User::factory()->create();
    $model = User::factory()->create();
    expect($this->policy->forceDelete($user, $model))->toBeTrue();
});
