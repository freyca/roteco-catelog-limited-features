<?php

use App\Filament\User\Resources\Addresses\Pages\CreateAddress;
use App\Filament\User\Resources\Addresses\Pages\EditAddress;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    test()->user = User::factory()->create([
        'email' => 'test@example.com',
    ]);
    test()->actingAs(test()->user);
});

it('validates required fields on create', function () {
    $user = test()->user;
    test()->actingAs($user);

    Livewire::test(CreateAddress::class)
        ->fillForm([])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'surname' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'address_type' => 'required',
        ]);
});

it('can delete an address', function () {
    $user = test()->user;
    $address = Address::factory()->for($user)->create();
    test()->actingAs($user);
    $address->delete();
    \Pest\Laravel\assertDatabaseMissing('addresses', ['id' => $address->id]);
});

it('user_cannot_access_another_users_address', function () {
    $user = test()->user;
    $otherUser = User::factory()->create();
    $myAddress = Address::factory()->for($user)->create(['address' => 'User Own Address']);
    // Create other user's address by logging in as that user, then back to current user
    test()->actingAs($otherUser);
    $otherAddress = Address::factory()->for($otherUser)->create(['address' => 'Other User Address']);
    test()->actingAs($user);

    // Verify address belongs to correct user
    expect($myAddress->user_id)->toBe($user->id);
    expect($otherAddress->user_id)->toBe($otherUser->id);

    // Test direct query with scope: only user's addresses should be visible
    $userAddresses = Address::query()->get();
    expect($userAddresses)->toHaveCount(1);
    expect($userAddresses->first()->id)->toBe($myAddress->id);

    // Edit: should not be able to access other's address
    expect(fn () => Livewire::test(EditAddress::class, ['record' => $otherAddress->id]))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
