<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SeoTags;
use App\Factories\BreadCrumbs\StandardPageBreadCrumbs;
use App\Models\Category;
use App\Repositories\Database\Categories\CategoryRepositoryInterface;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $repository
    ) {}

    public function index(): View
    {
        return view('pages.categories', [
            'categories' => $this->repository->getAll(),
            // 'seotags' => new SeoTags('categories'),
            'breadcrumbs' => new StandardPageBreadCrumbs([
                __('Categories') => route('category-list'),
            ]),
        ]);
    }

    public function category(Category $category): View
    {
        return view('pages.category', [
            'category' => $category,
            'products' => $this->repository->getProducts($category),
            // 'seotags' => new SeoTags($category),
            'breadcrumbs' => new StandardPageBreadCrumbs([
                __('Categories') => route('category-list'),
                __($category->name) => $category->slug,
            ]),
        ]);
    }
}
