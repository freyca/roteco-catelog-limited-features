<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SeoTags;
use App\Repositories\Database\Categories\CategoryRepositoryInterface;
use App\Repositories\Database\Product\Product\ProductRepositoryInterface;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function index(): View
    {
        return view('pages.index', [
            'categories' => $this->categoryRepository->featured(),
            'products' => $this->productRepository->featured(),
            'seotags' => new SeoTags('index'),
        ]);
    }
}
