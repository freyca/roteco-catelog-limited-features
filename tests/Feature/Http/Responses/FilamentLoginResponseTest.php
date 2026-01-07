<?php

use App\Enums\Role;
use App\Http\Responses\FilamentLoginResponse;
use App\Models\User;
use Illuminate\Http\Request;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);
    test()->customer = User::factory()->create(['role' => Role::Customer]);
});

describe('FilamentLoginResponse', function () {
    it('is instance of LoginResponse contract', function () {
        $response = new FilamentLoginResponse;

        expect($response)->toBeInstanceOf(\Filament\Auth\Http\Responses\Contracts\LoginResponse::class);
    });

    it('redirects admin to admin panel', function () {
        test()->actingAs(test()->admin);
        $response = new FilamentLoginResponse;
        $request = Request::create('/login', 'POST');

        $result = $response->toResponse($request);

        expect($result->getTargetUrl())->toContain('/admin');
    });

    it('redirects customer to home', function () {
        test()->actingAs(test()->customer);
        $response = new FilamentLoginResponse;
        $request = Request::create('/login', 'POST');

        $result = $response->toResponse($request);

        expect($result->getTargetUrl())->not()->toContain('/admin');
    });

    it('returns redirect response', function () {
        test()->actingAs(test()->customer);
        $response = new FilamentLoginResponse;
        $request = Request::create('/login', 'POST');

        $result = $response->toResponse($request);

        expect($result)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
    });

    it('redirects correctly for admin user', function () {
        auth()->login(test()->admin);
        $response = new FilamentLoginResponse;
        $request = Request::create('/login', 'POST');

        $result = $response->toResponse($request);

        expect($result->getTargetUrl())->toContain('/admin');
    });

    it('redirects correctly for customer user', function () {
        auth()->login(test()->customer);
        $response = new FilamentLoginResponse;
        $request = Request::create('/login', 'POST');

        $result = $response->toResponse($request);

        expect($result->getTargetUrl())->not()->toContain('/admin');
    });
});
