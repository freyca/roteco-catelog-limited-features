<?php

use App\Enums\Role;
use App\Filament\Admin\Resources\Features\Categories\Pages\CreateCategory;
use App\Filament\Admin\Resources\Features\Categories\Pages\EditCategory;
use App\Filament\Admin\Resources\Features\Categories\Pages\ListCategories;
use App\Models\Category;
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

describe('CategoryResource', function () {
    it('admin can access category list page', function () {
        test()->actingAs(test()->admin);

        Livewire::test(ListCategories::class)
            ->assertStatus(200);
    });

    it('can display categories in list table', function () {
        test()->actingAs(test()->admin);
        $categories = Category::factory(3)->create();

        $component = Livewire::test(ListCategories::class);

        foreach ($categories as $category) {
            $component->assertSee($category->name);
        }
    });

    it('admin can access create category page', function () {
        test()->actingAs(test()->admin);

        Livewire::test(CreateCategory::class)
            ->assertStatus(200);
    });

    it('can create a new category via form', function () {
        test()->actingAs(test()->admin);
        Storage::fake('local');
        $initialCount = Category::count();

        $file = UploadedFile::fake()->image('test-image.jpg');

        $component = Livewire::test(CreateCategory::class);
        $component->set('data.name', 'New Electronics');
        $component->set('data.big_image', $file);
        $component->call('create');

        expect(Category::count())->toBeGreaterThan($initialCount);
        expect(Category::where('name', 'New Electronics')->exists())->toBeTrue();
    });

    it('validates name is required on create', function () {
        test()->actingAs(test()->admin);
        $file = UploadedFile::fake()->image('test.jpg');
        $component = Livewire::test(CreateCategory::class);
        $component->set('data.name', '');
        $component->set('data.big_image', $file);
        $component->call('create');
        $component->assertHasFormErrors(['name' => 'required']);
    });

    it('validates big_image is required on create', function () {
        test()->actingAs(test()->admin);
        $component = Livewire::test(CreateCategory::class);
        $component->set('data.name', 'Test Category');
        $component->set('data.big_image', null);
        $component->call('create');
        $component->assertHasFormErrors(['big_image' => 'required']);
    });

    it('admin can access edit category page', function () {
        test()->actingAs(test()->admin);
        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->assertStatus(200);
    });

    it('can update category via form', function () {
        test()->actingAs(test()->admin);
        $category = Category::factory()->create(['name' => 'Old Name']);

        $component = Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()]);
        $component->set('data.name', 'Updated Name');
        $component->call('save');
        expect(Category::find($category->id)->name)->toBe('Updated Name');
    });

    it('validates name is required on update', function () {
        test()->actingAs(test()->admin);
        $category = Category::factory()->create();

        $component = Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()]);
        $component->set('data.name', '');
        $component->call('save');
        $component->assertHasFormErrors(['name' => 'required']);
    });

    it('can delete category via delete action', function () {
        test()->actingAs(test()->admin);
        $category = Category::factory()->create(['name' => 'To Delete']);
        $categoryId = $category->id;

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->callAction('delete');

        expect(Category::find($categoryId))->toBeNull();
    });

    it('category resource has correct navigation group', function () {
        $group = \App\Filament\Admin\Resources\Features\Categories\CategoryResource::getNavigationGroup();
        expect($group)->toBe(__('Features'));
    });

    it('category resource has correct model label', function () {
        $label = \App\Filament\Admin\Resources\Features\Categories\CategoryResource::getModelLabel();
        expect($label)->toBe(__('Categories'));
    });

    it('resource has index page', function () {
        $pages = \App\Filament\Admin\Resources\Features\Categories\CategoryResource::getPages();
        expect($pages)->toHaveKey('index');
    });

    it('resource has create page', function () {
        $pages = \App\Filament\Admin\Resources\Features\Categories\CategoryResource::getPages();
        expect($pages)->toHaveKey('create');
    });

    it('resource has edit page', function () {
        $pages = \App\Filament\Admin\Resources\Features\Categories\CategoryResource::getPages();
        expect($pages)->toHaveKey('edit');
    });

    it('can import categories from CSV via Livewire action', function () {
        Storage::fake('local');
        test()->actingAs(test()->admin);

        // Create a fake CSV file with correct headers and data
        $csvContent = "name,slug,big_image\nImported Electronics,imported-electronics,electronics.jpg\nImported Clothing,imported-clothing,clothing.jpg\n";
        $fileOnDisk = UploadedFile::fake()->createWithContent('cat.csv', $csvContent);

        // Test the import action through Livewire
        Livewire::test(ListCategories::class)
            ->mountTableAction('import')
            ->setTableActionData([
                'file' => $fileOnDisk,
            ])->callMountedTableAction()
            ->assertHasNoTableActionErrors();
    });
});
