<?php

use App\Models\Product;
use App\Models\ProductSparePart;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ProductSparePartSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    test()->admin = User::factory()->admin_notifiable()->create();
});

test('product urls returns 200 if published and 403 if not', function () {
    test()->seed(ProductSeeder::class);
    $user = User::factory()->create();

    $publishedProducts = Product::where('published', true)->get();

    foreach ($publishedProducts as $product) {
        $response = actingAs($user)->get('/producto/'.$product->slug);
        $response->assertStatus(200);
    }

    $notPublishedProducts = Product::where('published', false)->get();

    foreach ($notPublishedProducts as $product) {
        $response = get('/producto/'.$product->slug);
        $response->assertStatus(403);
    }
})->group('product-urls');

test('admin can access published and not published products', function () {
    test()->seed(ProductSparePartSeeder::class);
    test()->seed(ProductSeeder::class);

    $products = Product::all();

    foreach ($products as $product) {
        $response = actingAs(test()->admin)->get('/producto/'.$product->slug);
        $response->assertStatus(200);
    }
})->group('product-urls');

test('spare parts urls cannot be accesed directly', function () {
    test()->seed(ProductSparePartSeeder::class);
    $user = User::factory()->create();

    $products = ProductSparePart::all();

    foreach ($products as $product) {
        $response = actingAs($user)->get('/pieza-de-repuesto/'.$product->slug);
        $response->assertStatus(404);
    }
})->group('product-urls');
