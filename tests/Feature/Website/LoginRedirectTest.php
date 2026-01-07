<?php

use App\Enums\Role;
use App\Http\Responses\FilamentLoginResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

describe('LoginRedirect', function () {
    it('redirects admin users to /admin after login', function () {
        $admin = User::factory()->admin_notifiable()->create();

        Auth::login($admin);

        $response = new FilamentLoginResponse;
        $redirectResponse = $response->toResponse(request());

        expect($redirectResponse->getTargetUrl())->toContain('/admin');
    });

    it('redirects customer users to / after login', function () {
        $user = User::factory()->create(['role' => Role::Customer]);

        Auth::login($user);

        $response = new FilamentLoginResponse;
        $redirectResponse = $response->toResponse(request());

        expect($redirectResponse->getTargetUrl())->toContain('/');
    });

    it('redirects user with default role to / after login', function () {
        $user = User::factory()->create();

        Auth::login($user);

        $response = new FilamentLoginResponse;
        $redirectResponse = $response->toResponse(request());

        expect($redirectResponse->getTargetUrl())->toContain('/');
    });
});
