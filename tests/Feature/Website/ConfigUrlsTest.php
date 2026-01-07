<?php

use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('guest users are redirected to login for all the application', function (string $url) {
    $response = get($url);
    $response->assertRedirect(Filament::getPanel('user')->getLoginUrl());
})->with('configurls');

test('authenticated users can access the application', function (string $url) {
    $user = User::factory()->create();
    actingAs($user);

    $response = get($url);

    // / should redirect to category-list
    if ($url === '/') {
        $response->assertStatus(302);
        $response->assertRedirect(route('category-list'));
    } else {
        // All other URLs should return 200
        $response->assertStatus(200);
    }
})->with('configurls');

test('standard users cannot access admin panel', function () {
    $user = User::factory()->create();
    actingAs($user);

    $response = get(Filament::getPanel('admin')->getUrl());

    $response->assertForbidden();
});

test('admin users accesing user panel are redirected to admin panel', function () {
    test()->admin = User::factory()->admin_notifiable()->create();
    actingAs(test()->admin);

    $response = get(Filament::getPanel('user')->getUrl());

    $response->assertRedirect(Filament::getPanel('admin')->getUrl());
});
