<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class CategoryGrid extends Component
{
    /**
     * @param  Collection<int, Category>  $categories
     */
    public function __construct(public Collection $categories) {}

    public function render(): View
    {
        return view('components.category-grid');
    }
}
