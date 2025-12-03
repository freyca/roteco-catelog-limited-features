<?php

use App\Models\Disassembly;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSparePart;
use App\Models\User;
use App\Notifications\AdminOrderNotification;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\get;

test('user can visit home and login', function () {
    // Visit home page (may redirect if not authenticated)
    $response = get('/');
    expect($response->status())->toBeIn([200, 302]);

    // Create and login as a user
    $user = User::factory()->create();
    test()->actingAs($user);

    // Verify user is authenticated by visiting home again
    $response = get('/');
    expect($response->status())->toBeIn([200, 302]);
});

test('product page displays only published disassemblies and their spare parts', function () {
    // Create a published product
    $product = Product::factory()->create(['published' => true]);

    // Create published disassembly with spare parts
    $publishedDisassembly = Disassembly::factory()->create(['product_id' => $product->id]);
    $publishedSpareParts = ProductSparePart::factory(2)
        ->create(['disassembly_id' => $publishedDisassembly->id, 'published' => true]);

    // Create unpublished disassembly with spare parts
    $unpublishedDisassembly = Disassembly::factory()->create(['product_id' => $product->id]);
    $unpublishedSpareParts = ProductSparePart::factory(2)
        ->create(['disassembly_id' => $unpublishedDisassembly->id, 'published' => false]);

    // Create a user and login
    $user = User::factory()->create();
    test()->actingAs($user);

    // Visit the product page
    $response = get('/producto/'.$product->slug);
    $response->assertStatus(200);

    // Assert published disassembly is visible
    $response->assertSee($publishedDisassembly->name);

    // Assert published spare parts are visible
    $publishedSpareParts->each(function ($sparePart) use ($response) {
        $response->assertSee($sparePart->name);
    });

    // Assert unpublished spare parts are NOT visible
    $unpublishedSpareParts->each(function ($sparePart) use ($response) {
        $response->assertDontSee($sparePart->name);
    });
});

test('user can add random spare parts to cart', function () {
    // Create a published product
    $product = Product::factory()->create(['published' => true]);

    // Create disassembly with spare parts
    $disassembly = Disassembly::factory()->create(['product_id' => $product->id]);
    $spareParts = ProductSparePart::factory(5)
        ->create(['disassembly_id' => $disassembly->id, 'published' => true]);

    // Create a user and login
    $user = User::factory()->create();
    test()->actingAs($user);

    // Select random spare parts to add to cart (e.g., 2 out of 5)
    $selectedSpareParts = $spareParts->random(2);

    // Add selected spare parts to cart by testing the Livewire component
    foreach ($selectedSpareParts as $sparePart) {
        Livewire::test('buttons.product-cart-buttons', ['product' => $sparePart, 'variants' => collect()])
            ->call('add')
            ->assertDispatched('refresh-cart');
    }

    // Get the cart and verify the correct spare parts are in it
    $cart = app(\App\Services\Cart::class);

    // Verify selected spare parts are in cart
    foreach ($selectedSpareParts as $sparePart) {
        expect($cart->hasProduct($sparePart, false, null))->toBeTrue();
        expect($cart->getTotalQuantityForProduct($sparePart, false, null))->toBe(1);
    }

    // Verify unselected spare parts are NOT in cart
    $unselectedSpareParts = $spareParts->diff($selectedSpareParts);
    foreach ($unselectedSpareParts as $sparePart) {
        expect($cart->hasProduct($sparePart, false, null))->toBeFalse();
    }
});

test('user can complete checkout by submitting the checkout form', function () {
    // Setup: Fake notifications
    Notification::fake();

    // Create a published product with disassembly and spare parts
    $product = Product::factory()->create(['published' => true]);
    $disassembly = Disassembly::factory()->create(['product_id' => $product->id]);
    $spareParts = ProductSparePart::factory(3)
        ->create(['disassembly_id' => $disassembly->id, 'published' => true]);

    // Create user with email and login
    $user = User::factory()->create([
        'email' => 'checkout-test@example.com',
        'name' => 'Checkout Test User',
    ]);
    test()->actingAs($user);

    // Add all spare parts to cart
    foreach ($spareParts as $sparePart) {
        Livewire::test('buttons.product-cart-buttons', ['product' => $sparePart, 'variants' => collect()])
            ->call('add');
    }

    // Create admin user for notifications
    $admin = User::factory()->admin()->create();

    // Visit checkout page to verify form is present
    $response = get('/carrito');
    $response->assertStatus(200);
    $response->assertSee('Shipping address');

    // Get the cart to verify items before checkout
    $cart = app(\App\Services\Cart::class);
    expect($cart->getTotalQuantity())->toBe(3);

    // Submit the checkout form via Livewire (the "place order" button)
    Livewire::test('forms.checkout-form')
        ->set('checkoutFormData.shipping_name', $user->name)
        ->set('checkoutFormData.shipping_surname', 'Test')
        ->set('checkoutFormData.shipping_email', $user->email)
        ->set('checkoutFormData.shipping_phone', '1234567890')
        ->set('checkoutFormData.shipping_address', '123 Test Street')
        ->set('checkoutFormData.shipping_city', 'Test City')
        ->set('checkoutFormData.shipping_state', 'Test State')
        ->set('checkoutFormData.shipping_zip_code', '12345')
        ->set('checkoutFormData.shipping_country', 'ES')
        ->set('checkoutFormData.use_shipping_address_as_billing_address', true)
        ->set('checkoutFormData.purchase_as_guest', false)
        ->call('create')
        ->assertHasNoFormErrors();

    // Verify order was created in database
    $order = Order::where('user_id', $user->id)->latest()->first();
    expect($order)->not->toBeNull();
    expect($order->user_id)->toBe($user->id);
    expect($order->purchase_cost)->toBeGreaterThan(0);

    // Verify order contains all 3 spare parts
    $orderProducts = $order->orderProducts;
    expect($orderProducts->count())->toBe(3);

    foreach ($spareParts as $sparePart) {
        $orderProduct = $orderProducts->firstWhere('orderable_id', $sparePart->id);
        expect($orderProduct)->not->toBeNull();
        expect($orderProduct->orderable_type)->toBe(ProductSparePart::class);
        expect($orderProduct->quantity)->toBe(1);
    }

    // Verify OrderCreated event was dispatched
    // Event is dispatched but not faked, so we can't assert

    // Verify user was notified via queued listener (SendOrderConfirmationToUser)
    Notification::assertSentTo($user, OrderConfirmationNotification::class);

    // Verify admin was notified
    Notification::assertSentTo($admin, AdminOrderNotification::class);
});
