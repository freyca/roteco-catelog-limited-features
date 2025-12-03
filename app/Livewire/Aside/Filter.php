<?php

declare(strict_types=1);

namespace App\Livewire\Aside;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Filter extends Component
{
    /**
     * @var array<string, bool>
     */
    public array $enabled_filters = [
        'price' => false,
        'category' => false,
        'features' => false,
    ];

    public int $filtered_category = 0;

    public int $min_price = 0;

    public int $max_price = 10000;

    /**
     * @var array<int>
     */
    public array $filtered_features = [];

    public bool $is_hidden = true;

    #[On('toggleFilterBar')]
    public function toggleFilterBar(): void
    {
        $this->is_hidden = ! $this->is_hidden;
    }

    public function filterProducts(): void
    {
        $filters = [
            'filtered_category' => $this->filtered_category,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'filtered_features' => $this->filtered_features,
        ];

        $this->dispatch('refreshProductGrid', $filters);
    }

    public function clearFilters(): void
    {
        $this->filtered_category = 0;
        $this->min_price = 0;
        $this->max_price = 10000;
        $this->filtered_features = [];

        $this->filterProducts();
    }

    public function mount(array $filters): void
    {
        $this->enabled_filters = array_merge($this->enabled_filters, $filters);
    }

    public function render(): View
    {
        return view('livewire.aside.filter');
    }
}
