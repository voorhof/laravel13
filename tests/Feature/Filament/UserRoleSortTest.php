<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::bootCurrentPanel();
});

it('can sort users by role name', function () {
    $user = User::factory()->create(['name' => 'Admin User']);

    $roleA = Role::create(['name' => 'Alpha']);
    $roleZ = Role::create(['name' => 'Zeta']);

    $userA = User::factory()->create(['name' => 'User Alpha']);
    $userA->assignRole($roleA);

    $userZ = User::factory()->create(['name' => 'User Zeta']);
    $userZ->assignRole($roleZ);

    actingAs($user);

    // Sort ascending
    livewire(ListUsers::class)
        ->sortTable('roles.name', 'asc')
        ->assertCanSeeTableRecords([$user, $userA, $userZ], inOrder: true);

    // Sort descending
    livewire(ListUsers::class)
        ->sortTable('roles.name', 'desc')
        ->assertCanSeeTableRecords([$userZ, $userA, $user], inOrder: true);
});
