<?php

use App\Enums\Role;
use App\Filament\Admin\Resources\Users\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);

    Filament::setCurrentPanel(
        Filament::getPanel('admin')
    );
});

describe('UserResource', function () {
    it('admin can access user list page', function () {
        test()->actingAs(test()->admin);

        Livewire::test(ListUsers::class)
            ->assertStatus(200);
    });

    it('can display users in list table', function () {
        test()->actingAs(test()->admin);
        $users = User::factory(3)->create();

        $component = Livewire::test(ListUsers::class);

        foreach ($users as $user) {
            $component->assertSee($user->email);
        }
    });

    it('admin can access create user page', function () {
        test()->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->assertStatus(200);
    });

    it('can create a new user via form', function () {
        test()->actingAs(test()->admin);
        $component = Livewire::test(CreateUser::class);
        $component->set('data.name', 'New User');
        $component->set('data.surname', 'Test Surname');
        $component->set('data.email', 'newuser@test.com');
        $component->set('data.password', 'password123');
        $component->set('data.role', Role::Customer->value);
        $component->call('create');
        $component->assertHasNoFormErrors();
        expect(User::where('email', 'newuser@test.com')->exists())->toBeTrue();
    });

    it('can create an admin user via form', function () {
        test()->actingAs(test()->admin);
        $component = Livewire::test(CreateUser::class);
        $component->set('data.name', 'Admin User');
        $component->set('data.surname', 'Admin Surname');
        $component->set('data.email', 'adminuser@test.com');
        $component->set('data.password', 'adminpassword');
        $component->set('data.role', Role::Admin->value);
        $component->call('create');
        $component->assertHasNoFormErrors();
        expect(User::where('email', 'adminuser@test.com')->where('role', Role::Admin->value)->exists())->toBeTrue();
    });

    it('validates name is required on create', function () {
        test()->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => '',
                'surname' => 'Test Surname',
                'email' => 'test@test.com',
                'password' => 'password123',
                'role' => Role::Customer->value,
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('validates email is required on create', function () {
        test()->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'surname' => 'Test Surname',
                'email' => '',
                'password' => 'password123',
                'role' => Role::Customer->value,
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'required']);
    });

    it('validates surname is required on create', function () {
        test()->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'surname' => '',
                'email' => 'test@test.com',
                'password' => 'password123',
                'role' => Role::Customer->value,
            ])
            ->call('create')
            ->assertHasFormErrors(['surname' => 'required']);
    });

    it('can create user without password', function () {
        test()->actingAs(test()->admin);
        $component = Livewire::test(CreateUser::class);
        $component->set('data.name', 'Test User');
        $component->set('data.surname', 'Test Surname');
        $component->set('data.email', 'test@test.com');
        $component->set('data.password', '');
        $component->set('data.role', Role::Customer->value);
        $component->call('create');
        $component->assertHasNoFormErrors();
        expect(User::where('email', 'test@test.com')->exists())->toBeTrue();
    });

    it('admin can access edit user page', function () {
        test()->actingAs(test()->admin);
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertStatus(200);
    });

    it('can update user via form', function () {
        test()->actingAs(test()->admin);
        $user = User::factory()->create(['name' => 'Old Name']);
        $component = Livewire::test(EditUser::class, ['record' => $user->getRouteKey()]);
        $component->set('data.name', 'Updated Name');
        $component->call('save');
        expect(User::find($user->id)->name)->toBe('Updated Name');
    });

    it('validates name is required on update', function () {
        test()->actingAs(test()->admin);
        $user = User::factory()->create();
        $component = Livewire::test(EditUser::class, ['record' => $user->getRouteKey()]);
        $component->set('data.name', '');
        $component->call('save');
        $component->assertHasFormErrors(['name' => 'required']);
    });

    it('user resource has correct navigation group', function () {
        $group = \App\Filament\Admin\Resources\Users\Users\UserResource::getNavigationGroup();
        expect($group)->toBe(__('Users'));
    });

    it('user resource has correct model label', function () {
        $label = \App\Filament\Admin\Resources\Users\Users\UserResource::getModelLabel();
        expect($label)->toBe(__('User'));
    });

    it('resource has index page', function () {
        $pages = \App\Filament\Admin\Resources\Users\Users\UserResource::getPages();
        expect($pages)->toHaveKey('index');
    });

    it('resource has create page', function () {
        $pages = \App\Filament\Admin\Resources\Users\Users\UserResource::getPages();
        expect($pages)->toHaveKey('create');
    });

    it('resource has edit page', function () {
        $pages = \App\Filament\Admin\Resources\Users\Users\UserResource::getPages();
        expect($pages)->toHaveKey('edit');
    });
});
