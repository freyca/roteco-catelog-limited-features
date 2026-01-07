<?php

declare(strict_types=1);

namespace App\Livewire\Product;

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

    public function mount(): void
    {
        $this->class_filter = 'App\Repositories\Database\Product\Product\EloquentProductRepository';
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

        $repository = app($this->class_filter);
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
