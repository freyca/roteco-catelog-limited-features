<?php

use App\Enums\AddressType;
use App\Filament\Admin\Resources\Users\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Users\RelationManagers\AddressRelationManager;
use App\Models\Address;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    test()->admin = User::factory()->admin_notifiable()->create();
    test()->customer = User::factory()->customer()->create();
    test()->actingAs(test()->admin);
});

it('admin can create address through relation manager', function () {
    $customer = test()->customer;

    $component = Livewire::test(AddressRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(CreateAction::class);

    $component->set('mountedActions.0.data', [
        'name' => 'Shipping John',
        'surname' => 'Doe',
        'address_type' => AddressType::Shipping->value,
        'bussiness_name' => 'Business Inc',
        'financial_number' => 'B87654321',
        'phone' => '34911111111',
        'email' => 'shipping@example.com',
        'address' => '789 Admin Street',
        'city' => 'Valencia',
        'state' => 'Valencia',
        'zip_code' => 46001,
        'country' => 'Spain',
    ]);

    $component->callMountedTableAction();
    $component->assertHasNoTableActionErrors();

    expect(Address::count())->toBe(1);
    $address = Address::first();
    expect($address->user_id)->toBe($customer->id);
    expect($address->name)->toBe('Shipping John');
    expect($address->email)->toBe('shipping@example.com');
});

it('admin can edit address through relation manager', function () {
    $customer = test()->customer;
    $address = Address::factory()->for($customer)->create([
        'city' => 'Madrid',
        'address' => 'Old Street',
    ]);

    $component = Livewire::test(AddressRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(EditAction::class, $address);

    $component->set('mountedActions.0.data', [
        'name' => $address->name,
        'surname' => $address->surname,
        'address_type' => $address->address_type->value,
        'bussiness_name' => $address->bussiness_name,
        'financial_number' => $address->financial_number,
        'phone' => $address->phone,
        'email' => $address->email,
        'address' => 'New Street',
        'city' => 'Barcelona',
        'state' => $address->state,
        'zip_code' => $address->zip_code,
        'country' => $address->country,
    ]);

    $component->callMountedTableAction();
    $component->assertHasNoTableActionErrors();

    expect($address->fresh()->city)->toBe('Barcelona');
    expect($address->fresh()->address)->toBe('New Street');
});

it('admin can delete address through relation manager', function () {
    $customer = test()->customer;
    $address = Address::factory()->for($customer)->create();

    Livewire::test(AddressRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass' => EditUser::class,
    ])
        ->callTableAction(DeleteAction::class, $address)
        ->assertHasNoTableActionErrors();

    expect($address->fresh()->trashed())->toBeTrue();
});

it('validates required fields in create action', function () {
    $customer = test()->customer;

    $component = Livewire::test(AddressRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(CreateAction::class);

    $component->set('mountedActions.0.data', [
        'name' => '',
        'surname' => '',
        'address_type' => '',
        'bussiness_name' => '',
        'financial_number' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'country' => '',
    ]);

    $component->callMountedTableAction();
    $component->assertHasTableActionErrors([
        'name' => ['required'],
        'surname' => ['required'],
        'address_type' => ['required'],
        'phone' => ['required'],
        'email' => ['required'],
        'address' => ['required'],
        'city' => ['required'],
        'state' => ['required'],
        'zip_code' => ['required'],
        'country' => ['required'],
    ]);
});

it('can create address with optional fields empty', function () {
    $customer = test()->customer;

    $component = Livewire::test(AddressRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(CreateAction::class);

    $component->set('mountedActions.0.data', [
        'name' => 'Billing John',
        'surname' => 'Doe',
        'address_type' => AddressType::Billing->value,
        'phone' => '34922222222',
        'email' => 'billing@example.com',
        'address' => '321 Admin Avenue',
        'city' => 'Seville',
        'state' => 'Andalusia',
        'zip_code' => 41001,
        'country' => 'Spain',
    ]);

    $component->callMountedTableAction();
    $component->assertHasNoTableActionErrors();

    expect(Address::count())->toBe(1);
    $address = Address::first();
    expect($address->bussiness_name)->toBeNull();
    expect($address->financial_number)->toBeNull();
});
