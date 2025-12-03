<x-layouts.app :seotags="new App\DTO\SeoTags('noindex')">
    <section>
        <div class="container mx-auto p-4">
            <h1 class="text-3xl text-center font-bold mb-4">
                OOOOOPS, parece que estás intentando acceder a una página prohibida
            </h1>

            <h2 class="text-xl text-center mb-2">
                Quizá te interesen estos productos
            </h2>

            @php
                $productRepository = app(App\Repositories\Database\Product\Product\ProductRepositoryInterface::class);
                $featured_products = $productRepository->featured();
            @endphp

            <div class="container mx-auto py-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @foreach ($featured_products as $product)
                            <x-product.product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
