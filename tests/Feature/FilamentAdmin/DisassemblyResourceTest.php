<?php

use App\Enums\Role;
use App\Filament\Admin\Resources\Products\Disassemblies\Pages\CreateDisassembly;
use App\Filament\Admin\Resources\Products\Disassemblies\Pages\EditDisassembly;
use App\Filament\Admin\Resources\Products\Disassemblies\Pages\ListDisassemblies;
use App\Models\Disassembly;
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

describe('DisassemblyResource', function () {
    it('admin can access disassembly list page', function () {
        test()->actingAs(test()->admin);
        $component = Livewire::test(ListDisassemblies::class);
        $component->assertSee(__('Disassemblies'));
    });

    it('can display disassemblies in list table', function () {
        test()->actingAs(test()->admin);
        $disassemblies = Disassembly::factory(3)->create();

        $component = Livewire::test(ListDisassemblies::class);

        foreach ($disassemblies as $disassembly) {
            $component->assertSee($disassembly->name);
        }
    });

    it('admin can access create disassembly page', function () {
        test()->actingAs(test()->admin);
        $component = Livewire::test(CreateDisassembly::class);
        $component->assertFormComponentExists('name');
    });

    it('can create a new disassembly via form', function () {
        test()->actingAs(test()->admin);
        $product = Product::factory()->create();
        $file = UploadedFile::fake()->image('disasm.jpg');
        $component = Livewire::test(CreateDisassembly::class);
        $component->fillForm([
            'name' => 'New Disassembly',
            'product_id' => $product->id,
            'main_image' => $file,
            'productSpareParts' => [],
        ])->call('create');
        $component->assertHasNoFormErrors();
        expect(Disassembly::where('name', 'New Disassembly')->exists())->toBeTrue();
    });

    it('validates name is required on update', function () {
        test()->actingAs(test()->admin);
        $disassembly = Disassembly::factory()->create();
        $component = Livewire::test(EditDisassembly::class, ['record' => $disassembly->getRouteKey()]);
        $component->fillForm(['name' => ''])->call('save');
        $component->assertHasFormErrors(['name' => 'required']);
    });

    it('validates product_id is required on create', function () {
        test()->actingAs(test()->admin);

        Livewire::test(CreateDisassembly::class)
            ->fillForm([
                'name' => 'Test Disassembly',
                'product_id' => null,
                'main_image' => ['disasm.jpg'],
            ])
            ->call('create')
            ->assertHasFormErrors(['product_id' => 'required']);
    });

    it('validates main_image is required on create', function () {
        test()->actingAs(test()->admin);
        $product = Product::factory()->create();

        Livewire::test(CreateDisassembly::class)
            ->fillForm([
                'name' => 'Test Disassembly',
                'product_id' => $product->id,
                'main_image' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['main_image' => 'required']);
    });

    it('admin can access edit disassembly page', function () {
        test()->actingAs(test()->admin);
        $disassembly = Disassembly::factory()->create();

        Livewire::test(EditDisassembly::class, ['record' => $disassembly->getRouteKey()])
            ->assertStatus(200);
    });

    it('can update disassembly via form', function () {
        test()->actingAs(test()->admin);
        $disassembly = Disassembly::factory()->create(['name' => 'Old Name']);

        $component = Livewire::test(EditDisassembly::class, ['record' => $disassembly->getRouteKey()]);
        $component->fillForm(['name' => 'Updated Name'])->call('save');
        expect(Disassembly::find($disassembly->id)->name)->toBe('Updated Name');
    });


    it('disassembly resource has correct navigation group', function () {
        $group = \App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource::getNavigationGroup();
        expect($group)->toBe(__('Products'));
    });

    it('disassembly resource has correct model label', function () {
        $label = \App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource::getModelLabel();
        expect($label)->toBe(__('Disassembly'));
    });

    it('resource has index page', function () {
        $pages = \App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource::getPages();
        expect($pages)->toHaveKey('index');
    });

    it('resource has create page', function () {
        $pages = \App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource::getPages();
        expect($pages)->toHaveKey('create');
    });

    it('resource has edit page', function () {
        $pages = \App\Filament\Admin\Resources\Products\Disassemblies\DisassemblyResource::getPages();
        expect($pages)->toHaveKey('edit');
    });

    it('can import disassemblies from CSV via table action', function () {
        Storage::fake('local');
        test()->actingAs(test()->admin);
        $product = Product::factory()->create();

        // Create a fake CSV file with correct headers and data matching DisassemblyImporter
        $csvContent = "name,main_image,product\nImported Disassembly 1,disasm1.jpg,{$product->id}\nImported Disassembly 2,disasm2.jpg,{$product->id}\n";
        $fileOnDisk = UploadedFile::fake()->createWithContent('d.csv', $csvContent);

        // Test the import action through Livewire
        Livewire::test(ListDisassemblies::class)
            ->mountTableAction('import')
            ->setTableActionData([
                'file' => $fileOnDisk,
            ])->callMountedTableAction()
            ->assertHasNoTableActionErrors();
        // Assert imported disassemblies exist in DB
        expect(Disassembly::where('name', 'Imported Disassembly 1')->where('main_image', 'disasm1.jpg')->where('product_id', $product->id)->exists())->toBeTrue();
        expect(Disassembly::where('name', 'Imported Disassembly 2')->where('main_image', 'disasm2.jpg')->where('product_id', $product->id)->exists())->toBeTrue();
    });
});
