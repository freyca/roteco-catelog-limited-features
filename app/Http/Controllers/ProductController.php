<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\SeoTags;
use App\Enums\Role;
use App\Factories\BreadCrumbs\ProductBreadCrumbs;
use App\Factories\BreadCrumbs\StandardPageBreadCrumbs;
use App\Models\Product;
use App\Models\ProductComplement;
use App\Models\ProductFeature;
use App\Models\ProductFeatureValue;
use App\Models\ProductSparePart;
use App\Models\ProductVariant;
use App\Models\User;
use App\Repositories\Database\Product\Product\ProductRepositoryInterface;
use App\Repositories\Database\Product\ProductComplement\ProductComplementRepositoryInterface;
use App\Repositories\Database\Product\ProductSparePart\ProductSparePartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ProductComplementRepositoryInterface $productComplementRepository,
        private ProductSparePartRepositoryInterface $productSparePartRepository,
    ) {}

    /**
     * Products
     */
    public function all(): View
    {
        return view('pages.products', [
            'seotags' => new SeoTags('product_all'),
            'breadcrumbs' => new StandardPageBreadCrumbs([
                __('Products') => route('product-list'), // @phpstan-ignore-line
            ]),
        ]);
    }

    public function product(Product $product): View
    {
        if (! $product->published && ! $this->canAccessPrivateProducts()) {
            abort(403);
        }

        $variants = $product->productVariants()->get();
        if ($variants->count() !== 0) {
            /**
             * @var ProductVariant
             */
            $first_variant = $variants->first();
        }

        /**
         * @var Collection<int, ProductFeature>
         */
        $features = $product->productFeatures();

        /**
         * @var Collection<int, ProductFeatureValue>
         */
        $featureValues = $product->productFeatureValues;

        // $relatedComplements = $product->productComplements()->limit(5)->get();
        // $relatedSpareparts = $product->productSpareParts()->get(); // ->limit(5)->get();
        $relatedDisassemblies = $product->disassemblies;

        return view(
            'pages.product',
            [
                'product' => $product,
                'variants' => $variants,
                'features' => ($variants->count() === 0) ? $features : $features->merge($first_variant->productFeatures())->unique(),
                'featureValues' => ($variants->count() === 0) ? $featureValues : $featureValues->merge($first_variant->productFeatureValues()->get())->unique(),
                // 'featuredProducts' => $relatedComplements->concat($relatedSpareparts),
                'relatedDisassemblies' => $relatedDisassemblies,
                // 'seotags' => new SeoTags($product),
                'breadcrumbs' => new ProductBreadCrumbs($product),
                // TODO: difefference between related (other similar products, suitable components or spare parts)
                // and featured (products we want to sell)
            ]
        );
    }

    /**
     * Complements
     */
    // public function complements(): View
    // {
    //    return view('pages.complements', [
    //        'seotags' => new SeoTags('complements_all'),
    //        'breadcrumbs' => new StandardPageBreadCrumbs([
    //            __('Complements') => route('complement-list'), // @phpstan-ignore-line
    //        ]),
    //    ]);
    // }

    // public function productComplement(ProductComplement $productComplement): View
    // {
    //    if (! $productComplement->published && ! $this->canAccessPrivateProducts()) {
    //        abort(403);
    //    }
    //
    //    return view('pages.product', [
    //        'product' => $productComplement,
    //        'features' => $productComplement->productFeatures(),
    //        'featureValues' => $productComplement->productFeatureValues,
    //        'featuredProducts' => $productComplement->products()->limit(5)->get(),
    //        'seotags' => new SeoTags($productComplement),
    //        'breadcrumbs' => new ProductBreadCrumbs($productComplement),
    //    ]);
    // }

    /**
     * Spare parts
     */
    // public function spareParts(): View
    // {
    //    return view('pages.spare-parts', [
    //        'seotags' => new SeoTags('spare_parts_all'),
    //        'breadcrumbs' => new StandardPageBreadCrumbs([
    //            __('Spare parts') => route('spare-part-list'), // @phpstan-ignore-line
    //        ]),
    //    ]);
    // }

    // public function ProductSparePart(ProductSparePart $productSparePart): View
    // {
    //    if (! $productSparePart->published && ! $this->canAccessPrivateProducts()) {
    //        abort(403);
    //    }
    //
    //    return view('pages.product', [
    //        'product' => $productSparePart,
    //        'features' => $productSparePart->productFeatures(),
    //        'featureValues' => $productSparePart->productFeatureValues,
    //        'featuredProducts' => $productSparePart->products()->limit(5)->get(),
    //        'seotags' => new SeoTags($productSparePart),
    //        'breadcrumbs' => new ProductBreadCrumbs($productSparePart),
    //    ]);
    // }

    private function canAccessPrivateProducts(): bool
    {
        /** @var ?User */
        $user = Auth::user();

        return match (true) {
            $user === null => false,
            $user->role !== Role::Admin => false,
            default => true
        };
    }
}
