<?php

declare(strict_types=1);

namespace App\Factories\BreadCrumbs;

use App\Models\BaseProduct;
use App\Models\Product;
use App\Models\ProductSparePart;
use Exception;

class ProductBreadCrumbs extends StandardPageBreadCrumbs
{
    public function __construct(BaseProduct $product)
    {
        parent::setDefaultBreadCrumb();

        $bread_crumbs = match (true) {
            is_a($product, ProductSparePart::class) => $this->productSparePartBreadCrumb(),
            is_a($product, Product::class) => $this->productBreadCrumb($product),
            default => throw new Exception('Invalid class type'),
        };

        $bread_crumbs = array_merge($bread_crumbs, [$product->name => $product->slug]);

        $this->bread_crumbs = array_merge($this->default_bread_crumb, $bread_crumbs);
    }

    private function productBreadCrumb(Product $product): array
    {
        return [
            $product->category?->name => '/'.$product->category?->slug,
        ];
    }

    private function productSparePartBreadCrumb(): array
    {
        return [
            __('Spare parts') => route('spare-part-list'),
        ];
    }
}
