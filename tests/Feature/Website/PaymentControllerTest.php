<?php

use App\Enums\Role;
use App\Models\Order;
use App\Models\User;

beforeEach(function () {
    // Create an authenticated user and an admin user for the tests
    test()->user = User::factory()->create(['role' => Role::Customer]);
    User::factory()->create(['role' => Role::Admin]);
});

describe('PaymentController', function () {
    it('returns purchase complete view on successful order', function () {
        $order = Order::factory()->for(test()->user)->create();

        $response = test()->actingAs(test()->user)->get(route('payment.purchase-complete', $order));

        expect($response->status())->toBe(200);
        $response->assertViewIs('pages.purchase-complete');
    });

    it('passes order to purchase complete view', function () {
        $order = Order::factory()->for(test()->user)->create();

        $response = test()->actingAs(test()->user)->get(route('payment.purchase-complete', $order));

        expect($response->viewData('order')->id)->toBe((string) $order->id);
    });

    it('passes noindex seotags to purchase complete view', function () {
        $order = Order::factory()->for(test()->user)->create();

        $response = test()->actingAs(test()->user)->get(route('payment.purchase-complete', $order));

        $seotags = $response->viewData('seotags');
        expect($seotags)->not()->toBeNull();
    });
});
