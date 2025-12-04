<?php

use App\Enums\Role;
use App\Filament\Admin\Resources\Products\Products\Pages\CreateProduct;
use App\Filament\Admin\Resources\Products\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\Products\Pages\ListProducts;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    test()->admin = User::factory()->create(['role' => Role::Admin]);

    Filament::setCurrentPanel(
        Filament::getPanel('admin')
    );
});

describe('ProductResource', function () {
    it('admin can access product list page', function () {
        $this->actingAs(test()->admin);

        Livewire::test(ListProducts::class)
            ->assertStatus(200);
    });

    it('can display products in list table', function () {
        $this->actingAs(test()->admin);
        $products = Product::factory(3)->create();

        $component = Livewire::test(ListProducts::class);

        foreach ($products as $product) {
            $component->assertSee($product->name);
        }
    });

    it('admin can access create product page', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateProduct::class)
            ->assertStatus(200);
    });

    it('can create a new product via form', function () {
        $this->actingAs(test()->admin);
        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'New Product',
                'slug' => 'new-product',
                'ean13' => '1234567890123',
                'category_id' => $category->id,
                'main_image' => ['product.jpg'],
                'disassemblies' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Product::where('name', 'New Product')->exists())->toBeTrue();
    });

    it('validates name is required on create', function () {
        $this->actingAs(test()->admin);
        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => '',
                'ean13' => '1234567890123',
                'category_id' => $category->id,
                'main_image' => ['product.jpg'],
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('validates ean13 is required on create', function () {
        $this->actingAs(test()->admin);
        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Test Product',
                'ean13' => '',
                'category_id' => $category->id,
                'main_image' => ['product.jpg'],
            ])
            ->call('create')
            ->assertHasFormErrors(['ean13' => 'required']);
    });

    it('validates category_id is required on create', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Test Product',
                'ean13' => '1234567890123',
                'category_id' => null,
                'main_image' => ['product.jpg'],
            ])
            ->call('create')
            ->assertHasFormErrors(['category_id' => 'required']);
    });

    it('validates main_image is required on create', function () {
        $this->actingAs(test()->admin);
        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Test Product',
                'ean13' => '1234567890123',
                'category_id' => $category->id,
                'main_image' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['main_image' => 'required']);
    });

    it('admin can access edit product page', function () {
        $this->actingAs(test()->admin);
        $product = Product::factory()->create();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->assertStatus(200);
    });

    it('can update product via form', function () {
        $this->actingAs(test()->admin);
        $product = Product::factory()->create(['name' => 'Old Name']);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save');

        expect(Product::find($product->id)->name)->toBe('Updated Name');
    });

    it('validates name is required on update', function () {
        $this->actingAs(test()->admin);
        $product = Product::factory()->create();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm([
                'name' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('product resource has correct navigation group', function () {
        $group = \App\Filament\Admin\Resources\Products\Products\ProductResource::getNavigationGroup();
        expect($group)->toBe(__('Products'));
    });

    it('product resource has correct model label', function () {
        $label = \App\Filament\Admin\Resources\Products\Products\ProductResource::getModelLabel();
        expect($label)->toBe(__('Product'));
    });

    it('resource has index page', function () {
        $pages = \App\Filament\Admin\Resources\Products\Products\ProductResource::getPages();
        expect($pages)->toHaveKey('index');
    });

    it('resource has create page', function () {
        $pages = \App\Filament\Admin\Resources\Products\Products\ProductResource::getPages();
        expect($pages)->toHaveKey('create');
    });

    it('resource has edit page', function () {
        $pages = \App\Filament\Admin\Resources\Products\Products\ProductResource::getPages();
        expect($pages)->toHaveKey('edit');
    });

    it('can import products from CSV via Livewire action', function () {
        Storage::fake('local');
        $this->actingAs(test()->admin);
        $category = Category::factory()->create();

        // Create a fake CSV file with correct headers and data matching ProductImporter
        $csvContent = "ean13,name,slug,published,main_image,category\n1234567890001,Imported Product 1,imported-product-1,1,product1.jpg,{$category->id}\n1234567890002,Imported Product 2,imported-product-2,1,product2.jpg,{$category->id}\n";
        $fileOnDisk = UploadedFile::fake()->createWithContent('prod.csv', $csvContent);

        // Test the import action through Livewire (queue processes synchronously by default in tests)
        Livewire::test(ListProducts::class)
            ->mountTableAction('import')
            ->setTableActionData([
                'file' => $fileOnDisk,
            ])->callMountedTableAction()
            ->assertHasNoTableActionErrors();
    });
});
