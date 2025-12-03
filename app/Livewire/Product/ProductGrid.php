<?php

declare(strict_types=1);

namespace App\Livewire\Product;

use App\DTO\FilterDTO;
use App\Models\ProductComplement;
use App\Models\ProductSparePart;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class ProductGrid extends Component
{
    use WithoutUrlPagination, WithPagination;

    private LengthAwarePaginator $products;

    /**
     * Used only for comparison, do not touch it
     *
     * @var array{'min_price': int, 'max_price': int, 'filtered_features': array<int>, 'filtered_category': int}
     */
    public array $default_filters = [
        'filtered_category' => 0,
        'min_price' => 0,
        'max_price' => 10000,
        'filtered_features' => [],
    ];

    /**
     * @var array{'min_price': int, 'max_price': int, 'filtered_features': array<int>, 'filtered_category': int}
     */
    public array $filters = [
        'filtered_category' => 0,
        'min_price' => 0,
        'max_price' => 10000,
        'filtered_features' => [],
    ];

    public string $class_filter;

    public function mount(string $class_name): void
    {
        $basename = 'App\Repositories\Database\Product';

        $this->class_filter =
            match ($class_name) {
                ProductComplement::class => $basename.'\ProductComplement\EloquentProductComplementRepository',
                ProductSparePart::class => $basename.'\ProductSparePart\EloquentProductSparePartRepository',
                default => $basename.'\Product\EloquentProductRepository',
            };
    }

    /**
     * @param  array{'min_price': int, 'max_price': int, 'filtered_features': array<int>, 'filtered_category': int}  $filters
     */
    #[On('refreshProductGrid')]
    public function getFilteredProducts(array $filters): void
    {
        // If no filters has been set, return all products
        if ($filters === $this->default_filters) {
            $repository = app($this->class_filter);

            $this->products = $repository->getAll();
        }

        // If filters change, we reset url pagination and save them
        // Need to save them so pagination does not breaks filters
        if ($filters !== $this->filters) {
            $this->filters = $filters;
            $this->resetPage();
        }

        $filter_dto = new FilterDTO;

        // Database has prices in cents
        $filter_dto->minPrice(intval($filters['min_price']) * 100);
        $filter_dto->maxPrice(intval($filters['max_price']) * 100);
        $filter_dto->category($filters['filtered_category']);
        $filter_dto->features($filters['filtered_features']);

        $repository = app($this->class_filter);
        // $this->products = $repository->filter($filter_dto);
        $this->products = $repository->getAll();
    }

    public function render(): View
    {
        if (! isset($this->products)) {
            $this->getFilteredProducts($this->default_filters);
        }

        return view(
            'livewire.product.product-grid',
            [
                'products' => $this->products,
            ]
        );
    }
}
