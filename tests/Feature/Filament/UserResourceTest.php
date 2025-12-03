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
        $this->actingAs(test()->admin);

        Livewire::test(ListUsers::class)
            ->assertStatus(200);
    });

    it('can display users in list table', function () {
        $this->actingAs(test()->admin);
        $users = User::factory(3)->create();

        $component = Livewire::test(ListUsers::class);

        foreach ($users as $user) {
            $component->assertSee($user->email);
        }
    });

    it('admin can access create user page', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->assertStatus(200);
    });

    it('can create a new user via form', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New User',
                'surname' => 'Test Surname',
                'email' => 'newuser@test.com',
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(User::where('email', 'newuser@test.com')->exists())->toBeTrue();
    });

    it('validates name is required on create', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => '',
                'surname' => 'Test Surname',
                'email' => 'test@test.com',
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('validates email is required on create', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'surname' => 'Test Surname',
                'email' => '',
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'required']);
    });

    it('validates surname is required on create', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'surname' => '',
                'email' => 'test@test.com',
                'password' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['surname' => 'required']);
    });

    it('can create user without password', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'surname' => 'Test Surname',
                'email' => 'test@test.com',
                'password' => '',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(User::where('email', 'test@test.com')->exists())->toBeTrue();
    });

    it('admin can access edit user page', function () {
        $this->actingAs(test()->admin);
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertStatus(200);
    });

    it('can update user via form', function () {
        $this->actingAs(test()->admin);
        $user = User::factory()->create(['name' => 'Old Name']);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save');

        expect(User::find($user->id)->name)->toBe('Updated Name');
    });

    it('validates name is required on update', function () {
        $this->actingAs(test()->admin);
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'name' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
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
