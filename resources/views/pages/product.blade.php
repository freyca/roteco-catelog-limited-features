<x-layouts.app> {{-- :seotags="$seotags"> --}}
    @inject(cart, '\App\Services\Cart')

    <x-bread-crumbs :breadcrumbs="$breadcrumbs" />

    <div class="mx-4 my-4">
        <h1 class="text-3xl font-bold mt-16 mb-10 text-center">{{ $product->name }}</h1>
        <h2 class="mb-4">{{ $product->slogan }}</h2>

        <div class="grid gap-4 md:gap-14 lg:grid-cols-1 xl:grid-cols-2">
            <x-product.product-image-gallery :image="$product->main_image" />

            <div class="text-primary-700 text-justify">
                <x-disassembly-list :relatedDisassemblies="$relatedDisassemblies" />
                {{--
                <div id="product-short-description" class="mb-4">
                    {!! $product->short_description !!}
                </div>

                @livewire('buttons.product-cart-buttons', ['product' => $product, 'variants' => isset($variants) ? $variants : collect() ])

                <x-product.payment-banners />
                --}}
            </div>
        </div>

        {{--
        <div class="container mx-auto my-6">
            <div class="flex justify-center items-center">
                <h3 class="text-center my-6 bg-primary-800 p-4 rounded-xl max-w-2xl">
                    <span class="font-bold text-lg text-primary-100">
                        {{ mb_strtoupper( __('Extended description of') . ' ' . $product->name) }}
                    </span>
                </h3>
            </div>

            <div id="product-long-description" class="text-primary-700 text-justify">
                {!! $product->description !!}
            </div>
        </div>

        @if(isset($featuredProducts) && $featuredProducts->count() > 0)
            <div class="flex justify-center items-center">
                <p class="text-center my-6 bg-primary-800 p-4 rounded-xl max-w-2xl">
                    <span class="font-bold text-lg text-primary-100">
                        {{ mb_strtoupper( __('Featured products') )}}
                    </span>
                </p>
            </div>

            <x-product-grid :products="$featuredProducts" />
        @endif
        --}}
    </div>

    <x-buttons.whats-app-button />
</x-layouts.app>
