<?php

use App\Enums\Role;
use App\Http\Responses\FilamentLogoutResponse;
use App\Models\User;
use Illuminate\Http\Request;

describe('FilamentLogoutResponse', function () {
    it('redirects to home route', function () {
        $response = new FilamentLogoutResponse();
        $request = Request::create('/admin', 'GET');

        $result = $response->toResponse($request);

        expect($result->getTargetUrl())->toContain('/')
            ->and($result->getStatusCode())->toBe(302);
    });

    it('is instance of LogoutResponse contract', function () {
        $response = new FilamentLogoutResponse();

        expect($response)->toBeInstanceOf(\Filament\Auth\Http\Responses\Contracts\LogoutResponse::class);
    });

    it('returns redirect response', function () {
        $response = new FilamentLogoutResponse();
        $request = Request::create('/admin', 'GET');

        $result = $response->toResponse($request);

        expect($result)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
    });

    it('logout response redirects to named route', function () {
        $response = new FilamentLogoutResponse();
        $request = Request::create('/admin', 'GET');

        $result = $response->toResponse($request);
        $url = $result->getTargetUrl();

        expect($url)->toBe(route('home'));
    });
});
