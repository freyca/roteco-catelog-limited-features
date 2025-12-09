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
        'name' => 'Test',
        'surname' => 'User',
    ]);
    test()->actingAs(test()->user);
});

it('validates required fields on create', function () {
    $user = test()->user;
    test()->actingAs($user);
    $component = Livewire::test(CreateAddress::class);
    $component->set('data.name', '');
    $component->set('data.surname', '');
    $component->set('data.address', '');
    $component->set('data.city', '');
    $component->set('data.state', '');
    $component->set('data.zip_code', '');
    $component->set('data.country', '');
    $component->set('data.phone', '');
    $component->set('data.address_type', '');
    $component->set('data.email', '');
    $component->call('create');
    $component->assertHasFormErrors([
        'name' => 'required',
        'surname' => 'required',
        'address' => 'required',
        'city' => 'required',
        'state' => 'required',
        'zip_code' => 'required',
        'country' => 'required',
        'phone' => 'required',
        'address_type' => 'required',
        'email' => 'required',
    ]);
});

it('can create address with livewire form with all fields', function () {
    $user = test()->user;
    test()->actingAs($user);

    $component = Livewire::test(CreateAddress::class);
    $component->set('data.name', 'John Doe');
    $component->set('data.surname', 'Smith');
    $component->set('data.address_type', 'shipping');
    $component->set('data.bussiness_name', 'ACME Corp');
    $component->set('data.financial_number', '12345678A');
    $component->set('data.phone', '+34912345678');
    $component->set('data.email', 'john@example.com');
    $component->set('data.address', '123 Main Street');
    $component->set('data.city', 'Madrid');
    $component->set('data.state', 'Madrid');
    $component->set('data.zip_code', '28001');
    $component->set('data.country', 'Spain');
    $component->call('create');
    $component->assertHasNoFormErrors();

    expect(Address::count())->toBe(1);
    $address = Address::first();
    expect($address->name)->toBe('John Doe');
    expect($address->surname)->toBe('Smith');
    expect($address->user_id)->toBe($user->id);
    expect($address->email)->toBe('john@example.com');
    expect($address->bussiness_name)->toBe('ACME Corp');
});

it('can create address with optional fields empty', function () {
    $user = test()->user;
    test()->actingAs($user);

    $component = Livewire::test(CreateAddress::class);
    $component->set('data.name', 'Jane Doe');
    $component->set('data.surname', 'Johnson');
    $component->set('data.address_type', 'billing');
    $component->set('data.phone', '+34987654321');
    $component->set('data.email', 'jane@example.com');
    $component->set('data.address', '456 Oak Avenue');
    $component->set('data.city', 'Barcelona');
    $component->set('data.state', 'Catalonia');
    $component->set('data.zip_code', 8002);
    $component->set('data.country', 'Spain');
    $component->call('create');
    $component->assertHasNoFormErrors();

    expect(Address::count())->toBe(1);
    $address = Address::first();
    expect($address->bussiness_name)->toBeNull();
    expect($address->financial_number)->toBeNull();
});

it('can edit address through livewire form', function () {
    $user = test()->user;
    test()->actingAs($user);

    $address = Address::factory()->for($user)->create([
        'city' => 'Madrid',
        'address' => 'Old Street 1',
    ]);

    $component = Livewire::test(EditAddress::class, ['record' => $address->id]);
    $component->set('data.city', 'Barcelona');
    $component->set('data.address', 'New Street 2');
    $component->call('save');
    $component->assertHasNoFormErrors();

    expect($address->fresh()->city)->toBe('Barcelona');
    expect($address->fresh()->address)->toBe('New Street 2');
});

it('can delete an address', function () {
    $user = test()->user;
    $address = Address::factory()->for($user)->create();
    test()->actingAs($user);
    $address->delete();
    expect($address->fresh()->trashed())->toBeTrue();
});

it('user_cannot_access_another_users_address', function () {
    $user = test()->user;
    $otherUser = User::factory()->create();
    $myAddress = Address::factory()->for($user)->create(['address' => 'User Own Address']);
    test()->actingAs($otherUser);
    $otherAddress = Address::factory()->for($otherUser)->create(['address' => 'Other User Address']);
    test()->actingAs($user);

    expect($myAddress->user_id)->toBe($user->id);
    expect($otherAddress->user_id)->toBe($otherUser->id);

    $userAddresses = Address::query()->get();
    expect($userAddresses)->toHaveCount(1);
    expect($userAddresses->first()->id)->toBe($myAddress->id);

    expect(fn() => Livewire::test(EditAddress::class, ['record' => $otherAddress->id]))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});
