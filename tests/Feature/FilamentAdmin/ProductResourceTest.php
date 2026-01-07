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
        test()->actingAs(test()->admin);
        $component = Livewire::test(ListProducts::class);
        $component->assertSee(__('Products'));
    });

    it('can display products in list table', function () {
        test()->actingAs(test()->admin);
        $products = Product::factory(3)->create();
        $component = Livewire::test(ListProducts::class);
        foreach ($products as $product) {
            $component->assertSee($product->name);
        }
    });

    it('admin can access create product page', function () {
        test()->actingAs(test()->admin);
        $component = Livewire::test(CreateProduct::class);
        $component->assertFormComponentExists('name');
    });

    it('can create a new product via form', function () {
        test()->actingAs(test()->admin);
        $category = Category::factory()->create();
        $file = UploadedFile::fake()->image('product.jpg');
        $component = Livewire::test(CreateProduct::class);
        $component->fillForm([
            'name' => 'New Product',
            'slug' => 'new-product',
            'reference' => 'REF-12345678',
            'category_id' => $category->id,
            'main_image' => $file,
            'disassemblies' => [],
        ])->call('create');
        $component->assertHasNoFormErrors();
        expect(Product::where('name', 'New Product')->exists())->toBeTrue();
    });

    it('validates name is required on update', function () {
        test()->actingAs(test()->admin);
        $product = Product::factory()->create();
        $component = Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()]);
        $component->fillForm(['name' => ''])->call('save');
        $component->assertHasFormErrors(['name' => 'required']);
    });

    it('validates reference is required on create', function () {
        test()->actingAs(test()->admin);
        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Test Product',
                'reference' => '',
                'category_id' => $category->id,
                'main_image' => ['product.jpg'],
            ])
            ->call('create')
            ->assertHasFormErrors(['reference' => 'required']);
    });

    it('validates category_id is required on create', function () {
        test()->actingAs(test()->admin);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Test Product',
                'reference' => 'REF-12345678',
                'category_id' => null,
                'main_image' => ['product.jpg'],
            ])
            ->call('create')
            ->assertHasFormErrors(['category_id' => 'required']);
    });

    it('validates main_image is required on create', function () {
        test()->actingAs(test()->admin);
        $category = Category::factory()->create();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Test Product',
                'reference' => 'REF-12345678',
                'category_id' => $category->id,
                'main_image' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['main_image' => 'required']);
    });

    it('admin can access edit product page', function () {
        test()->actingAs(test()->admin);
        $product = Product::factory()->create();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->assertStatus(200);
    });

    it('can update product via form', function () {
        test()->actingAs(test()->admin);
        $product = Product::factory()->create(['name' => 'Old Name']);

        $component = Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()]);
        $component->fillForm(['name' => 'Updated Name'])->call('save');
        expect(Product::find($product->id)->name)->toBe('Updated Name');
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
        test()->actingAs(test()->admin);
        $category = Category::factory()->create();

        // Create a fake CSV file with correct headers and data matching ProductImporter
        $csvContent = "reference,name,published,main_image,category\nREF-0001,Imported Product 1,1,product1.jpg,{$category->id}\nREF-0002,Imported Product 2,1,product2.jpg,{$category->id}\n";
        $fileOnDisk = UploadedFile::fake()->createWithContent('prod.csv', $csvContent);

        // Test the import action through Livewire (queue processes synchronously by default in tests)
        Livewire::test(ListProducts::class)
            ->mountTableAction('import')
            ->setTableActionData([
                'file' => $fileOnDisk,
            ])->callMountedTableAction()
            ->assertHasNoTableActionErrors();

        expect(Product::where('name', 'Imported Product 1')->where('main_image', 'product1.jpg')->where('category_id', $category->id)->exists())->toBeTrue();
        expect(Product::where('name', 'Imported Product 2')->where('main_image', 'product2.jpg')->where('category_id', $category->id)->exists())->toBeTrue();
    });
});
