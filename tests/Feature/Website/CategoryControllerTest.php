<?php

use App\Enums\Role;
use App\Models\Category;
use App\Models\User;

beforeEach(function () {
    // Create an authenticated user for the tests
    test()->user = User::factory()->create(['role' => Role::Customer]);
});

describe('CategoryController', function () {
    it('returns categories index view', function () {
        Category::factory(3)->create();

        $response = test()->actingAs(test()->user)->get(route('category-list'));

        expect($response->status())->toBe(200);
        $response->assertViewIs('pages.categories');
    });

    it('passes categories to view', function () {
        $categories = Category::factory(3)->create();

        $response = test()->actingAs(test()->user)->get(route('category-list'));

        $viewCategories = $response->viewData('categories');
        expect($viewCategories)->toHaveCount(3);
        foreach ($categories as $category) {
            expect($viewCategories->pluck('id')->contains((string) $category->id))->toBeTrue();
        }
    });

    it('passes breadcrumbs to view', function () {
        Category::factory(3)->create();

        $response = test()->actingAs(test()->user)->get(route('category-list'));

        $breadcrumbs = $response->viewData('breadcrumbs');
        expect($breadcrumbs)->not()->toBeNull();
        $response->assertViewHas('breadcrumbs');
    });

    it('returns category detail view', function () {
        $category = Category::factory()->create();

        $response = test()->actingAs(test()->user)->get(route('category', $category));

        expect($response->status())->toBe(200);
        $response->assertViewIs('pages.category');
    });

    it('passes category to view', function () {
        $category = Category::factory()->create();

        $response = test()->actingAs(test()->user)->get(route('category', $category));

        $viewCategory = $response->viewData('category');
        expect((string) $viewCategory->id)->toBe((string) $category->id);
        expect($viewCategory->name)->toBe($category->name);
    });

    it('passes products to view', function () {
        $category = Category::factory()->create();

        $response = test()->actingAs(test()->user)->get(route('category', $category));

        $products = $response->viewData('products');
        expect($products)->not()->toBeNull();
        $response->assertViewHas('products');
    });

    it('passes breadcrumbs with category to detail view', function () {
        $category = Category::factory()->create();

        $response = test()->actingAs(test()->user)->get(route('category', $category));

        $breadcrumbs = $response->viewData('breadcrumbs');
        expect($breadcrumbs)->not()->toBeNull();
        $response->assertViewHas('breadcrumbs');
    });
});
