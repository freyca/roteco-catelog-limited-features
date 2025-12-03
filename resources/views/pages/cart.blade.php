<x-layouts.app :seotags="$seotags">
    @inject('cart', 'App\Services\Cart')

    <x-bread-crumbs :breadcrumbs="$breadcrumbs" />

    <div class="mx-auto px-0 xl:px-4 my-4">
        @inject('cart', 'App\Services\Cart')

        <div>
            <h2 class="mx-auto text-3xl text-center font-semibold text-primary-900 sm:text-2xl">
                @if ($cart->isEmpty())
                    {{ __('Shopping Cart') }}
                @else
                    {{ __('Order summary') }}
                @endif
            </h2>

            <div class="mt-6 sm:mt-8 md:gap-6 xl:gap-4">
                @if ($cart->isEmpty())
                    <x-cart.empty-cart />
                @else
                    <div class="my-2 space-y-4 rounded-lg border border-primary-200 bg-white p-4 shadow-sm">
                        @livewire('cart.cart-product-container')
                    </div>

                    <div class="my-10 space-y-4 rounded-lg border border-primary-200 bg-white p-4 shadow-sm">
                        @livewire('forms.checkout-form')
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>
