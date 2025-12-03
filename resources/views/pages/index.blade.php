<x-layouts.app :seotags="$seotags">
    <!-- <x-sliders.main-slider /> -->

    <div class="container rounded-top-md">
        <div class="container mx-auto">
            <h2 class="flex text-3xl font-bold mt-10 mx-auto justify-left text-primary-800 ml-4">
                {{ __('Categories') }}
            </h2>

            <x-category-grid :categories="$categories" />
        </div>

        <div class="container mx-auto">
            <h2 class="flex text-3xl font-bold mt-10 mx-auto justify-left text-primary-800 ml-4">
                {{ __('Featured products') }}
            </h2>

            <div class="main-content transition-all duration-500 ease-in-out px-4 w-auto">
                <x-product-grid :products="$products" />
            </div>

        </div>

        <x-buttons.whats-app-button />
    </div>
</x-layouts.app>
