<?php

use App\Enums\AddressType;
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
        'name' => 'John',
        'surname' => 'Doe',
    ]);
    test()->actingAs(test()->user);
});

describe('Form Validation Feedback', function () {
    it('displays validation error for empty required name', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => '',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('displays validation error for non-numeric zip code', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => 'ABCDE',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertHasFormErrors(['zip_code']);
    });

    it('displays multiple validation errors at once', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => '',
                'surname' => '',
                'address' => '',
                'city' => '',
                'state' => '',
                'zip_code' => '',
                'country' => '',
                'phone' => '',
                'address_type' => null,
            ])
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

    it('shows max length validation error for name', function () {
        $longString = str_repeat('a', 256);
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => $longString,
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertHasFormErrors(['name']);
    });

    it('shows max length validation error for address', function () {
        $longString = str_repeat('a', 256);
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => $longString,
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertHasFormErrors(['address']);
    });

    it('validates phone field is required', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertHasFormErrors(['phone']);
    });
});

describe('Form Success Feedback', function () {
    it('creates address successfully', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'Jane',
                'surname' => 'Smith',
                'address' => '456 Oak Ave',
                'city' => 'Barcelona',
                'state' => 'Barcelona',
                'zip_code' => '08002',
                'country' => 'Spain',
                'phone' => '+34987654321',
                'address_type' => AddressType::Billing,
            ])
            ->call('create')
            ->assertSuccessful();
    });

    it('creates address with optional business name', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'Jane',
                'surname' => 'Smith',
                'address' => '789 Elm St',
                'city' => 'Valencia',
                'state' => 'Valencia',
                'zip_code' => '46001',
                'country' => 'Spain',
                'phone' => '+34999888777',
                'address_type' => AddressType::Billing,
                'bussiness_name' => 'Acme Corp',
            ])
            ->call('create')
            ->assertSuccessful();

        $address = Address::where('bussiness_name', 'Acme Corp')->first();
        expect($address)->not->toBeNull();
        expect($address->bussiness_name)->toBe('Acme Corp');
    });

    it('creates address with optional financial number', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'Jane',
                'surname' => 'Smith',
                'address' => '999 Pine St',
                'city' => 'Seville',
                'state' => 'Seville',
                'zip_code' => '41001',
                'country' => 'Spain',
                'phone' => '+34888777666',
                'address_type' => AddressType::Billing,
                'financial_number' => 'ES12345678A',
            ])
            ->call('create')
            ->assertSuccessful();

        $address = Address::where('financial_number', 'ES12345678A')->first();
        expect($address)->not->toBeNull();
    });
});

describe('Edge Cases - Empty Form States', function () {
    it('handles form with only optional fields empty', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
                'bussiness_name' => '',
                'financial_number' => '',
            ])
            ->call('create')
            ->assertSuccessful();
    });
});

describe('Edge Cases - Boundary Values', function () {
    it('accepts maximum length string (255 chars)', function () {
        $maxString = str_repeat('a', 255);
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => $maxString,
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertSuccessful();
    });

    it('rejects string longer than 255 chars', function () {
        $tooLongString = str_repeat('a', 256);
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => $tooLongString,
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertHasFormErrors(['name']);
    });

    it('accepts numeric zip code', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertSuccessful();
    });
});

describe('Address Type Constraints', function () {
    it('accepts billing address type', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Billing,
            ])
            ->call('create')
            ->assertSuccessful();
    });

    it('accepts shipping address type', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertSuccessful();
    });

    it('accepts shipping and billing address type', function () {
        Livewire::test(CreateAddress::class)
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::ShippingAndBilling,
            ])
            ->call('create')
            ->assertSuccessful();
    });

    it('correctly displays address type badge colors', function ($addressType, $expectedColor) {
        expect($addressType->getColor())->toBe($expectedColor);
    })->with([
        [AddressType::Billing, 'success'],
        [AddressType::Shipping, 'info'],
        [AddressType::ShippingAndBilling, 'gray'],
    ]);

    it('correctly displays address type icons', function ($addressType, $expectedIcon) {
        expect($addressType->getIcon())->toBe($expectedIcon);
    })->with([
        [AddressType::Billing, 'heroicon-c-document-currency-euro'],
        [AddressType::Shipping, 'heroicon-o-truck'],
        [AddressType::ShippingAndBilling, 'heroicon-m-rocket-launch'],
    ]);
});

describe('Error Recovery', function () {
    it('can correct and resubmit form after validation error', function () {
        $livewire = Livewire::test(CreateAddress::class);

        // First submission fails
        $livewire
            ->fillForm([
                'name' => '',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::Shipping,
            ])
            ->call('create')
            ->assertHasFormErrors(['name']);

        // Correct and resubmit succeeds
        $livewire
            ->fillForm(['name' => 'John'])
            ->call('create')
            ->assertSuccessful();
    });

    it('edge case: edit address changing type with optional fields', function () {
        $address = Address::factory()->for(test()->user)->create([
            'name' => 'John',
            'surname' => 'Doe',
            'address' => '123 Main St',
            'city' => 'Madrid',
            'address_type' => AddressType::Shipping,
            'bussiness_name' => null,
        ]);

        Livewire::test(EditAddress::class, ['record' => $address->getRouteKey()])
            ->fillForm([
                'name' => 'John',
                'surname' => 'Doe',
                'address' => '123 Main St',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'zip_code' => '28001',
                'country' => 'Spain',
                'phone' => '+34123456789',
                'address_type' => AddressType::ShippingAndBilling,
                'bussiness_name' => 'My Business',
                'financial_number' => 'ES12345678A',
            ])
            ->call('save')
            ->assertSuccessful();

        $address->refresh();
        expect($address->address_type)->toBe(AddressType::ShippingAndBilling);
        expect($address->bussiness_name)->toBe('My Business');
        expect($address->financial_number)->toBe('ES12345678A');
    });
});

describe('Address List and Delete', function () {
    it('creates address and can delete it', function () {
        $address = Address::factory()->for(test()->user)->create();

        expect(Address::find($address->id))->not->toBeNull();

        $address->delete();

        expect(Address::find($address->id))->toBeNull();
    });

    it('user can create multiple addresses', function () {
        Address::factory(3)->for(test()->user)->create();

        $userAddresses = Address::where('user_id', test()->user->id)->get();
        expect($userAddresses)->toHaveCount(3);
    });
});

describe('Enum Labels and Translations', function () {
    it('displays billing label', function () {
        expect(AddressType::Billing->getLabel())->not->toBeEmpty();
    });

    it('displays shipping label', function () {
        expect(AddressType::Shipping->getLabel())->not->toBeEmpty();
    });

    it('displays shipping and billing label', function () {
        expect(AddressType::ShippingAndBilling->getLabel())->not->toBeEmpty();
    });
});
