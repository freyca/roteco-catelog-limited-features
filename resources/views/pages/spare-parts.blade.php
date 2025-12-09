<x-layouts.app :seotags="$seotags">

    <x-bread-crumbs :breadcrumbs="$breadcrumbs" />

    <div class="main-content transition-all duration-500 ease-in-out px-4 w-auto">
        @livewire('product.product-grid', key(md5('product.product-grid')), ['class_name' => \App\Models\ProductSparePart::class])
    </div>

</x-layouts.app>
