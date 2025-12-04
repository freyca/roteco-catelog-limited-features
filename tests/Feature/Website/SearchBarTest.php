<?php

use App\Livewire\SearchBar;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders successfully', function () {
    Livewire::test(SearchBar::class)
        ->assertStatus(200);
});

it('does not search with less than 3 characters', function () {
    Product::factory()->create(['name' => 'iPhone 15', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'iP');

    // Verify the component rendered
    expect($component->viewData('results'))->toEqual([]);
});

it('returns empty results when no products match', function () {
    Product::factory()->create(['name' => 'iPhone 15', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'Samsung');

    expect($component->viewData('results'))->toEqual([]);
});

it('finds products by exact name match', function () {
    $product = Product::factory()->create(['name' => 'iPhone 15 Pro', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'iPhone 15 Pro');

    $results = $component->viewData('results');
    expect($results['products']->contains('id', $product->id))->toBeTrue();
});

it('finds products by partial name match', function () {
    $product = Product::factory()->create(['name' => 'iPhone 15 Pro Max', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'iPhone');

    $results = $component->viewData('results');
    expect($results['products']->contains('id', $product->id))->toBeTrue();
});

it('finds products case insensitively', function () {
    $product = Product::factory()->create(['name' => 'Samsung Galaxy S24', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'samsung');

    $results = $component->viewData('results');
    expect($results['products']->contains('id', $product->id))->toBeTrue();
});

it('limits results to 5 products', function () {
    Product::factory(10)->sequence(
        ['name' => 'Test Product One', 'published' => true],
        ['name' => 'Test Product Two', 'published' => true],
        ['name' => 'Test Product Three', 'published' => true],
        ['name' => 'Test Product Four', 'published' => true],
        ['name' => 'Test Product Five', 'published' => true],
        ['name' => 'Test Product Six', 'published' => true],
        ['name' => 'Test Product Seven', 'published' => true],
        ['name' => 'Test Product Eight', 'published' => true],
        ['name' => 'Test Product Nine', 'published' => true],
        ['name' => 'Test Product Ten', 'published' => true],
    )->create();

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'Test');

    $results = $component->viewData('results');
    expect($results['products']->count())->toBe(5);
});

it('returns correct products for multiple matches', function () {
    $product1 = Product::factory()->create(['name' => 'Apple iPhone 15', 'published' => true]);
    $product2 = Product::factory()->create(['name' => 'Apple iPhone 14', 'published' => true]);
    $product3 = Product::factory()->create(['name' => 'Samsung Galaxy S24', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'Apple');

    $results = $component->viewData('results');
    expect($results['products']->contains('id', $product1->id))->toBeTrue();
    expect($results['products']->contains('id', $product2->id))->toBeTrue();
    expect($results['products']->contains('id', $product3->id))->toBeFalse();
});

it('handles special characters in search', function () {
    $product = Product::factory()->create(['name' => 'USB-C Cable (2m)', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'USB');

    $results = $component->viewData('results');
    expect($results['products']->contains('id', $product->id))->toBeTrue();
});

it('finds products with numbers in name', function () {
    $product = Product::factory()->create(['name' => 'RTX 4090 Graphics Card', 'published' => true]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', '4090');

    $results = $component->viewData('results');
    expect($results['products']->contains('id', $product->id))->toBeTrue();
});

it('search term updates dynamically', function () {
    $iPhone = Product::factory()->create(['name' => 'Apple iPhone 15', 'published' => true]);
    $samsung = Product::factory()->create(['name' => 'Samsung Galaxy S24', 'published' => true]);

    $component = Livewire::test(SearchBar::class);

    $component->set('searchTerm', 'Apple');
    $results1 = $component->viewData('results');
    expect($results1['products']->contains('id', $iPhone->id))->toBeTrue();
    expect($results1['products']->contains('id', $samsung->id))->toBeFalse();

    $component->set('searchTerm', 'Samsung');
    $results2 = $component->viewData('results');
    expect($results2['products']->contains('id', $samsung->id))->toBeTrue();
    expect($results2['products']->contains('id', $iPhone->id))->toBeFalse();
});

it('does not show unpublished products in search results', function () {
    $publishedProduct = Product::factory()->create(['name' => 'Published iPhone', 'published' => true]);
    $unpublishedProduct = Product::factory()->create(['name' => 'Unpublished iPhone', 'published' => false]);

    $component = Livewire::test(SearchBar::class)
        ->set('searchTerm', 'iPhone');

    $results = $component->viewData('results');
    expect($results['products']->contains('id', $publishedProduct->id))->toBeTrue();
    expect($results['products']->contains('id', $unpublishedProduct->id))->toBeFalse();
});

it('prevents SQL injection attacks', function () {
    $product = Product::factory()->create(['name' => 'Laptop Pro', 'published' => true]);

    // Test various SQL injection patterns
    $sqlInjectionAttempts = [
        "'; DROP TABLE products; --",
        "' OR '1'='1",
        "' OR 1=1 --",
        "admin' --",
        "' UNION SELECT * FROM users --",
        "%'; DELETE FROM products; --",
    ];

    foreach ($sqlInjectionAttempts as $attempt) {
        $component = Livewire::test(SearchBar::class)
            ->set('searchTerm', $attempt);

        $results = $component->viewData('results');
        // Should return empty array (no match), not throw an error or execute the injection
        expect($results)->toEqual([]);
    }

    // Verify the original product is still intact
    expect(Product::find($product->id))->not()->toBeNull();
});
