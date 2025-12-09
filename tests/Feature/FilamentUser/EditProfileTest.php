<?php

use App\Enums\Role;
use App\Filament\User\Pages\Auth\EditProfile;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    test()->user = User::factory()->create(['role' => Role::Customer]);

    Filament::setCurrentPanel(
        Filament::getPanel('user')
    );
});

describe('EditProfile Page', function () {
    it('user can access edit profile page', function () {
        test()->actingAs(test()->user);

        Livewire::test(EditProfile::class)
            ->assertStatus(200);
    });

    it('user can update profile name', function () {
        test()->actingAs(test()->user);
        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', 'Updated Name');
        $component->set('data.surname', test()->user->surname);
        $component->set('data.email', test()->user->email);
        $component->call('save');
        expect(test()->user->fresh()->name)->toBe('Updated Name');
    });

    it('user can update profile surname', function () {
        test()->actingAs(test()->user);

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', test()->user->name);
        $component->set('data.surname', 'Updated Surname');
        $component->set('data.email', test()->user->email);
        $component->call('save');
        expect(test()->user->fresh()->surname)->toBe('Updated Surname');
    });

    it('user can update profile email', function () {
        test()->actingAs(test()->user);

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', test()->user->name);
        $component->set('data.surname', test()->user->surname);
        $component->set('data.email', 'newemail@example.com');
        $component->call('save');
        expect(test()->user->fresh()->email)->toBe('newemail@example.com');
    });

    it('user can update profile password', function () {
        test()->actingAs(test()->user);
        $oldPassword = test()->user->password;

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', test()->user->name);
        $component->set('data.surname', test()->user->surname);
        $component->set('data.email', test()->user->email);
        $component->set('data.password', 'newpassword123');
        $component->set('data.passwordConfirmation', 'newpassword123');
        $component->call('save');
        expect(test()->user->fresh()->password)->not()->toBe($oldPassword);
    });

    it('validates name is required', function () {
        test()->actingAs(test()->user);

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', '');
        $component->set('data.surname', test()->user->surname);
        $component->set('data.email', test()->user->email);
        $component->call('save');
        $component->assertHasFormErrors(['name' => 'required']);
    });

    it('validates surname is required', function () {
        test()->actingAs(test()->user);

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', test()->user->name);
        $component->set('data.surname', '');
        $component->set('data.email', test()->user->email);
        $component->call('save');
        $component->assertHasFormErrors(['surname' => 'required']);
    });

    it('validates email is required', function () {
        test()->actingAs(test()->user);

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', test()->user->name);
        $component->set('data.surname', test()->user->surname);
        $component->set('data.email', '');
        $component->call('save');
        $component->assertHasFormErrors(['email' => 'required']);
    });

    it('validates email format', function () {
        test()->actingAs(test()->user);

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', test()->user->name);
        $component->set('data.surname', test()->user->surname);
        $component->set('data.email', 'not-an-email');
        $component->call('save');
        $component->assertHasFormErrors(['email']);
    });

    it('password and confirmation can be different when no password change', function () {
        test()->actingAs(test()->user);

        $component = Livewire::test(EditProfile::class);
        $component->set('data.name', test()->user->name);
        $component->set('data.surname', test()->user->surname);
        $component->set('data.email', test()->user->email);
        $component->set('data.password', '');
        $component->set('data.passwordConfirmation', '');
        $component->call('save');
        $component->assertHasNoFormErrors();
    });

    it('form has name component', function () {
        test()->actingAs(test()->user);

        Livewire::test(EditProfile::class)
            ->assertFormComponentExists('name');
    });

    it('form has surname component', function () {
        test()->actingAs(test()->user);

        Livewire::test(EditProfile::class)
            ->assertFormComponentExists('surname');
    });

    it('form has email component', function () {
        test()->actingAs(test()->user);

        Livewire::test(EditProfile::class)
            ->assertFormComponentExists('email');
    });

    it('form has password component', function () {
        test()->actingAs(test()->user);

        Livewire::test(EditProfile::class)
            ->assertFormComponentExists('password');
    });

    it('form has password confirmation component', function () {
        test()->actingAs(test()->user);

        Livewire::test(EditProfile::class)
            ->assertFormComponentExists('passwordConfirmation');
    });
});
