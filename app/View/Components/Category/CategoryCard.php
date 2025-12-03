<?php

declare(strict_types=1);

namespace App\View\Components\Category;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryCard extends Component
{
    public function __construct(public Category $category) {}

    public function render(): View|Closure|string
    {
        return view('components.category.category-card');
    }
}
