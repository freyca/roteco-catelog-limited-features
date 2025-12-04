<?php

use App\Enums\Role;
use App\Filament\Admin\Resources\Products\ProductSpareParts\Pages\CreateProductSparePart;
use App\Filament\Admin\Resources\Products\ProductSpareParts\Pages\EditProductSparePart;
use App\Filament\Admin\Resources\Products\ProductSpareParts\Pages\ListProductSpareParts;
use App\Models\Product;
use App\Models\ProductSparePart;
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

describe('ProductSparePartResource', function () {
    it('admin can access product spare part list page', function () {
        $this->actingAs(test()->admin);

        Livewire::test(ListProductSpareParts::class)
            ->assertStatus(200);
    });

    it('can display product spare parts in list table', function () {
        $this->actingAs(test()->admin);
        $spareParts = ProductSparePart::factory(3)->create();

        $component = Livewire::test(ListProductSpareParts::class);

        foreach ($spareParts as $sparePart) {
            $component->assertSee($sparePart->name);
        }
    });

    it('admin can access create product spare part page', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateProductSparePart::class)
            ->assertStatus(200);
    });

    it('can create a new product spare part via form', function () {
        $this->actingAs(test()->admin);
        $disassembly = \App\Models\Disassembly::factory()->create();

        Livewire::test(CreateProductSparePart::class)
            ->fillForm([
                'name' => 'New Spare Part',
                'ean13' => 1234567890123,
                'slug' => 'new-spare-part',
                'price' => 100,
                'published' => true,
                'disassembly_id' => $disassembly->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(ProductSparePart::where('name', 'New Spare Part')->exists())->toBeTrue();
    });

    it('validates name is required on create', function () {
        $this->actingAs(test()->admin);
        $product = Product::factory()->create();

        Livewire::test(CreateProductSparePart::class)
            ->fillForm([
                'name' => '',
                'product_id' => $product->id,
                'main_image' => ['spare.jpg'],
            ])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('validates ean13 is required on create', function () {
        $this->actingAs(test()->admin);
        $disassembly = \App\Models\Disassembly::factory()->create();

        Livewire::test(CreateProductSparePart::class)
            ->fillForm([
                'name' => 'Test Spare Part',
                'ean13' => null,
                'slug' => 'test-spare-part',
                'price' => 100,
                'published' => true,
                'disassembly_id' => $disassembly->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['ean13' => 'required']);
    });

    it('validates disassembly is required on create', function () {
        $this->actingAs(test()->admin);

        Livewire::test(CreateProductSparePart::class)
            ->fillForm([
                'name' => 'Test Spare Part',
                'ean13' => 1234567890123,
                'slug' => 'test-spare-part',
                'price' => 100,
                'published' => true,
                'disassembly_id' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['disassembly_id' => 'required']);
    });

    it('admin can access edit product spare part page', function () {
        $this->actingAs(test()->admin);
        $sparePart = ProductSparePart::factory()->create();

        Livewire::test(EditProductSparePart::class, ['record' => $sparePart->getRouteKey()])
            ->assertStatus(200);
    });

    it('can update product spare part via form', function () {
        $this->actingAs(test()->admin);
        $sparePart = ProductSparePart::factory()->create(['name' => 'Old Name']);

        Livewire::test(EditProductSparePart::class, ['record' => $sparePart->getRouteKey()])
            ->fillForm([
                'name' => 'Updated Name',
            ])
            ->call('save');

        expect(ProductSparePart::find($sparePart->id)->name)->toBe('Updated Name');
    });

    it('validates name is required on update', function () {
        $this->actingAs(test()->admin);
        $sparePart = ProductSparePart::factory()->create();

        Livewire::test(EditProductSparePart::class, ['record' => $sparePart->getRouteKey()])
            ->fillForm([
                'name' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
    });

    it('product spare part resource has correct navigation group', function () {
        $group = \App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource::getNavigationGroup();
        expect($group)->toBe(__('Products'));
    });

    it('product spare part resource has correct model label', function () {
        $label = \App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource::getModelLabel();
        expect($label)->toBe(__('Spare parts'));
    });

    it('resource has index page', function () {
        $pages = \App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource::getPages();
        expect($pages)->toHaveKey('index');
    });

    it('resource has create page', function () {
        $pages = \App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource::getPages();
        expect($pages)->toHaveKey('create');
    });

    it('resource has edit page', function () {
        $pages = \App\Filament\Admin\Resources\Products\ProductSpareParts\ProductSparePartResource::getPages();
        expect($pages)->toHaveKey('edit');
    });

    it('can import product spare parts from CSV via table action', function () {
        Storage::fake('local');
        $this->actingAs(test()->admin);
        $disassembly = \App\Models\Disassembly::factory()->create();

        // Create a fake CSV file with correct headers and data matching ProductSparePartImporter
        $csvContent = "ean13,name,slug,price,price_with_discount,published,disassembly\n1111111111111,Imported Spare 1,imported-spare-1,100,,true,{$disassembly->id}\n2222222222222,Imported Spare 2,imported-spare-2,200,,true,{$disassembly->id}\n";
        $fileOnDisk = UploadedFile::fake()->createWithContent('sp.csv', $csvContent);

        // Test the import action through Livewire
        Livewire::test(ListProductSpareParts::class)
            ->mountTableAction('import')
            ->setTableActionData([
                'file' => $fileOnDisk,
            ])->callMountedTableAction()
            ->assertHasNoTableActionErrors();
    });
});
