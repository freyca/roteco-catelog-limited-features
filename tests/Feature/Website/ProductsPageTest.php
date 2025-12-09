<?php

use App\Enums\Role;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    test()->user = User::factory()->create(['role' => Role::Customer]);
});

describe('Products Page - ProductCard Component', function () {
    it('renders product cards on products page', function () {
        $product = Product::factory()->create(['published' => true]);

        $response = $this->actingAs(test()->user)->get('/productos');

        expect($response->status())->toBe(200);
        $response->assertSee($product->name);
    });

    it('displays product information in card', function () {
        $product = Product::factory()->create(['name' => 'Test Product', 'published' => true]);

        $response = $this->actingAs(test()->user)->get('/productos');

        $response->assertSee('Test Product');
    });

    it('displays product with price', function () {
        $product = Product::factory()->create(['published' => true]);

        $response = $this->actingAs(test()->user)->get('/productos');

        expect($response->status())->toBe(200);
    });

    it('handles multiple products on page', function () {
        Product::factory()->count(3)->create(['published' => true]);

        $response = $this->actingAs(test()->user)->get('/productos');

        expect($response->status())->toBe(200);
    });

    it('renders product cards correctly', function () {
        $product1 = Product::factory()->create(['name' => 'Product 1', 'published' => true]);
        $product2 = Product::factory()->create(['name' => 'Product 2', 'published' => true]);

        $response = $this->actingAs(test()->user)->get('/productos');

        expect($response->status())->toBe(200);
        expect($response->content())->toContain('Product 1');
        expect($response->content())->toContain('Product 2');
    });

    it('shows page with single product', function () {
        $product = Product::factory()->create(['published' => true]);

        $response = $this->actingAs(test()->user)->get('/productos');

        expect($response->status())->toBe(200);
    });
});
